<?php

// /**
//  * Here is the serverless function entry
//  * for deployment with Vercel.
//  */
// require __DIR__.'/../public/index.php';

ini_set('display_errors', '0');
error_reporting(E_ALL);

$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTPS'] = 'on';

// Fix APP_ENV
putenv('APP_ENV='.($_ENV['APP_ENV'] ?? 'production'));

require __DIR__.'/../public/index.php';
