<?php
namespace App\Models;

use CodeIgniter\Model;

class LoginLogModel extends Model
{
	protected $table = 'login_logs';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $protectFields = true;
	protected $allowedFields = [
		'user_id',
		'user_name',
		'user_email',
		'user_role',
		'ip_address',
		'login_time',
	];

	public function logLogin(int $userId, string $userName, string $userEmail, string $userRole): bool
	{
		$ipAddress = service('request')->getIPAddress();
		$data = [
			'user_id' => $userId,
			'user_name' => $userName,
			'user_email' => $userEmail,
			'user_role' => $userRole,
			'ip_address' => $ipAddress,
			'login_time' => date('Y-m-d H:i:s'),
		];
		return (bool) $this->insert($data);
	}

	public function getRecentLogins(int $limit = 10): array
	{
		return $this->orderBy('login_time', 'DESC')
			->limit($limit)
			->find();
	}

	public function getLoginStatsByRole(int $days = 7): array
	{
		$cutoff = date('Y-m-d 00:00:00', strtotime('-' . max(0, $days - 1) . ' days'));
		return $this->select('user_role, COUNT(*) as login_count')
			->where('login_time >=', $cutoff)
			->groupBy('user_role')
			->orderBy('login_count', 'DESC')
			->find();
	}

	public function getRecentUniqueUsers(int $days = 7, int $limit = 10): array
	{
		$cutoff = date('Y-m-d 00:00:00', strtotime('-' . max(0, $days - 1) . ' days'));
		// Get latest login per user within window
		$sql = "
			SELECT ll.user_id, ll.user_name, ll.user_email, ll.user_role, MAX(ll.login_time) AS last_login
			FROM {$this->table} ll
			WHERE ll.login_time >= ?
			GROUP BY ll.user_id, ll.user_name, ll.user_email, ll.user_role
			ORDER BY last_login DESC
			LIMIT ?
		";
		return $this->db->query($sql, [$cutoff, $limit])->getResultArray();
	}
}


