<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Use user ID 1 as instructor for all courses (assuming this is an admin/teacher account)
        $courses = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of HTML, CSS, and JavaScript to build modern web applications. This comprehensive course covers responsive design, basic programming concepts, and hands-on projects.',
                'instructor_id' => 1, // Admin/Teacher user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Master advanced PHP concepts including object-oriented programming, MVC architecture, database integration, and security best practices. Perfect for developers looking to build robust web applications.',
                'instructor_id' => 1, // Admin/Teacher user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Database Design and Management',
                'description' => 'Comprehensive course on database design principles, SQL optimization, normalization, and modern database management systems. Learn to design efficient and scalable database solutions.',
                'instructor_id' => 1, // Admin/Teacher user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Mobile App Development with React Native',
                'description' => 'Build cross-platform mobile applications using React Native. Learn component design, state management, API integration, and deployment strategies for iOS and Android.',
                'instructor_id' => 1, // Admin/Teacher user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Essential cybersecurity concepts including threat analysis, encryption, network security, ethical hacking, and security best practices. Learn to protect systems and data from cyber threats.',
                'instructor_id' => 1, // Admin/Teacher user ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert courses
        foreach ($courses as $course) {
            $this->db->table('courses')->insert($course);
        }

        echo "5 courses seeded successfully!\n";
    }
}
