<?php
// Admin Panel's user controller
?>
<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserController extends Controller
{
    // Get all users
    public function getUsers()
    {
        $model = new Admin_UserModel();
        $users = $model->findAll();

        return $this->response->setJSON($users);
    }

    // Update user status (block, suspend, active)
    public function updateStatus()
    {
        $data = $this->request->getJSON(true);
        $id = $data['user_id'] ?? null;
        $status = $data['status'] ?? null;

        if ($id && $status) {
            $model = new Admin_UserModel();
            $model->update($id, ['status' => $status]);

            return $this->response->setJSON([
                'success' => true,
                'message' => "User status updated to $status"
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => "Invalid request"
        ]);
    }

    // Delete user
    public function deleteUser($id = null)
    {
        $model = new Admin_UserModel();
        if ($id && $model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => "User deleted"]);
        }
        return $this->response->setJSON(['success' => false, 'message' => "Delete failed"]);
    }
}
