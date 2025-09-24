<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsers extends Migration
{
	public function up()
	{
		$db = \Config\Database::connect();
		$forge = \Config\Database::forge();

		// Add role column if it does not exist
		if (!$db->fieldExists('role', 'users')) {
			$fields = [
				'role' => [
					'type' => 'VARCHAR',
					'constraint' => 20,
					'null' => false,
					'default' => 'student',
					'after' => 'password',
				],
			];
			$forge->addColumn('users', $fields);
		}

		// Ensure an index on role for faster filtering
		// CodeIgniter Forge lacks direct conditional index checks; attempt and ignore if exists
		try {
			$forge->addKey('role');
			$forge->processIndexes('users');
		} catch (\Throwable $e) {
			// Ignore errors if index already exists
		}
	}

	public function down()
	{
		$forge = \Config\Database::forge();
		// Drop role column if exists
		try {
			$forge->dropColumn('users', 'role');
		} catch (\Throwable $e) {
			// ignore
		}
	}
}
