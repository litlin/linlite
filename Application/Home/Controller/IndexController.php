<?php

namespace linlite\Home\Controller;

use linlite\linlite\AbstractController;

class IndexController extends AbstractController {
	public function index() {
		$input = file_get_contents ( "php://input" );
		if (empty ( $input ))
			echo "<h1>welcome!</h1>";
		else
			print_r ( $input );
	}
}