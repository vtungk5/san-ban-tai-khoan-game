<?php
namespace App\Models;

use Database\DB;

class Service extends DB {

    const table = 'service';

    const fillable = [
        'logo',
        'name',
        'path',
    ];

}