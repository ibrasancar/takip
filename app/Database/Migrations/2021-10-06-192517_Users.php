<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            // 'level'            => ['type' => 'SMALLINT'],
            'user_type'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'full_name'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 200],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'address'          => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true,],
            'password'         => ['type' => 'VARCHAR', 'constraint' => 255],

            // 'reset_hash'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'reset_at'         => ['type' => 'datetime', 'null' => true],
            // 'reset_expires'    => ['type' => 'datetime', 'null' => true],

            // 'activate_hash'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'status'           => ['type' => 'varchar', 'constraint' => 255, 'null' => true],

            // 'status_message'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            // 'active'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            // 'force_pass_reset' =>x ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],

            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('users');
    }
}
