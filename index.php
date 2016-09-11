<?php

namespace linlite;

use linlite\Core\Controller\IndexController;

include_once 'config/config.php';
require 'autoloader.php';

// ini_set("display_error", "on");
// include __DIR__."/Application/Core/Controller/IndexController.php";
$c = new IndexController();
$c->run();

