<?php

// Payroll module and permission
$app->group(['AuthMiddleware', 'TenantMiddleware', 'ModuleMiddleware:module=PAYROLL'], function ($app) {

    // $app->get('/payroll/schedules', [
    //     'PermissionMiddleware:permission=payroll.schedule.view',
    //     'payroll/PayrollController@schedules'
    // ]);

    // create
    $app->get('/payroll/create', 'payroll/PayrollController@create', ['PermissionMiddleware:permission=payroll.create']);

    // Allowances
    $app->get('/payroll/allowances', 'payroll/AllowancesController@index');
    $app->get('/payroll/allowances/getallallowances', 'payroll/AllowancesController@getAllAllowances');
    $app->get('/payroll/allowances/getallowancebyid/{id}', 'payroll/AllowancesController@getAllowanceById');
    $app->post('/payroll/allowances/create', 'payroll/AllowancesController@create');
    $app->post('/payroll/allowances/update/{id}', 'payroll/AllowancesController@update');
    $app->post('/payroll/allowances/delete/{id}', 'payroll/AllowancesController@delete');
    $app->get('/payroll/allowances/types/getall', 'payroll/AllowancesController@getAllTypes');
    $app->get('/payroll/allowances/types/getbyid/{id}', 'payroll/AllowancesController@getTypeById');
    $app->post('/payroll/allowances/types/create', 'payroll/AllowancesController@createType');
    $app->post('/payroll/allowances/types/update/{id}', 'payroll/AllowancesController@updateType');
    $app->post('/payroll/allowances/types/delete/{id}', 'payroll/AllowancesController@deleteType');
    $app->get('/payroll/allowances/employees/options', 'payroll/AllowancesController@getEmployees');
    $app->get('/payroll/allowances/types/options', 'payroll/AllowancesController@getTypeOptions');

    // Cash Advances
    $app->get('/payroll/cash-advances', 'payroll/CashAdvancesController@index');
    $app->get('/payroll/cash-advances/getallcashadvances', 'payroll/CashAdvancesController@getAllCashAdvances');
    $app->get('/payroll/cash-advances/getcashadvancebyid/{id}', 'payroll/CashAdvancesController@getCashAdvanceById');
    $app->post('/payroll/cash-advances/create', 'payroll/CashAdvancesController@create');
    $app->post('/payroll/cash-advances/update/{id}', 'payroll/CashAdvancesController@update');
    $app->post('/payroll/cash-advances/delete/{id}', 'payroll/CashAdvancesController@delete');
    $app->get('/payroll/cash-advances/employees/options', 'payroll/CashAdvancesController@getEmployees');

    // Commissions
    $app->get('/payroll/commissions', 'payroll/CommissionsController@index');
    $app->get('/payroll/commissions/getallcommissions', 'payroll/CommissionsController@getAllCommissions');
    $app->get('/payroll/commissions/getcommissionbyid/{id}', 'payroll/CommissionsController@getCommissionById');
    $app->post('/payroll/commissions/create', 'payroll/CommissionsController@create');
    $app->post('/payroll/commissions/update/{id}', 'payroll/CommissionsController@update');
    $app->post('/payroll/commissions/delete/{id}', 'payroll/CommissionsController@delete');
    $app->get('/payroll/commissions/types/getall', 'payroll/CommissionsController@getAllTypes');
    $app->get('/payroll/commissions/types/getbyid/{id}', 'payroll/CommissionsController@getTypeById');
    $app->post('/payroll/commissions/types/create', 'payroll/CommissionsController@createType');
    $app->post('/payroll/commissions/types/update/{id}', 'payroll/CommissionsController@updateType');
    $app->post('/payroll/commissions/types/delete/{id}', 'payroll/CommissionsController@deleteType');
    $app->get('/payroll/commissions/employees/options', 'payroll/CommissionsController@getEmployees');
    $app->get('/payroll/commissions/types/options', 'payroll/CommissionsController@getTypeOptions');

    // Compensations
    $app->get('/payroll/compensations', 'payroll/CompensationsController@index');
    $app->get('/payroll/compensations/getallcompensations', 'payroll/CompensationsController@getAllCompensations');
    $app->get('/payroll/compensations/getcompensationbyid/{id}', 'payroll/CompensationsController@getCompensationById');
    $app->post('/payroll/compensations/create', 'payroll/CompensationsController@create');
    $app->post('/payroll/compensations/update/{id}', 'payroll/CompensationsController@update');
    $app->post('/payroll/compensations/delete/{id}', 'payroll/CompensationsController@delete');
    $app->get('/payroll/compensations/employees/options', 'payroll/CompensationsController@getEmployees');

    // Deductions
    $app->get('/payroll/deductions', 'payroll/DeductionsController@index');
    $app->get('/payroll/deductions/getalldeductions', 'payroll/DeductionsController@getAllDeductions');
    $app->get('/payroll/deductions/getdeductionbyid/{id}', 'payroll/DeductionsController@getDeductionById');
    $app->post('/payroll/deductions/create', 'payroll/DeductionsController@create');
    $app->post('/payroll/deductions/update/{id}', 'payroll/DeductionsController@update');
    $app->post('/payroll/deductions/delete/{id}', 'payroll/DeductionsController@delete');
    $app->get('/payroll/deductions/types/getall', 'payroll/DeductionsController@getAllTypes');
    $app->get('/payroll/deductions/types/getbyid/{id}', 'payroll/DeductionsController@getTypeById');
    $app->post('/payroll/deductions/types/create', 'payroll/DeductionsController@createType');
    $app->post('/payroll/deductions/types/update/{id}', 'payroll/DeductionsController@updateType');
    $app->post('/payroll/deductions/types/delete/{id}', 'payroll/DeductionsController@deleteType');
    $app->get('/payroll/deductions/employees/options', 'payroll/DeductionsController@getEmployees');
    $app->get('/payroll/deductions/types/options', 'payroll/DeductionsController@getTypeOptions');

    // Incentives
    $app->get('/payroll/incentives', 'payroll/IncentivesController@index');
    $app->get('/payroll/incentives/getallincentives', 'payroll/IncentivesController@getAllIncentives');
    $app->get('/payroll/incentives/getincentivebyid/{id}', 'payroll/IncentivesController@getIncentiveById');
    $app->post('/payroll/incentives/create', 'payroll/IncentivesController@create');
    $app->post('/payroll/incentives/update/{id}', 'payroll/IncentivesController@update');
    $app->post('/payroll/incentives/delete/{id}', 'payroll/IncentivesController@delete');
    $app->get('/payroll/incentives/types/getall', 'payroll/IncentivesController@getAllTypes');
    $app->get('/payroll/incentives/types/getbyid/{id}', 'payroll/IncentivesController@getTypeById');
    $app->post('/payroll/incentives/types/create', 'payroll/IncentivesController@createType');
    $app->post('/payroll/incentives/types/update/{id}', 'payroll/IncentivesController@updateType');
    $app->post('/payroll/incentives/types/delete/{id}', 'payroll/IncentivesController@deleteType');
    $app->get('/payroll/incentives/employees/options', 'payroll/IncentivesController@getEmployees');
    $app->get('/payroll/incentives/types/options', 'payroll/IncentivesController@getTypeOptions');



    // $app->get('/payroll/attendance/import', 'payroll/PayrollRunController@importView');
    //  $app->post('/payroll/attendance/import', 'payroll/PayrollRunController@importView');
    // $app->get('/payroll/attendance/import', 'payroll/BiometricController@index');
    // $app->post('/payroll/attendance/import', 'payroll/BiometricController@index');

    $app->get('/payroll/attendance/import', 'payroll/BiometricController@index');
    $app->post('/payroll/attendance/import', 'payroll/BiometricController@index');
    $app->post('/payroll/attendance/import/process', 'payroll/BiometricController@import');

    // Payroll Runs
    $app->get('/payroll/runs', 'payroll/PayrollRunController@index');
    $app->get('/payroll/runs/getallruns', 'payroll/PayrollRunController@getAllRuns');
    $app->post('/payroll/runs/create', 'payroll/PayrollRunController@create');
    $app->get('/payroll/runs/view/{id}', 'payroll/PayrollRunController@viewRun');
    $app->post('/payroll/runs/load-employees/{id}', 'payroll/PayrollRunController@loadEmployees');
    $app->post('/payroll/runs/process/{id}', 'payroll/PayrollRunController@process');
    $app->post('/payroll/runs/finalize/{id}', 'payroll/PayrollRunController@finalize');
    $app->post('/payroll/runs/cancel/{id}', 'payroll/PayrollRunController@cancel');
    $app->post('/payroll/runs/delete/{id}', 'payroll/PayrollRunController@delete');
    $app->post('/payroll/runs/reset/{id}', 'payroll/PayrollRunController@reset');
    $app->get('/payroll/runs/{runId}/employee/{runEmployeeId}', 'payroll/PayrollRunController@employeeDetails');


});
