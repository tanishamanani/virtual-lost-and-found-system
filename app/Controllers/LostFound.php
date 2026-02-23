<?php

namespace App\Controllers;

use App\Models\LostFoundModel;
use CodeIgniter\RESTful\ResourceController;

class LostFound extends ResourceController
{
    // Tell CodeIgniter which model to use
    protected $modelName = LostFoundModel::class;
    protected $format    = 'json';  // default output format

    /**
     * ---------------------------
     * READ: Fetch all items
     * ---------------------------
     * This is used when we want to display all lost/found items.
     * Example: GET /items
     */
    public function getAllItems()
    {
        $data = $this->model->findAll();   // fetch all rows from DB
        return $this->respond($data);      // return as JSON response
    }

    /**
     * ---------------------------
     * READ: Fetch single item by ID
     * ---------------------------
     * Example: GET /items/5
     */
    public function getItem($id = null)
    {
        $item = $this->model->find($id);   // find one record by ID
        if (!$item) {
            return $this->failNotFound("Item not found with ID: $id");
        }
        return $this->respond($item);
    }

    /**
     * ---------------------------
     * CREATE: Add a new item (from your frontend form)
     * ---------------------------
     * Example: POST form → /items
     */
    public function addItem()
    {
        $model = new LostFoundModel();

        // Collect form data sent from your HTML form
        $data = [
            'item_type'      => $this->request->getPost('item_type'),
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'color'          => $this->request->getPost('color'),
            'location'       => $this->request->getPost('location'),
            'contact_name'   => $this->request->getPost('contact_name'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'created_at'     => date('Y-m-d H:i:s'),  // timestamp when created
        ];

        // ✅ Handle file upload if user attaches an image
        $img = $this->request->getFile('images'); // "images" is the input name in your form

        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();               // generate safe file name
            $img->move(FCPATH . 'uploads', $newName);       // move file to public/uploads folder
            $data['images'] = $newName;                     // store filename in DB
        }

        // Save data into DB
        $model->insert($data);

        // Redirect back to form with success message
        return redirect()->back()->with('success', 'Item added successfully!');
    }

    /**
     * ---------------------------
     * UPDATE: Edit an existing item
     * ---------------------------
     * Example: Form (POST) → /items/update/5
     */
    public function updateItem($id = null)
    {
        $model = new LostFoundModel();

        // First check if the item exists
        $item = $model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', "Item not found with ID $id");
        }

        // Get updated form data
        $data = [
            'item_type'      => $this->request->getPost('item_type'),
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'color'          => $this->request->getPost('color'),
            'location'       => $this->request->getPost('location'),
            'contact_name'   => $this->request->getPost('contact_name'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'contact_email'  => $this->request->getPost('contact_email'),
        ];

        // ✅ Handle file update if a new image is uploaded
        $img = $this->request->getFile('images');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads', $newName);
            $data['images'] = $newName;
        }

        // Update in DB
        $model->update($id, $data);

        // Redirect with success message
        return redirect()->back()->with('success', "Item updated successfully!");
    }

    /**
     * ---------------------------
     * DELETE: Remove an item
     * ---------------------------
     * Example: POST form → /items/delete/5
     */
    public function deleteItem($id = null)
    {
        $model = new LostFoundModel();

        // First check if record exists
        $item = $model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', "Item not found with ID $id");
        }

        // Delete record from DB
        $model->delete($id);

        // Redirect with success message
        return redirect()->back()->with('success', "Item deleted successfully!");
    }
}
