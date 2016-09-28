<?php


use linlite\linlite\Bootstrap;

chdir ( dirname ( __DIR__ ) );

include_once 'config/config.php';
require 'autoloader.php';
// ini_set("display_error", "on");
// session_start();

// $str1=file_get_contents("php://input");
// TestController::run($str1);
var_dump($_SERVER);
Bootstrap::start();



