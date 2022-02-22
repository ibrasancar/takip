<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Announcements extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'type'          => ['type' => 'ENUM', 'constraint' => ['sms', 'email'], 'default' => 'sms'],
            'title'         => ['type' => 'VARCHAR', 'constraint' => '200'],
            'message'       => ['type' => 'TEXT'],
            'sent_to'       => ['type' => 'TEXT'],
            'created_at'    => ['type' => 'DATETIME'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('announcements');
    }

    public function down()
    {
        //
        $this->forge->dropTable('announcements');
    }
}
