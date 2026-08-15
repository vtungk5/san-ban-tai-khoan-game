<?php
namespace App\Models;

use Database\DB;

class Setting extends DB {

    const table = 'setting';

    const fillable = [
        'title',
        'description',
        'keywords',
        'partner_id',
        'partner_key',
        'signature'
    ];

    const dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}