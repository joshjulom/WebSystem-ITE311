<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentQuestionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'assignment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_type' => [
                'type' => 'ENUM',
                'constraint' => ['multiple_choice', 'essay', 'file_upload'],
            ],
            'question_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of options for multiple choice questions'
            ],
            'correct_answer' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Correct answer for multiple choice, or expected answer for essay'
            ],
            'max_points' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 10.00,
            ],
            'order_position' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assignment_questions');
    }

    public function down()
    {
        $this->forge->dropTable('assignment_questions');
    }
}
