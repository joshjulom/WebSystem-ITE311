<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseFields extends Migration
{
    public function up()
    {
        // Add missing columns to courses table
        // First check which columns exist and only add the missing ones
        $fields = $this->db->getFieldNames('courses');

        if (!in_array('school_year', $fields)) {
            $this->forge->addColumn('courses', [
                'school_year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => true,
                    'after'      => 'description'
                ]
            ]);
        }

        if (!in_array('semester', $fields)) {
            $this->forge->addColumn('courses', [
                'semester' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'null'       => true,
                    'after'      => 'school_year'
                ]
            ]);
        }

        if (!in_array('max_students', $fields)) {
            $this->forge->addColumn('courses', [
                'max_students' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'null'       => true,
                    'default'    => 30,
                    'after'      => 'semester'
                ]
            ]);
        }

        if (!in_array('start_time', $fields)) {
            $this->forge->addColumn('courses', [
                'start_time' => [
                    'type'       => 'TIME',
                    'null'       => true,
                    'after'      => 'max_students'
                ]
            ]);
        }

        if (!in_array('end_time', $fields)) {
            $this->forge->addColumn('courses', [
                'end_time' => [
                    'type'       => 'TIME',
                    'null'       => true,
                    'after'      => 'start_time'
                ]
            ]);
        }

        if (!in_array('start_date', $fields)) {
            $this->forge->addColumn('courses', [
                'start_date' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'end_time'
                ]
            ]);
        }

        if (!in_array('end_date', $fields)) {
            $this->forge->addColumn('courses', [
                'end_date' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'start_date'
                ]
            ]);
        }

        if (!in_array('status', $fields)) {
            $this->forge->addColumn('courses', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => false,
                    'default'    => 'Active',
                    'after' => 'end_date'
                ]
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'course_code',
            'school_year',
            'semester',
            'schedule',
            'max_students',
            'start_time',
            'end_time',
            'start_date',
            'end_date',
            'status'
        ]);
    }
}
