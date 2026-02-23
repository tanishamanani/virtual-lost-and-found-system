<?php
// client side ItemModel.php
?>
<?php

namespace App\Models;
use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'lost_and_found';      
    protected $primaryKey = 'lf_id';          

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'item_type',
        'title',
        'description',
        'color',
        'location',
        'images',
        'contact_name',
        'contact_phone',
        'contact_email',
        'created_at', 
    ];

    // timestamps settings
    protected $useTimestamps = true;           // enable timestamps
    protected $createdField  = 'created_at';   // only created_at exists
    protected $updatedField  = '';             // tell CI there is no updated_at
}
