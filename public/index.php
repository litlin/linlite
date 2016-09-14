<?php
use linlite\Core\Controller\IndexController;
use linlite\Core\Controller\TestController;

chdir ( dirname ( __DIR__ ) );

include_once 'config/config.php';
require 'autoloader.php';
// ini_set("display_error", "on");
// session_start();

$str1=file_get_contents("php://input");
TestController::run($str1);
$c = new IndexController ();
if (isset ( $_GET ['echostr'] )) {
	$c->valid ();
} else {
	$c->response ();
}



