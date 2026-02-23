<?php 
namespace App\Models;

use CodeIgniter\Model;

class Admin_InsertModel extends Model
{
    protected $table      = 'lost_and_found';
    protected $primaryKey = 'lf_id';

    protected $allowedFields = [
        'user_id', 'item_type', 'title', 'description', 'color', 
        'location', 'images', 'status', 'contact_name', 
        'contact_phone', 'contact_email', 'created_at'
    ];

    protected $useTimestamps = false;
}
