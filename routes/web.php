<?php
use MiladRahimi\PhpRouter\Router;

$Router = Router::create();


$Router->get('/', [App\Controllers\Client\PAGES::class, 'index']);
$Router->get('/search', [App\Controllers\Client\PAGES::class, 'search']);
$Router->get('/payment', [App\Controllers\Client\PAGES::class, 'payment']);

$Router->get('/register', [App\Controllers\Client\PAGES::class, 'register']);
$Router->get('/login', [App\Controllers\Client\PAGES::class, 'login']);

$Router->get('/logout', [App\Controllers\Client\PAGES::class, 'logout']);

$Router->get('/info/profile', [App\Controllers\Client\PAGES::class, 'profile']);
$Router->get('/info/security', [App\Controllers\Client\PAGES::class, 'security']);
$Router->get('/info/history', [App\Controllers\Client\PAGES::class, 'history']);

$Router->post('/api/login', [App\Controllers\Client\API::class, 'login']);
$Router->post('/api/register', [App\Controllers\Client\API::class, 'register']);

$Router->post('/api/info/change-profile', [App\Controllers\Client\API::class, 'changeinfo']);
$Router->post('/api/info/change-password', [App\Controllers\Client\API::class, 'changepassword']);


$Router->get('/admin/', [App\Controllers\Admin\PAGES::class, 'index']);
$Router->get('/admin/setting', [App\Controllers\Admin\PAGES::class, 'setting']);
$Router->get('/admin/service/list', [App\Controllers\Admin\PAGES::class, 'service']);
$Router->get('/admin/service/add', [App\Controllers\Admin\PAGES::class, 'add_service']);
$Router->get('/admin/service/{id}/edit', [App\Controllers\Admin\PAGES::class, 'edit_service']);
$Router->get('/admin/account/list', [App\Controllers\Admin\PAGES::class, 'account']);
$Router->get('/admin/account/add', [App\Controllers\Admin\PAGES::class, 'add_account']);
$Router->get('/admin/account/{id}/edit', [App\Controllers\Admin\PAGES::class, 'edit_account']);
$Router->get('/admin/product/list', [App\Controllers\Admin\PAGES::class, 'product']);
$Router->get('/admin/product/add', [App\Controllers\Admin\PAGES::class, 'add_product']);

$Router->get('/admin/users/{type}', [App\Controllers\Admin\PAGES::class, 'users']);
$Router->get('/admin/users/{type}/{status}', [App\Controllers\Admin\PAGES::class, 'users']);
$Router->get('/admin/users/{type}/{uid}/edit', [App\Controllers\Admin\PAGES::class, 'edit_users']);

$Router->post('/api/admin/setting/update', [App\Controllers\Admin\API::class, 'Update_Setting']);
$Router->get('/api/admin/users/{uid}/toggle-status', [App\Controllers\Admin\API::class, 'Toggle_Users_Status']);
$Router->get('/api/admin/users/{uid}/delete', [App\Controllers\Admin\API::class, 'Delete_Users']);
$Router->post('/api/admin/users/{uid}/edit', [App\Controllers\Admin\API::class, 'Update_Users']);
$Router->post('/api/admin/service/{id}/edit', [App\Controllers\Admin\API::class, 'Edit_Service']);
$Router->get('/api/admin/service/{id}/delete', [App\Controllers\Admin\API::class, 'Delete_Service']);
$Router->post('/api/admin/service/add', [App\Controllers\Admin\API::class, 'Add_Service']);


$Router->post('/api/admin/account/{id}/edit', [App\Controllers\Admin\API::class, 'Edit_Account']);
$Router->get('/api/admin/account/{id}/delete', [App\Controllers\Admin\API::class, 'Delete_Account']);
$Router->post('/api/admin/account/add', [App\Controllers\Admin\API::class, 'Add_Account']);

Bin::Router($Router);
