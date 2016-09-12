<?php

namespace linlite\Core\Controller;

class IndexController {
	public function run() {
		if (empty ( $_GET ['echostr'] ) && empty ( $_POST )) {
			echo "welcome!";
		} else {
			if (isset ( $_GET ['echostr'] )) {
				$this->valid ();
			} else {
				// $postStr = file_get_contents ( 'php://input' );
				// $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
				// // include dirname(__DIR__)."/Model/ResponseMsg.php";
				// $respon = new ResponseMsg ();
				// $respon->response ( $postObj );
				
				// $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
				$postStr = file_get_contents ( 'php://input' );
				
				if (! empty ( $postStr )) {
					$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
					if ($postObj !== false) {
						$msgType = $postObj->MsgType;
						$fromUsername = $postObj->FromUserName;
						$toUsername = $postObj->ToUserName;
						$time = time ();
						$tpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
						$resultType = "text";
						switch ($msgType) {
							case "image" :
								$contentStr = "发送类型为图片,url地址为：" . $postObj->PicUrl . "媒体ID为：" . $postObj->MediaId;
								break;
							case "voice" :
								$contentStr = "发送类型为语音,格式为：" . $postObj->Format . "媒体ID为：" . $postObj->MediaId;
								break;
							/**
							 * 经过测试目前为小视频
							 */
							case "shortvideo" :
								$contentStr = "发送类型为视频,媒体ID为：" . $postObj->MediaId . "缩略图ID为：" . $postObj->ThumbMediaId;
								break;
							case "location" :
								$contentStr = "发送类型为位置：" . $postObj->Label . "坐标为：X:" . $postObj->Location_X . "Y：" . $postObj->Location_Y . "缩放级别：" . $postObj->Scale;
								break;
							case "link" :
								$contentStr = "发送类型为链接,标题为：" . $postObj->Title . "图文消息描述：" . $postObj->Description . "图文消息链接：" . $postObj->Url;
								break;
							case "text" :
								$keyword = trim ( $postObj->Content );
								if ($keyword == "?" || $keyword == "？" || preg_match ( "/[当前|现在|目前]?(?=时间)/u", $keyword )) {
									$contentStr = "当前时间:\n" . date ( "Y-m-d H:i:s", time () );
								} else {
									$contentStr = "针对信息\"" . $keyword . "\"的回应";
								}
								break;
							default :
								$contentStr = "发送信息为：\n";
								foreach ( $postObj as $k => $v ) {
									$contentStr .= "键：" . $k . " 值：" . $v . "\n";
								}
								break;
						}
						if (! isset ( $resultStr ))
							$resultStr = sprintf ( $tpl, $fromUsername, $toUsername, $time, $resultType, $contentStr );
						echo $resultStr;
					}
				} else {
					echo "";
					exit ();
				}
			}
		}
	}
	public function valid() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ()) {
			echo $echoStr;
			die ();
		}
	}
	public static function dbTest() {
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