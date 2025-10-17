<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Academic Year!',
                'content' => 'We are excited to welcome all students, teachers, and staff to the new academic year. This year promises to be filled with learning opportunities, growth, and success. Please make sure to check your course schedules and familiarize yourself with the new features of our learning management system.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'title' => 'Important: System Maintenance Scheduled',
                'content' => 'Please be informed that our system will undergo scheduled maintenance on Sunday, October 20th, from 2:00 AM to 6:00 AM. During this time, the portal will be temporarily unavailable. We apologize for any inconvenience this may cause and appreciate your understanding.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'title' => 'Midterm Examination Guidelines',
                'content' => 'Midterm examinations will begin next week. Please review the examination schedule posted in your course pages. Remember to bring your student ID and arrive 15 minutes before your scheduled exam time. Good luck to all students!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            ]
        ];

        // Using the model to insert data
        $model = new \App\Models\AnnouncementModel();
        $model->insertBatch($data);
    }
}
