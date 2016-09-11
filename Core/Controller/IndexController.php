<?php

namespace linlite\Core\Controller;

use linlite\Core\Model\ResponseMsg;

class IndexController {
	public function run() {
		if (empty ( $_GET ['echostr'] ) && empty ( $_POST )) {
			echo "welcome!";
		} elseif (isset ( $_GET ['echostr'] )) {
			$this->valid ();
		} else {
			
			// try {
			// $postStr = file_get_contents ( 'php://input' );
			// $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			// // include dirname(__DIR__)."/Model/ResponseMsg.php";
			// $respon = new ResponseMsg();
			// $respon->response($postObj);
			// } catch ( \Exception $e ) {
			// echo "";
			// exit();
			// }
			
// 			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			$postStr = file_get_contents ( 'php://input' );
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			// $fromUser = $postObj->FromUserName;
			// $toUser = $postObj->ToUserName;
			// echo $fromUser;
			// $data = array(
			// 'ToUserName' => $fromUser,
			// 'FromUserName' => $toUser,
			// 'CreateTime' => time(),
			// 'MsgType' => "text",
			// 'Content'=>"当前时间:\n" . date ( "Y-m-d H:i:s", time () ),
			// 'FuncFlag'=>0
			// );
			
			// $xml = new \SimpleXMLElement('<xml></xml>');
			// $this->data2xml($xml, $data);
			// exit($xml->asXML());
			$fromUser = $postObj->FromUserName;
			$toUser = $postObj->ToUserName;
			$contentStr = "当前时间:\n" . date ( "Y-m-d H:i:s", time () );
			$tpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
			$resultStr = sprintf ( $tpl, $fromUser, $toUser, time (), $contentStr );
			exit($resultStr);
		}
	}
	public function valid() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ()) {
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
	private function data2xml($xml, $data, $item = 'item') {
		foreach ( $data as $key => $value ) {
			is_numeric ( $key ) && $key = $item;
			if (is_array ( $value ) || is_object ( $value )) {
				$child = $xml->addChild ( $key );
				$this->data2xml ( $child, $value, $item );
			} else {
				if (is_numeric ( $value )) {
					$child = $xml->addChild ( $key, $value );
				} else {
					$child = $xml->addChild ( $key );
					$node = dom_import_simplexml ( $child );
					$node->appendChild ( $node->ownerDocument->createCDATASection ( $value ) );
				}
			}
		}
	}
	private function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$token = TOKEN;
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