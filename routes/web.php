<?php
/*

Notes:
AuthMiddleware = login
TenantMiddleware = Tenant is Active/Inactive
ModuleMiddleware = Tenant have module subscriptions

*/
// routes files
// require 'locations-routes.php';
require 'hris-routes.php';
require 'payroll-routes.php';

///// Public pages (no login required)

// SUPER ADMIN Account MIGRATION MODULE - disable after use
// $app->get('/migrate/sadmin', 'MigrationController@sadmin');
// $app->post('/migrate/sadmin', 'MigrationController@sadmin');

// login page
$app->get('/', 'AuthController@login');
$app->post('/', 'AuthController@loginPost');
$app->get('/logout', 'AuthController@logout');

// Protected routes (login required)
$app->group(['AuthMiddleware','TenantMiddleware'], function ($app) {
    ///// Web pages 
    $app->get('/home', 'ModuleController@index');
    $app->get('/about', 'ModuleController@about');

    $app->get('/users', 'UserController@index', ['PermissionMiddleware:permission=user.view']);
    $app->get('/tenants', 'TenantController@index', ['PermissionMiddleware:permission=tenant.view']);
    $app->get('/branches', 'hris/BranchController@main');

    $app->get('/getAllTenantUsers', 'UserController@getAllTenantUsers', ['PermissionMiddleware:permission=user.view']);
    $app->post('/addUser', 'UserController@addUser', ['PermissionMiddleware:permission=user.add']);
    $app->get('/editUser', 'UserController@editUser', ['PermissionMiddleware:permission=user.edit']);
    $app->get('/editUserRoles', 'UserController@editUserRoles', ['PermissionMiddleware:permission=user.edit']);
    $app->get('/editUserBranch', 'UserController@editUserBranch', ['PermissionMiddleware:permission=user.edit']);
    // $app->get('/countUserBranches', 'UserController@countUserBranches', ['PermissionMiddleware:permission=user.edit']);
    $app->get('/getUserBranchById', 'UserController@getUserBranchById', ['PermissionMiddleware:permission=user.edit']);

    $app->get('/getTenants', 'TenantController@getTenants', ['PermissionMiddleware:permission=tenant.view']);

    $app->group(['ModuleMiddleware:module=HRIS','PermissionMiddleware:permission=hris.view'], function ($app) {
        $app->get('/hris', 'ModuleController@hris');
    });

    //$app->get('/payroll', 'ModuleController@payroll');
    $app->group(['ModuleMiddleware:module=PAYROLL','PermissionMiddleware:permission=payroll.view'], function ($app) {
        $app->get('/payroll', 'ModuleController@payroll');
    });

    $app->post('/keepalive', 'AuthController@keepAlive');
});

// permission based access
/*
$app->group(['AuthMiddleware', 'TenantMiddleware', 'PermissionMiddleware:permission=user.view'], function ($app) {
    $app->get('/users', 'UserController@users');
});

*/


