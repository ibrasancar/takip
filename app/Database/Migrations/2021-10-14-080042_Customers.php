<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customers extends Migration
{
    public function up()
    {
        //
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => '200'],
            'birthday'      => ['type' => 'DATE', 'null' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => true],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => true],
            'address'       => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => true],
            'salesman_id'   => ['type' => 'INT', 'constraint' => '11', 'null' => true],
            'created_at'    => ['type' => 'DATETIME'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable(('customers'));
    }
}
