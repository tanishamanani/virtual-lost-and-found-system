<?php
// Client side userModel.
?>
<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users_log';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'fullname', 'username', 'email', 'password',
        'course', 'year', 'division', 'contact_no'
    ];
    protected $returnType    = 'array'; 
}
