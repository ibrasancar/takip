<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderMeta extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'order_id'      => ['type' => 'INT', 'constraint' => 11],
            'order_title'   => ['type' => 'VARCHAR', 'constraint' => '200'],
            'order_value'   => ['type' => 'TEXT'],
            'created_at'    => ['type' => 'DATETIME'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('order_meta');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('order_meta');
    }
}
