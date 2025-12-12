<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationFields extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('notifications');

        if (!in_array('type', $fields)) {
            $this->forge->addColumn('notifications', [
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => 'general',
                    'after'      => 'message'
                ]
            ]);
        }

        if (!in_array('enrollment_id', $fields)) {
            $this->forge->addColumn('notifications', [
                'enrollment_id' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'type'
                ]
            ]);
            // Add foreign key to enrollments table
            $this->forge->addForeignKey('enrollment_id', 'enrollments', 'id', 'CASCADE', 'CASCADE');
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('notifications');

        if (in_array('enrollment_id', $fields)) {
            // Drop the foreign key first
            $this->forge->dropForeignKey('notifications', 'notifications_enrollment_id_foreign');
            $this->forge->dropColumn('notifications', 'enrollment_id');
        }

        if (in_array('type', $fields)) {
            $this->forge->dropColumn('notifications', 'type');
        }
    }
}
