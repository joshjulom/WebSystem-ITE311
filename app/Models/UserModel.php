<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation - Only validate fields that exist in your database
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        // Allow Ñ/ñ in the local-part; keep domain limited to ASCII (punycode for IDNs is recommended)
        'email' => 'required|is_unique[users.email]|regex_match[/^[A-Za-z0-9._%+\-Ññ]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/]',
        'password' => 'required|min_length[6]'
        // Removed role validation since we're setting it programmatically
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.',
            'regex_match' => 'Please enter a valid email address. Allowed characters before the @: letters, numbers and . _ % + - and Ñ/ñ.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }
}
