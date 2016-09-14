<?php

namespace linlite\Core\Controller;

use linlite\Core\Model\ResponseMsg;

class TestController {
	public static function run($str1 = "") {
		if (isset ( $_GET ['echostr'] )) {
			(new ResponseMsg())->valid ();
		} else {
			(new IndexController())->response();
// 			$str2 = file_get_contents ( "php://input" );
// 			if (! empty ( $str2 )) {
// 				$postObj = simplexml_load_string ( $str2, 'SimpleXMLElement', LIBXML_NOCDATA );
// 				if ($postObj !== false) {
// 					$fromUser = $postObj->FromUserName;
// 					$toUser = $postObj->ToUserName;
// 					$tpl = "<xml>
//                         <ToUserName><![CDATA[%s]]></ToUserName>
//                         <FromUserName><![CDATA[%s]]></FromUserName>
//                         <CreateTime>%s</CreateTime>
//                         <MsgType><![CDATA[text]]></MsgType>
//                         <Content><![CDATA[%s]]></Content>
//                         <FuncFlag>0</FuncFlag>
//                         </xml>";
// 					if ($str1 == $str2) {
// 						$resultStr = sprintf ( $tpl, $fromUser, $toUser, time (), "yeah" );
// 					} else {
// 						$resultStr = sprintf ( $tpl, $fromUser, $toUser, time (), "no" );
// 					}
// 					exit ( $resultStr );
// 				}
// 			}

			exit ( "" );
		}
	}
}