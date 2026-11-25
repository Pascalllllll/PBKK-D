<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Lanjutan dari Week 10: tabel users sudah ada, jadi kolom baru ditambahkan lewat migration baru,
 * bukan dengan mengubah migration lama.
 */
class UpgradeAutentikasi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'aktif'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'role'],
            // Dinaikkan saat sandi diganti atau admin mengeluarkan akun: semua session lama jadi tidak berlaku
            'versi_sesi'     => ['type' => 'INT', 'unsigned' => true, 'default' => 1, 'after' => 'aktif'],
            'login_terakhir' => ['type' => 'DATETIME', 'null' => true, 'after' => 'versi_sesi'],
        ]);

        // Token "Ingat saya". Cookie berisi selector:validator, database hanya menyimpan hash validator.
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'selector'       => ['type' => 'CHAR', 'constraint' => 24],
            'validator_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'perangkat'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'kedaluwarsa'    => ['type' => 'DATETIME'],
            'dipakai_pada'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('selector');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('token_ingat');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'hasil'      => ['type' => 'ENUM', 'constraint' => ['berhasil', 'ingat', 'gagal', 'dibekukan', 'dibatasi']],
            'ip'         => ['type' => 'VARCHAR', 'constraint' => 45],
            'perangkat'  => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('log_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('log_masuk', true);
        $this->forge->dropTable('token_ingat', true);
        $this->forge->dropColumn('users', ['aktif', 'versi_sesi', 'login_terakhir']);
    }
}
