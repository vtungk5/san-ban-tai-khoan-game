<?php
namespace App\Models;

use Database\DB;

class User extends DB {

    const table = 'users';

    const fillable = [
        'uid',
        'fullname',
        'username',
        'email',
        'password',
        'money',
        'token',
        'status',
        'level'
    ];

    const dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}