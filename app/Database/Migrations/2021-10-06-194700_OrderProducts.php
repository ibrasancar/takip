<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderProducts extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'order_product_id'      => ['type' => 'INT'],
            'quantity'              => ['type' => 'INT'],
            'product_id'            => ['type' => 'INT'],
            'status'                => ['type' => 'VARCHAR', 'constraint' => '20'],
            'price'                 => ['type' => 'DECIMAL(10,2)', 'null' => true, 'default' => 0.00],
            'estimated_delivery'    => ['type' => 'DATE'],
            'shipping_date'         => ['type' => 'DATETIME', 'null' => true],
            'deliver_confirm'       => ['type' => 'DATETIME', 'null' => true],
            'extras'                => ['type' => 'TEXT', 'null' => true],
            'created_at'            => ['type' => 'DATETIME'],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
        $this->forge->createTable('order_products');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('order_products');
    }
}
