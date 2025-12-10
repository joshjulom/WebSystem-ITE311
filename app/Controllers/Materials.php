<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Materials extends BaseController
{
    protected $materialModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->materialModel = new MaterialModel();
    }

    /**
     * Display the file upload form and handle the file upload process.
     *
     * @param int $course_id
     */
    public function upload($course_id)
    {
        if ($this->request->getMethod() === 'POST') {
            // Load upload and validation libraries
            $validation = \Config\Services::validation();
            $validation->setRules([
                'material' => 'uploaded[material]|max_size[material,10240]|ext_in[material,pdf,doc,docx,ppt,pptx,zip]',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->with('error', $validation->getErrors());
            }

            $file = $this->request->getFile('material');
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);

                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/' . $newName,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                if ($this->materialModel->insertMaterial($data)) {
                    return redirect()->back()->with('success', 'Material uploaded successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to save material.');
                }
            }
        }

        // Display upload form
        $materials = $this->materialModel->getMaterialsByCourse($course_id);
        return view('materials/upload', ['course_id' => $course_id, 'materials' => $materials]);
    }

    /**
     * Handle the deletion of a material record and the associated file.
     *
     * @param int $material_id
     */
    public function delete($material_id)
    {
        $material = $this->materialModel->find($material_id);
        if ($material) {
            // Delete file if exists
            if (file_exists(WRITEPATH . $material['file_path'])) {
                unlink(WRITEPATH . $material['file_path']);
            }
            $this->materialModel->delete($material_id);
            return redirect()->back()->with('success', 'Material deleted successfully.');
        }
        return redirect()->back()->with('error', 'Material not found.');
    }

    /**
     * Handle the file download for enrolled students.
     *
     * @param int $material_id
     */
    public function download($material_id)
    {
        $material = $this->materialModel->find($material_id);
        if ($material && file_exists(WRITEPATH . $material['file_path'])) {
            // Check if user is logged in
            if (!session('user_id')) {
                return redirect()->to('/login')->with('error', 'Please log in to download materials.');
            }

            // Check if user account is active
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find(session('user_id'));
            if (!$user || ($user['status'] ?? 'active') !== 'active') {
                return redirect()->to('/login')->with('error', 'Your account has been deactivated.');
            }

            // Check if user is enrolled in the course
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $isEnrolled = $enrollmentModel->isAlreadyEnrolled(session('user_id'), $material['course_id']);

            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'You are not enrolled in this course and cannot download the material.');
            }

            $filePath = WRITEPATH . $material['file_path'];
            $fileName = $material['file_name'];

            return $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                                  ->setHeader('Content-Type', 'application/octet-stream')
                                  ->setBody(file_get_contents($filePath))
                                  ->send();
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
