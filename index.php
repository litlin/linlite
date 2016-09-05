<?php

namespace Linlite;

use Linlite\Core\Controller\IndexController;

include 'autoloader.php';

Autoload::register ();

ini_set("display_error", "on");
new IndexController();
// new \Core\Controller\IndexController();
echo "abc";