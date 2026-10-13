<?php

class ModuleController extends Controller
{
    public function index()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'is_super_admin' => $_SESSION['user']['name'],
            'title' => 'Home'
        ];
        $this->view('home', $data);
    }

    public function about()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'title' => 'About'
        ];
        $this->view('about', $data);
    }

    // public function users()
    // {
    //     $data = [
    //         'name' => $_SESSION['user']['name'],
    //         'title' => 'Users',
    //     ];
    //     $this->view('users', $data);
    // }


    /////////// module pages here /////////
    public function hris()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'title' => 'HRIS Home'
        ];
        $this->view('module_hris/hris_index', $data);
    }

    public function payroll()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'title' => 'HRIS Home'
        ];
        $this->view('module_payroll/payroll_index', $data);
    }
}