<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
<<<<<<< HEAD
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'email', 'password', 'role'];

    protected $useTimestamps = true; // enables automatic created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
=======
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; // timestamps handled by DB defaults in migration
}


>>>>>>> 4ce6d5449c1f03dd0a546ba78ef04f097ef7b778
