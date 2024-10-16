<?php
$public_dir=__DIR__.'/public';

// serve existing files as-is
if (file_exists(__DIR__.$_SERVER['REQUEST_URI']))
    return FALSE;

if (file_exists(__DIR__.$_SERVER['REQUEST_URI'].'.php'))
    return FALSE;

// Pass the request to index.php
$_SERVER['SCRIPT_NAME']='index.php';
require(__DIR__.'/index.php');
