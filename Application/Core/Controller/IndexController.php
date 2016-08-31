<?php

namespace Core\Controller;

use Core\Model\ResponseMsg;

class IndexController {
	public function __construct() {
		if (empty ( $_GET ['echostr'] ) && empty ( $_POST )) {
			echo "welcome!";
		} elseif ($_GET ['echostr']) {
			$this->valid ();
		} else {
			// ResponseMsg::response ();
			
			$postStr = file_get_contents ( 'php://input' );
			if (! empty ( $postStr )) {
				$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
				if ($postObj !== false) {
					$msgType = $postObj->MsgType;
					$fromUser = $postObj->FromUserName;
					$toUser = $postObj->ToUserName;
					$contentStr = "当前时间:\n" . date ( "Y-m-d H:i:s", time () );
					$tpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
					echo sprintf( $tpl, $fromUser, $toUser, time (), $contentStr );
					die();
				}
			}
			echo "";die();
		}
	}
	private function valid() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature()) {
			echo $echoStr;
			die ();
		}
	}
	public function dbTest() {
		die ( "租用数据库服务后设置好参数后再行测试" );
		$dsn = 'mysql:host=w.rdc.sae.sina.com.cn;port=3307;dbname=app_linlite';
		$user = '5n4oxmzmkn';
		$password = '5y25mhhmyhim14530z4ylkwy5mwjxj2k5y4i2y14';
		try {
			$dbh = new \PDO ( $dsn, $user, $password, array (
					\PDO::ATTR_PERSISTENT => true 
			) );
			// $dbh->query ( 'set names utf8;' );
			if ($dbh)
				echo "数据库连接测试成功";
			else
				echo "数据库连接测试不成功";
		} catch ( \PDOException $e ) {
			echo 'Connection failed: ' . $e->getMessage ();
		}
	}
	private function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$token = "linlite";
		$tmpArr = array (
				$token,$timestamp,$nonce 
		);
		sort ( $tmpArr );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}