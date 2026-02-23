<?php
namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
{
    $userModel = new UserModel();
    
    $username = $this->request->getPost('username');
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');
    $confirmPassword = $this->request->getPost('confirm_password');

    // Check duplicate email
    if ($userModel->where('email', $email)->first()) {
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?error=' . urlencode('This Email is Already Registered!'));
    }

    // Check duplicate username
    if ($userModel->where('username', $username)->first()) {
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?error=' . urlencode('This UserName Already Exists! Please select an unique UserName!'));
    }

     //  Check password confirmation
    if ($password !== $confirmPassword) {
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?error=' . urlencode('Passwords do not match!'));
    }

    //  Check password length
    if (strlen($password) < 4) {
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?error=' . urlencode('Password must be atleast 4 characters long!'));
    }


    // Insert user 
    $data = [
        'fullname'   => $this->request->getPost('fullname'),
        'username'   => $username,
        'email'      => $email,
        'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'course'     => $this->request->getPost('course'),
        'year'       => $this->request->getPost('year'),
        'division'   => $this->request->getPost('division'),
        'contact_no' => $this->request->getPost('contact_no'),
        'status'     => 'active'   // all new users are active by default
    ];

    $userModel->insert($data);

    return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/LR2.php?success=' . urlencode('Registration successful! Please login.'));
}

    public function login()
{
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    $userModel = new UserModel();
    $user = $userModel->where('username', $username)->first();

     if ($user) {
        // Check if user is blocked
        if ($user['status'] === 'blocked') {
            echo "<script>alert('Your account is blocked. Please contact admin.'); window.location.href='http://localhost/Lost_And_Found/PHP_proj/LR2.php';</script>";
            exit;
        }

    // stored a hashed password 
    if (!password_verify($password, $user['password'])) {
        echo "<script>alert('Invalid username or password!'); window.location.href='http://localhost/Lost_And_Found/PHP_proj/LR2.php';</script>";
            exit;
    }

    $session = session();

    session()->set([
        'isLoggedIn' => true,
        'user_id'   => $user['user_id'],
        'fullname'   => $user['fullname'],
        'username'  => $user['username'],
    ]);

    // set cookies for plain PHP pages
    setcookie('isLoggedIn', '1', time() + 3600, '/');
    setcookie('fullname', $user['fullname'], time() + 3600, '/');
    setcookie('user_id',   $user['user_id'],  time() + 3600, '/');

    return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/user_dashboard.php');
  }

  else {
        echo "<script>alert('User not found!'); window.location.href='http://localhost/Lost_And_Found/PHP_proj/LR2.php';</script>";
        exit;
    }
}

    public function logout()
    {
        // Remove cookie by setting expiry in past
        setcookie("fullname", "", time() - 3600, "/");
        return redirect()->to('http://localhost/Lost_And_Found/PHP_proj/index.php?success=' . urlencode('Logged out successfully'));
    }
}
