<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTokoTables extends Migration
{
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
            'id'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'role'     => ['type' => 'ENUM', 'constraint' => ['admin', 'customer'], 'default' => 'customer'],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        $this->forge->addField([
            'id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 50],
            'ikon' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'fa-box'],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nama');
        $this->forge->createTable('kategori');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kategori_id' => ['type' => 'INT', 'unsigned' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'harga'       => ['type' => 'INT', 'unsigned' => true],
            'stok'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'gambar'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        // addForeignKey(field, table, column, onUpdate, onDelete)
        // RESTRICT: kategori yang masih punya produk tidak bisa dihapus
        $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('produk');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true],
            'total'         => ['type' => 'BIGINT', 'unsigned' => true],
            'nama_penerima' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'        => ['type' => 'TEXT'],
            'status'        => ['type' => 'ENUM', 'constraint' => ['pending', 'dibayar', 'dikirim', 'selesai', 'batal'], 'default' => 'pending'],
        ] + $this->timestamps());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders');

        // Harga disalin saat checkout (snapshot), jadi perubahan harga produk tidak mengubah pesanan lama
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'  => ['type' => 'INT', 'unsigned' => true],
            'produk_id' => ['type' => 'INT', 'unsigned' => true],
            'qty'       => ['type' => 'INT', 'unsigned' => true],
            'harga'     => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        // RESTRICT: produk yang pernah dipesan tidak bisa dihapus
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('order_items');
    }

    public function down()
    {
        foreach (['order_items', 'orders', 'produk', 'kategori', 'users'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
