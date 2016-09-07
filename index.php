<?php

namespace linlite;

use linlite\Core\Controller\IndexController;

include_once __DIR__ . '/config/config.php';
// require __DIR__ . '/autoloader.php';

// ini_set("display_error", "on");
include __DIR__."/Application/Core/Controller/IndexController.php";
$c = new IndexController();
$c->go();

