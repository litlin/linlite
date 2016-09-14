<?php
use linlite\Core\Controller\IndexController;

chdir ( dirname ( __DIR__ ) );

include_once 'config/config.php';
require 'autoloader.php';
// ini_set("display_error", "on");
// session_start();

// $str1=file_get_contents("php://input");
// TestController::run($str1);
IndexController::run ();



