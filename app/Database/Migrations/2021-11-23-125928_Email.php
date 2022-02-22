<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Email extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'user_type'     => ['type' => 'VARCHAR', 'constraint' => '50'],
            'user_id'       => ['type' => 'INT', "constraint" => 11],
            'email'         => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => true],
            'message'       => ['type' => 'TEXT', 'null' => true],
            'sended_at'     => ['type' => 'DATETIME'],
            'status'        => ['type' => 'VARCHAR', 'constraint' => '50']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('email');
    }

    public function down()
    {
        $this->forge->dropTable('email');
    }
}
