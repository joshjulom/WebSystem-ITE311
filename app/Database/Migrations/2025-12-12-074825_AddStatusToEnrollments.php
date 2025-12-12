<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollments extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'null'       => false,
                'after'      => 'enrollment_date'
            ],
        ];
        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'status');
    }
}
