<?php

namespace linlite\linlite;

abstract class AbstractController {
	protected $dataFromGet;
	protected $dataFromPost;
	public abstract function index();
	public function _empty() {
		$accessMsg = func_get_arg ( 0 );
		if (is_string ( $accessMsg )) {
			echo "<h2>action $accessMsg not exists!</h2>";
		} else {
			echo "<h2>this is empty action's response</h2>";
		}
	}
	protected function getGetData() {
		$this->dataFromGet = empty ( $_GET ) ? array () : $_GET;
		return $this->dataFromGet;
	}
	protected function getPostData() {
		$this->dataFromPost = file_get_contents ( 'php://input' );
		return $this->dataFromPost;
	}
}