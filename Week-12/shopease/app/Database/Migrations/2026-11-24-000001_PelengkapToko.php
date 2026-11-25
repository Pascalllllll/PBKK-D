<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Week 12: pengaturan toko, alamat utama, harga di keranjang, kategori aktif, produk unggulan, riwayat status. */
class PelengkapToko extends Migration
{
    public function up()
    {
        // Pengaturan toko dalam bentuk kunci-nilai, jadi menambah pengaturan baru tidak perlu migration lagi
        $this->forge->addField([
            'kunci'      => ['type' => 'VARCHAR', 'constraint' => 40],
            'nilai'      => ['type' => 'TEXT', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('kunci');
        $this->forge->createTable('pengaturan');

        $this->forge->addColumn('alamat', ['utama' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'kode_pos']]);
        // Harga saat produk dimasukkan ke keranjang, untuk mendeteksi perubahan harga sebelum checkout
        $this->forge->addColumn('keranjang', ['harga' => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'qty']]);
        $this->forge->addColumn('kategori', ['aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'deskripsi']]);
        $this->forge->addColumn('produk', ['unggulan' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'aktif']]);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pesanan_id' => ['type' => 'INT', 'unsigned' => true],
            'dari'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'ke'         => ['type' => 'VARCHAR', 'constraint' => 30],
            'oleh'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'catatan'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('pesanan_id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('oleh', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pesanan_riwayat');
    }

    public function down()
    {
        $this->forge->dropTable('pesanan_riwayat', true);
        $this->forge->dropColumn('produk', 'unggulan');
        $this->forge->dropColumn('kategori', 'aktif');
        $this->forge->dropColumn('keranjang', 'harga');
        $this->forge->dropColumn('alamat', 'utama');
        $this->forge->dropTable('pengaturan', true);
    }
}
