<?php



$app->post('/hris/attendance/scan', 'hris/AttendanceLogController@scan');


// HRIS module and permission
$app->group(['AuthMiddleware', 'TenantMiddleware', 'ModuleMiddleware:module=HRIS'], function ($app) {

    // create
    // $app->get('/hris/employees/create', 'hris/EmployeeController@create', ['PermissionMiddleware:permission=hris.employee.create']);

    // // edit and update
    // $app->group(['PermissionMiddleware:permission=hris.employee.edit'], function ($app) {
    //     $app->get('/hris/employees/edit', 'hris/EmployeeController@edit');
    //     $app->post('/hris/employees/update', 'hris/EmployeeController@update');
    // });

    // // delete
    // $app->get('/hris/employees/delete', 'hris/EmployeeController@delete', ['PermissionMiddleware:permission=hris.employee.delete']);

    //web pages
    $app->get('/hris/employees', 'hris/EmployeeController@index', ['PermissionMiddleware:permission=hris.employee.view']);
    $app->get('/hris/offices', 'hris/OrgUnitsController@index', ['PermissionMiddleware:permission=hris.view']);
    $app->get('/hris/branches', 'hris/BranchController@index', ['PermissionMiddleware:permission=hris.view']);
    $app->get('/hris/positions', 'hris/PositionsController@index', ['PermissionMiddleware:permission=hris.view']);
    $app->get('/hris/appointments', 'hris/AppointmentsController@index', ['PermissionMiddleware:permission=hris.view']);

    $app->get('/hris/leaves', 'hris/LeavesController@index', ['PermissionMiddleware:permission=hris.view']);
    $app->get('/hris/holidays', 'hris/HolidaysController@index', ['PermissionMiddleware:permission=hris.view']);
    $app->get('/hris/shifts', 'hris/ShiftsController@index', ['PermissionMiddleware:permission=hris.view']);


    //ss employees
    $app->get('/hris/employees/getallemployees', 'hris/EmployeeController@getAllEmployees', ['PermissionMiddleware:permission=hris.employee.view']);
    $app->post('/hris/employees/create', 'hris/EmployeeController@create', ['PermissionMiddleware:permission=hris.employee.create']);
    $app->get('/hris/employees/getemployee/{id}', 'hris/EmployeeController@getemployeeById', ['PermissionMiddleware:permission=hris.employee.view']);
    $app->post('/hris/employees/deleteemployee/{id}', 'hris/EmployeeController@deleteEmployee', ['PermissionMiddleware:permission=hris.employee.delete']);
    $app->post('/hris/employees/updateemployee/{id}', 'hris/EmployeeController@updateEmployee', ['PermissionMiddleware:permission=hris.employee.edit']);
    $app->get('/hris/employees/employment-types', 'hris/EmployeeController@getEmploymentTypes');
    $app->get('/hris/employees/employment-statuses', 'hris/EmployeeController@getEmploymentStatuses');

    $app->get('/hris/employees/getcompensation/{id}','hris/EmployeeController@getEmployeeCompensation');
    $app->post('/hris/employees/createcompensation','hris/EmployeeController@createEmployeeCompensation');
    $app->post('/hris/employees/updatecompensation/{id}','hris/EmployeeController@updateEmployeeCompensation');

    //ss organizational units
    $app->get('/hris/offices/getallunits', 'hris/OrgUnitsController@getAllUnits');
    $app->post('/hris/offices/create', 'hris/OrgUnitsController@create');
    $app->post('/hris/offices/getparentid', 'hris/OrgUnitsController@getParentId');
    $app->get('/hris/offices/getunitbyid/{id}', 'hris/OrgUnitsController@getUnitById');
    $app->get('/hris/offices/getunitemployees/{id}', 'hris/OrgUnitsController@getUnitEmployees');
    $app->post('/hris/offices/updateunit/{id}', 'hris/OrgUnitsController@updateOrgUnit');
    $app->post('/hris/offices/deleteunit/{id}', 'hris/OrgUnitsController@deleteOrgUnit');

    //ss branches
    $app->get('/hris/branches/getallbranches', 'hris/BranchController@getAllBranches');
    $app->get('/hris/branches/getbranchbyid/{id}', 'hris/BranchController@getBranchById');
    $app->post('/hris/branches/create', 'hris/BranchController@create');
    $app->post('/hris/branches/updatebranch/{id}', 'hris/BranchController@updateBranch');
    $app->post('/hris/branches/deletebranch/{id}', 'hris/BranchController@deleteBranch');
    //ss positions
    $app->get('/hris/positions/getallpositions', 'hris/PositionsController@getAllPositions');
    $app->get('/hris/positions/getpositionbyid/{id}', 'hris/PositionsController@getPositionById');
    $app->post('/hris/positions/create', 'hris/PositionsController@create');
    $app->post('/hris/positions/update/{id}', 'hris/PositionsController@update');
    $app->post('/hris/positions/delete/{id}', 'hris/PositionsController@delete');

    //appointments 
    $app->get('/hris/appointments/getallappointments', 'hris/AppointmentsController@getAllAppointments');
    $app->get('/hris/appointments/getappointmentbyid/{id}', 'hris/AppointmentsController@getAppointmentById');
    $app->post('/hris/appointments/create', 'hris/AppointmentsController@create');
    $app->post('/hris/appointments/update/{id}', 'hris/AppointmentsController@update');
    $app->post('/hris/appointments/delete/{id}', 'hris/AppointmentsController@delete');


    //ss leaves
    $app->get('/hris/leaves/requests/getall', 'hris/LeavesController@getAllRequests');
    $app->get('/hris/leaves/requests/getbyid/{id}', 'hris/LeavesController@getRequestById');
    $app->post('/hris/leaves/requests/create', 'hris/LeavesController@createRequest');
    $app->post('/hris/leaves/requests/update/{id}', 'hris/LeavesController@updateRequest');
    $app->post('/hris/leaves/requests/delete/{id}', 'hris/LeavesController@deleteRequest');
    $app->get('/hris/leaves/types/getall', 'hris/LeavesController@getAllTypes');
    $app->get('/hris/leaves/types/getbyid/{id}', 'hris/LeavesController@getTypeById');
    $app->post('/hris/leaves/types/create', 'hris/LeavesController@createType');
    $app->post('/hris/leaves/types/update/{id}', 'hris/LeavesController@updateType');
    $app->post('/hris/leaves/types/delete/{id}', 'hris/LeavesController@deleteType');
    $app->get('/hris/leaves/employees/options', 'hris/LeavesController@getEmployees');
    $app->get('/hris/leaves/types/options', 'hris/LeavesController@getTypeOptions');

    //ss holidays
    $app->get('/hris/holidays/getallholidays', 'hris/HolidaysController@getAllHolidays');
    $app->get('/hris/holidays/getholidaybyid/{id}', 'hris/HolidaysController@getHolidayById');
    $app->post('/hris/holidays/create', 'hris/HolidaysController@create');
    $app->post('/hris/holidays/update/{id}', 'hris/HolidaysController@update');
    $app->post('/hris/holidays/delete/{id}', 'hris/HolidaysController@delete');

    //ss shifts
    $app->get('/hris/shifts/getallshifts', 'hris/ShiftsController@getAllShifts');
    $app->get('/hris/shifts/getshiftbyid/{id}', 'hris/ShiftsController@getShiftById');
    $app->post('/hris/shifts/create', 'hris/ShiftsController@create');
    $app->post('/hris/shifts/update/{id}', 'hris/ShiftsController@update');
    $app->post('/hris/shifts/delete/{id}', 'hris/ShiftsController@delete');

    $app->get('/hris/dtr', 'hris/AttendanceLogController@dtr');

    //attendance log
    $app->get('/hris/attendance/test', 'hris/AttendanceLogController@test');
    $app->get('/hris/attendance/{id}/openscan', 'hris/AttendanceLogController@openScanner');

});