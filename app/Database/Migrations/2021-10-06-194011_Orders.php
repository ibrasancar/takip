<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Orders extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => '100'],
            'status'            => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => true],
            'customer_id'       => ['type' => 'INT', 'null' => true],
            'products'          => ['type' => 'TEXT', 'null' => true],
            'total_price'       => ['type' => 'DECIMAL(10,2)', 'null' => true, 'default' => 0.00],
            'deposit'           => ['type' => 'DECIMAL(10,2)', 'null' => true, 'default' => 0.00],
            'discount'          => ['type' => 'DECIMAL(10,2)', 'null' => true, 'default' => 0.00],
            'salesman_id'       => ['type' => 'INT', 'null' => true],
            'admin_confirm'     => ['type' => 'DATETIME', 'null' => true],
            'customer_confirm'  => ['type' => 'DATETIME', 'null' => true],
            'order_note'        => ['type' => 'TEXT', 'null' => true],
            'completed_at'      => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', '', 'SET NULL');
        $this->forge->addForeignKey('salesman_id', 'users', 'id', '', 'SET NULL');
        $this->forge->createTable('orders');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        // $this->forge->dropForeignKey('customer_id', 'customers');
        // $this->forge->dropForeignKey('salesman_id', 'id');
        $this->forge->dropTable('orders');
    }
}
