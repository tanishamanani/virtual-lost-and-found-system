<?php

namespace App\Controllers;
use App\Models\ItemModel;
use CodeIgniter\Controller;

class ItemsController extends Controller
{
    private function checkLogin()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !$session->get('user_id')) {
            // Redirect to login page if not logged in
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?error=' . urlencode('You must be logged in to perform this action.'));

        }

        return null; // means logged in
    }

    public function index()
    {
        $model = new ItemModel();
        $data = $model->findAll();
        return $this->response->setJSON($data);
    }

    public function insert()
    {
        //  Check login
        $loginRedirect = $this->checkLogin();
        if ($loginRedirect) return $loginRedirect;

        $session = session();
        $userId = $session->get('user_id');

        //  Validation Rules
        $rules = [
            'contact_phone' => 'required|numeric|exact_length[10]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode(implode(', ', $errors)));
        }

        $db = \Config\Database::connect();
        $builder = $db->table('lost_and_found');

        //  Handle Image Upload
        $files = $this->request->getFiles();
        $uploadedImages = [];

        if (isset($files['images']) && $files['images']) {
            foreach ($files['images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move('D:/xampp_2/htdocs/Lost_And_Found/PHP_proj/images/', $newName);
                    $uploadedImages[] = './Lost_And_Found/PHP_proj/images/' . $newName;
                }
            }
        }

        $imagePathString = implode(',', $uploadedImages);

        $data = [
            'user_id'       => $userId, //  logged in user
            'item_type'     => $this->request->getPost('item_type'),
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'color'         => $this->request->getPost('color'),
            'images'        => $imagePathString,
            'location'      => $this->request->getPost('location'),
            'contact_name'  => $this->request->getPost('contact_name'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
            'status'        => 'pending'
        ];

        $builder->insert($data);
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?success=' . urlencode('Item inserted successfully! It is Display below the form.'));
    }

    public function update()
    {
        //  Check login
        $loginRedirect = $this->checkLogin();
        if ($loginRedirect) return $loginRedirect;

        $session = session();
        $userId = $session->get('user_id');

        $id = $this->request->getPost('lf_id');
        if (empty($id)) {
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('Missing lf_id.'));
        }

        $model = new ItemModel();

        $rules = [
            'contact_phone' => 'required|numeric|exact_length[10]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode(implode(', ', $errors)));
        }

        $existingItem = $model->find($id);
        if (!$existingItem) {
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('Item not found.'));
        }

        //  Make sure the logged-in user owns this item
        if ($existingItem['user_id'] != $userId) {
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('You are not allowed to update this item.'));
        }

        $data = [
            'item_type'     => $this->request->getPost('item_type'),
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'color'         => $this->request->getPost('color'),
            'location'      => $this->request->getPost('location'),
            'contact_name'  => $this->request->getPost('contact_name'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
        ];

        // === Image handling (MULTIPLE) ===
        $uploadsServerDirectory = 'D:/xampp_2/htdocs/Lost_And_Found/PHP_proj/images/';
        $relativePrefixForDB = './Lost_And_Found/PHP_proj/images/';
        if (!is_dir($uploadsServerDirectory)) {
            @mkdir($uploadsServerDirectory, 0777, true);
        }

        $newFiles = $this->request->getFileMultiple('images');
        $uploadedImages = [];
        $hasNewFiles = false;

        if (!empty($newFiles)) {
            foreach ($newFiles as $file) {
                if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) continue;

                if ($file->isValid() && !$file->hasMoved()) {
                    $hasNewFiles = true;
                    $newName = $file->getRandomName();
                    $file->move($uploadsServerDirectory, $newName);
                    $uploadedImages[] = $relativePrefixForDB . $newName;
                }
            }
        }

        if ($hasNewFiles && !empty($uploadedImages)) {
            $oldImagesCSV = $existingItem['images'] ?? '';
            if (!empty($oldImagesCSV)) {
                $oldImages = array_filter(array_map('trim', explode(',', $oldImagesCSV)));
                foreach ($oldImages as $oldWebPath) {
                    $basename = basename($oldWebPath);
                    $absPath = rtrim($uploadsServerDirectory, '/\\') . DIRECTORY_SEPARATOR . $basename;
                    if (is_file($absPath)) {
                        @unlink($absPath);
                    }
                }
            }
            $data['images'] = implode(',', $uploadedImages);
        } else {
            $data['images'] = $existingItem['images'] ?? null;
        }

        $success = $model->update($id, $data);

        if (!$success) {
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('Item update failed.'));
        }

        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?success=' . urlencode('Item updated successfully!'));
    }

    // Delete item
    public function delete()
    {
        // Check login
        $loginRedirect = $this->checkLogin();
        if ($loginRedirect) return $loginRedirect;

        $session = session();
        $userId = $session->get('user_id');

        $model = new ItemModel();
        $id = $this->request->getPost('lf_id');
        if ($id) {
            $existingItem = $model->find($id);
            if (!$existingItem) {
                return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('Item not found.'));
            }

            //  User can only delete their own items
            if ($existingItem['user_id'] != $userId) {
                return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('You are not allowed to delete this item.'));
            }

            $model->delete($id);
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?success=' . urlencode('Item deleted successfully!'));
        }
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LF_report.php?error=' . urlencode('Something went wrong!'));
    }

    // Get all items
    public function getAll()
    {
        $model = new ItemModel();
        return $this->response->setJSON($model->findAll());
    }
}
