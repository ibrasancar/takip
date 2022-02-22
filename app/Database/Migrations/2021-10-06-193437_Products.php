<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'category'          => ['type' => 'INT', 'null' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => '200'],
            'price'             => ['type' => 'DECIMAL(10,2)', 'null' => true, 'default' => 0.00],
            'description'       => ['type' => 'TEXT', 'null' => true],
            'image'             => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => true],
            'manufacturer_id'   => ['type' => 'INT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('manufacturer_id', 'manufacturers', 'id', '', 'SET NULL');
        $this->forge->addForeignKey('category', 'product_categories', 'id', '', 'SET NULL');
        $this->forge->createTable('products');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('products');
    }
}
