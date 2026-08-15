<?php
// YODY DEV
session_start();
require_once __DIR__ . '/bin.php';

Bin::load([
    '/vendor/autoload.php',
])::run()::direct([
    'config',
    'app',
    'routes',
]);
