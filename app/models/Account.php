<?php
namespace App\Models;

use Database\DB;

class Account extends DB {

    const table = 'account';

    const fillable = [
        'name',
    ];

}