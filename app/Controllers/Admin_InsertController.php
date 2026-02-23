<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Admin_InsertModel;

class Admin_InsertController extends BaseController
{
    public function create()
    {
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/Admin_Panel/Admin_Insert.php');
    }

    public function store()
    {
        $model = new Admin_InsertModel();

          // Handle Image Upload
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
            'user_id'       => 9999, 
            'item_type'     => $this->request->getPost('item_type'),
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'color'         => $this->request->getPost('color'),
            'location'      => $this->request->getPost('location'),
            'images'        => $imagePathString,
            'status'        => 'approved', 
            'contact_name'  => $this->request->getPost('contact_name'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $model->insert($data);

        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/Admin_Panel/posts.php?success=' . urlencode('Item added successfully & auto-approved!'));
    }
}
