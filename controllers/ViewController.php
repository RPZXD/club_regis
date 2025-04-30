<?php
namespace App\Controllers;

use App\Models\Document;

class ViewController
{
    private $documentModel;

    public function __construct()
    {
        $this->documentModel = new Document();
    }

    public function index()
    {
        return $this->documentModel->getAll();
    }
}
