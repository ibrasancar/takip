<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tickets extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'order_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'title'         => ['type' => 'VARCHAR', 'null' => true, 'constraint' => '200'],
            'message'       => ['type' => 'TEXT', 'null' => true],
            'estimated_solve' => ['type' => 'DATETIME', 'null' => true],
            'solved_at'     => ['type' => 'DATETIME', 'null' => true],
            'confirm'       => ['type' => 'DATETIME', 'null' => true],
            'technic_id'    => ['type' => 'INT', 'constraint' => 11],
            'salesman_id'    => ['type' => 'INT', 'constraint' => 11],
            'created_at'    => ['type' => 'DATETIME'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('order_id', 'orders', 'id', '', 'SET NULL');
        // $this->forge->addForeignKey('technic_id', 'users', 'id', '', 'SET NULL');
        $this->forge->createTable('tickets');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('tickets');
    }
}
