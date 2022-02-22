<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductCategories extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name'     => ['type' => 'VARCHAR', 'constraint' => '200'],
            'created_at'        => ['type' => 'DATETIME'],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('product_categories');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('product_categories');
    }
}
