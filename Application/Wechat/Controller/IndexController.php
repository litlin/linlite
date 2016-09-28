<?php

namespace linlite\Wechat\Controller;

use linlite\linlite\AbstractController;
use linlite\Wechat\Model\ResponseMsg;

class IndexController extends AbstractController {
	public function index() {
		if (isset ( $this->getGetData () ['echostr'] )) {
			(new ResponseMsg ())->valid ( $this->dataFromGet );
		} elseif (! empty ( $this->getPostData () )) {
			(new ResponseMsg ())->response ( $this->dataFromPost );
		}
		exit ( "<h1>this module work for wechat request!</h1>" );
	}
}