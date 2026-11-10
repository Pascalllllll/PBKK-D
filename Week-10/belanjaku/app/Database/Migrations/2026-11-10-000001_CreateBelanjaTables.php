<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBelanjaTables extends Migration
{
    private function id(): array
    {
        return ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true];
    }

    private function fk(bool $null = false): array
    {
        return ['type' => 'INT', 'unsigned' => true, 'null' => $null];
    }

    private function timestamps(): array
    {
        return [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];
    }

    public function up()
    {
        $this->forge->addField([
            'id'       => $this->id(),
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'     => ['type' => 'ENUM', 'constraint' => ['admin', 'gudang', 'customer'], 'default' => 'customer'],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        $this->forge->addField([
            'id'        => $this->id(),
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'slug'      => ['type' => 'VARCHAR', 'constraint' => 70],
            'deskripsi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nama');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('kategori');

        $this->forge->addField([
            'id'          => $this->id(),
            'kategori_id' => $this->fk(),
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 140],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'harga'       => ['type' => 'INT', 'unsigned' => true],
            'stok'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'berat'       => ['type' => 'INT', 'unsigned' => true, 'default' => 500, 'comment' => 'gram'],
            'gambar'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'aktif'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['aktif', 'kategori_id']);
        // RESTRICT: kategori yang masih punya produk tidak bisa dihapus
        $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('produk');

        // Buku alamat pelanggan
        $this->forge->addField([
            'id'       => $this->id(),
            'user_id'  => $this->fk(),
            'label'    => ['type' => 'VARCHAR', 'constraint' => 30],
            'penerima' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'kota'     => ['type' => 'VARCHAR', 'constraint' => 60],
            'kode_pos' => ['type' => 'CHAR', 'constraint' => 5],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('alamat');

        // Keranjang disimpan di database, satu baris per (user, produk)
        $this->forge->addField([
            'id'        => $this->id(),
            'user_id'   => $this->fk(),
            'produk_id' => $this->fk(),
            'qty'       => ['type' => 'INT', 'unsigned' => true],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['user_id', 'produk_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('keranjang');

        // Alamat dan harga disalin saat checkout (snapshot)
        $this->forge->addField([
            'id'       => $this->id(),
            'kode'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'user_id'  => $this->fk(),
            'penerima' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'   => ['type' => 'TEXT'],
            'subtotal' => ['type' => 'BIGINT', 'unsigned' => true],
            'ongkir'   => ['type' => 'INT', 'unsigned' => true],
            'total'    => ['type' => 'BIGINT', 'unsigned' => true],
            'catatan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'   => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_bayar', 'menunggu_verifikasi', 'diproses', 'dikirim', 'selesai', 'batal'],
                'default'    => 'menunggu_bayar',
            ],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('pesanan');

        $this->forge->addField([
            'id'          => $this->id(),
            'pesanan_id'  => $this->fk(),
            'produk_id'   => $this->fk(),
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 120],
            'harga'       => ['type' => 'INT', 'unsigned' => true],
            'qty'         => ['type' => 'INT', 'unsigned' => true],
            'subtotal'    => ['type' => 'BIGINT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        // RESTRICT: produk yang pernah dipesan tidak bisa dihapus (cukup dinonaktifkan)
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('pesanan_item');

        // Satu pesanan punya satu data pembayaran (transfer manual + bukti)
        $this->forge->addField([
            'id'               => $this->id(),
            'pesanan_id'       => $this->fk(),
            'jumlah'           => ['type' => 'BIGINT', 'unsigned' => true],
            'bank_pengirim'    => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'nama_rekening'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bukti'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['belum', 'menunggu', 'diterima', 'ditolak'], 'default' => 'belum'],
            'alasan_tolak'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'diunggah_at'      => ['type' => 'DATETIME', 'null' => true],
            'diverifikasi_oleh' => $this->fk(true),
            'diverifikasi_at'  => ['type' => 'DATETIME', 'null' => true],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('pesanan_id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('diverifikasi_oleh', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('pembayaran');

        $this->forge->addField([
            'id'          => $this->id(),
            'pesanan_id'  => $this->fk(),
            'kurir'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'no_resi'     => ['type' => 'VARCHAR', 'constraint' => 40],
            'diinput_oleh' => $this->fk(true),
            'dikirim_at'  => ['type' => 'DATETIME', 'null' => true],
            'diterima_at' => ['type' => 'DATETIME', 'null' => true],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('pesanan_id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('diinput_oleh', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('pengiriman');
    }

    public function down()
    {
        foreach (['pengiriman', 'pembayaran', 'pesanan_item', 'pesanan', 'keranjang', 'alamat', 'produk', 'kategori', 'users'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
