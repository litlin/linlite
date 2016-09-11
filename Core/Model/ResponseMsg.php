<?php

namespace linlite\Core\Model;

class ResponseMsg {
	public function response(\SimpleXMLElement $postObj) {
		$msgType = $postObj->MsgType;
		$fromUser = $postObj->FromUserName;
		$toUser = $postObj->ToUserName;
		// include "Curl.php";
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
				$keyWord = trim ( $postObj->Content );
				switch (true) {
					case $keyWord == "?" || $keyWord == "？" || preg_match ( "/[当前|现在|目前]?(?=时间)/u", $keyWord ) :
						$contentStr = "当前时间:\n" . date ( "Y-m-d H:i:s", time () );
						break;
					case preg_match ( "/[\x{4e00}-\x{9fa5}]{2,3}(?=图片|图文)/u", $keyWord ) :
						$resultStr = sprintf ( $this->getTpl ( "news", 1 ), $fromUser, $toUser, time (), "描述信息", "测试图文格式--标题", "测试链接为必应中国网址", "http://mmbiz.qpic.cn/mmbiz/LhuPjPp9Ry8yeKvmr6AqLCagF0vVKAuhe9cvKibY0Xw78WNficH84fou6HD5V8khgct6dp3ibSJbLVViba6LXugZRg/0", "http://cn.bing.com" );
						break;
					case preg_match ( "/^[\x{4e00}-\x{9fa5}]{0,2}(?=音乐$|music$)/u", $keyWord ) :
						$title = "Shatter Me";
						$description = "Lindsey Stirling Lzzy Hale";
						$html = Curl::getHeader ( "https://od.lk/s/NzZfMzY5ODk2OV8/Lindsey%20Stirling%20Lzzy%20Hale%20-%20Shatter%20Me.mp3" );
						
						preg_match ( '/^Location:\s(https?.*)$/m', $html, $match );
						$musicUrl = $match [1];
						$HQMusicUrl = $musicUrl;
						$resultStr = sprintf ( $this->getTpl ( "music" ), $fromUser, $toUser, time (), $title, $description, $musicUrl, $HQMusicUrl );
						break;
					case preg_match ( '/^添加按钮$/u', $keyWord ) :
						$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . AccessToken::getAccessToken ();
						$button = urldecode ( json_encode ( array (
								"button" => array (
										array (
												"name" => urlencode ( "菜单" ),
												"sub_button" => array (
														array (
																"name" => urlencode ( "搜索" ),"type" => "view","url" => "http://cn.bing.com" 
														),array (
																"name" => urlencode ( "视频" ),"type" => "view","url" => "http://v.qq.com" 
														) 
												) 
										) 
								) 
						) ) );
						$html = Curl::callWebServer ( $url, $button, 'post' );
						$contentStr = implode ( " ", $html );
						break;
					case preg_match ( '/^删除按钮$/u', $keyWord ) :
						$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . AccessToken::getAccessToken ();
						$html = Curl::callWebServer ( $url );
						$contentStr = implode ( " ", $html );
						break;
					// case (preg_match ( "/^\bget\b\s\bkey\b$/", $keyWord ) && !$_SERVER [‘HTTP_APPNAME’]) :
					// $contentStr = AccessToken::getAccessToken ();
					// break;
					default :
						$contentStr = "针对信息\"" . $keyWord . "\"的回应";
						break;
				}
				break;
			default :
				$contentStr = "发送信息为：\n";
				foreach ( $postObj as $k => $v ) {
					$contentStr .= "键：" . $k . " 值：" . $v . "\n";
				}
				break;
		}
		if (! isset ( $resultStr )) {
			$resultStr = sprintf ( $this->getTpl ( "text" ), $fromUser, $toUser, time (), $contentStr );
		}
		echo $resultStr;
	}
	private function getTpl($msgType = "", $ArticleCount = 1) {
		$msgType = strtolower ( $msgType );
		$tpl = "";
		switch ($msgType) {
			case "shortvideo" :
				/*
				 * 
 参数 	是否必须 	说明
ToUserName 	是 	接收方帐号（收到的OpenID）
FromUserName 	是 	开发者微信号
CreateTime 	是 	消息创建时间 （整型）
MsgType 	是 	video 经过测试应该为shortvideo
MediaId 	是 	通过素材管理接口上传多媒体文件，得到的id
Title 	否 	视频消息的标题
Description 	否 	视频消息的描述 
				 */
				$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[shortvideo]]></MsgType>
						<Video>
						<MediaId><![CDATA[%s]]></MediaId>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						</Video> 						
						</xml>";
				break;
			case "music" :
				/*
				 * 
 参数 	是否必须 	说明
ToUserName 	是 	接收方帐号（收到的OpenID）
FromUserName 	是 	开发者微信号
CreateTime 	是 	消息创建时间 （整型）
MsgType 	是 	music
Title 	否 	音乐标题
Description 	否 	音乐描述
MusicURL 	否 	音乐链接
HQMusicUrl 	否 	高质量音乐链接，WIFI环境优先使用该链接播放音乐
ThumbMediaId 	否 	缩略图的媒体id，通过素材管理接口上传多媒体文件，得到的id 
				 */
				$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[music]]></MsgType>
						<Music>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						<MusicUrl><![CDATA[%s]]></MusicUrl>
						<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
						</Music>
						</xml>";
				break;
			case "voice" :
				/*
				 * 
参数 	是否必须 	说明
ToUserName 	是 	接收方帐号（收到的OpenID）
FromUserName 	是 	开发者微信号
CreateTime 	是 	消息创建时间戳 （整型）
MsgType 	是 	语音，voice
MediaId 	是 	通过素材管理接口上传多媒体文件，得到的id 
				 * 
				 */
				$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[voice]]></MsgType>
						<Voice>
						<MediaId><![CDATA[%s]]></MediaId>
						</Voice>
						</xml>";
				break;
			case "image" :
				/*
				 * 
参数 	是否必须 	说明
ToUserName 	是 	接收方帐号（收到的OpenID）
FromUserName 	是 	开发者微信号
CreateTime 	是 	消息创建时间 （整型）
MsgType 	是 	image
MediaId 	是 	通过素材管理接口上传多媒体文件，得到的id。 
				 * 
				 */
				$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[image]]></MsgType>
						<Image>
						<MediaId><![CDATA[%s]]></MediaId>
						</Image>
						</xml>";
				break;
			case "news" :
				/*
				 * 
 参数 	是否必须 	说明
ToUserName 	是 	接收方帐号（收到的OpenID）
FromUserName 	是 	开发者微信号
CreateTime 	是 	消息创建时间 （整型）
MsgType 	是 	news
ArticleCount 	是 	图文消息个数，限制为10条以内
Articles 	是 	多条图文消息信息，默认第一个item为大图,注意，如果图文数超过10，则将会无响应
Title 	否 	图文消息标题
Description 	否 	图文消息描述
PicUrl 	否 	图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
Url 	否 	点击图文消息跳转链接 
				 * 
				 */
				$ArticleCount = ( int ) $ArticleCount < 1 ? 1 : ( int ) $ArticleCount;
				$ArticleCount = $ArticleCount > 10 ? 10 : $ArticleCount;
				$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>" . $ArticleCount . "</ArticleCount>
						<Articles>";
				for($i = 0; $i < $ArticleCount; $i ++) {
					$tpl .= "
							<item>
							<Title><![CDATA[%s]]></Title> 
							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
							<Url><![CDATA[%s]]></Url>
							</item>";
				}
				$tpl .= "
						</Articles>
						</xml> 
			";
				break;
			case "text" :
			default :
				$tpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
				
				// "<xml>
				// <ToUserName><![CDATA[%s]]></ToUserName>
				// <FromUserName><![CDATA[%s]]></FromUserName>
				// <CreateTime>%s</CreateTime>
				// <MsgType><![CDATA[text]]></MsgType>
				// <Content><![CDATA[%s]]></Content>
				// <FuncFlag>0</FuncFlag>
				// </xml>";
				break;
		}
		return $tpl;
	}
}