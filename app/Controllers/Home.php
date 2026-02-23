<?php namespace App\Controllers;
helper('url');
class Home extends BaseController
{
    public function index()
    {
        return view('pages/home'); // loads app/Views/pages/home.php
    }
}
