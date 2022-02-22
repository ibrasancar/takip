<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserMeta extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT'],
            'meta_title'    => ['type' => 'VARCHAR', 'constraint' => '200'],
            'meta_value'    => ['type' => 'TEXT'],
            'created_at'    => ['type' => 'DATETIME'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_meta');
        $this->db->disableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('user_meta');
    }
}
