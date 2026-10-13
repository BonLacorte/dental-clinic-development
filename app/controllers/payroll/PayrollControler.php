<?php
require_once __DIR__ . '/../../models/payroll/PayrollSchedulelModel.php'; //// always check for paths when creating controllers

class PayrollController extends Controller
{
    protected $payrollScheduleModel;

    public function __construct()
    {
        $this->payrollScheduleModel = new PayrollScheduleModel();
    }

    public function schedules()
    {

    }

    
}