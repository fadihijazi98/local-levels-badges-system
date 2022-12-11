<?php

namespace Controllers;

class StudentsController extends BaseController
{
    protected function index()
    {
        include __DIR__ . '/../views/students.php';
    }
}