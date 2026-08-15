<?php
use App\Models\Setting;
use Database\DB;

class Site
{

    public static function get($value)
    {
        return Setting::first()->$value;
    }
}



