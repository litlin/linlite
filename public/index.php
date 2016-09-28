<?php


use linlite\linlite\Bootstrap;

chdir ( dirname ( __DIR__ ) );

include_once 'config/config.php';
require 'autoloader.php';
// ini_set("display_errors", "on");
// session_start();

Bootstrap::start();



