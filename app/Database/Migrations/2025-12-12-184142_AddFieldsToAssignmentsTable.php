<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToAssignmentsTable extends Migration
{
    public function up()
    {
        $fields = [
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 100.00,
                'after' => 'due_date'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'closed'],
                'default' => 'draft',
                'after' => 'max_score'
            ]
        ];

        $this->forge->addColumn('assignments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', ['max_score', 'status']);
    }
}
