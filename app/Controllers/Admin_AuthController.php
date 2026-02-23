<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class Admin_AuthController extends BaseController
{
    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if ($username === 'admin' && $password === 'admin123') {
            session()->set('isAdminLoggedIn', true);
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/admin_Panel/dashboard');
        } else {
            $_SESSION['login_error'] = 'Invalid username or password';
            return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/admin_Panel/admin_login.php');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/admin_Panel/admin_login.php');
    }
}
