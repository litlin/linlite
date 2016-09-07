<?php

namespace linlite;

use linlite\Core\Controller\IndexController;

include_once __DIR__ . '/config/config.php';
require __DIR__ . '/autoloader.php';

// ini_set("display_error", "on");

$c = new IndexController();
$c->go();

