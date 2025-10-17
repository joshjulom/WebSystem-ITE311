<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Announcement extends Controller
{
    public function index()
    {
        $model = new AnnouncementModel();
        
        // Fetch all announcements ordered by created_at in descending order (newest first)
        $announcements = $model->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'announcements' => $announcements
        ];
        
        return view('announcements', $data);
    }
}
