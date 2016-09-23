<?php

namespace linlite\linlite;

abstract class AbstractController {
	public abstract function index();
	public function _empty($action) {
		echo "<h2>action $action not exists!</h2>";
	}
	protected function getGetData() {
		return empty ( $_GET ) ? array () : $_GET;
	}
	protected function getPostData() {
		return file_get_contents ( 'php://input' );
	}
}