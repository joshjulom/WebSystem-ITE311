<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginLogsTable extends Migration
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
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => false,
			],
			'user_name' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
			],
			'user_email' => [
				'type' => 'VARCHAR',
				'constraint' => 191,
				'null' => false,
			],
			'user_role' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => false,
			],
			'ip_address' => [
				'type' => 'VARCHAR',
				'constraint' => 45,
				'null' => true,
			],
			'login_time' => [
				'type' => 'DATETIME',
				'null' => false,
				'default' => 'CURRENT_TIMESTAMP',
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('user_id');
		$this->forge->addKey('user_role');
		$this->forge->createTable('login_logs', true);
	}

	public function down()
	{
		$this->forge->dropTable('login_logs', true);
	}
}


