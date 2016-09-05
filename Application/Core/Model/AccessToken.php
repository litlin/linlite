<?php

namespace linlite\Core\Model;

class AccessToken {
	private static $appid = "wx2e576dbafca6e32c";
	private static $secret = "ec440a695257e6d50d61eb0d43c6dc61";
	private static $accessToken;
	private static $expiresTime;
	protected static $token = "linlite";
	public static function getAccessToken() {
		if ($_SERVER [‘HTTP_APPNAME’]) {
			if (file_exists ( self::$token . 'access_token.json' )) {
				$res = file_get_contents ( self::$token . 'access_token.json' );
				$result = json_decode ( $res, true );
				$expiresTime = $result [“expires_time”];
				if ($expiresTime > time ()) {
					self::$accessToken = $result [“access_token”];
				}
			} else {
				self::getCurlData ();
				self::saveAccessToken ();
			}
		} else {
			if (class_exists ( "memcache" )) {
				try {	
					$memcache = new \Memcache ();
					$memcache->connect ( "127.0.0.1", 11211 );
					self::$accessToken = $memcache->get ( self::$token . self::$appid );
					if (! self::$accessToken) {
						self::getCurlData ();
						self::saveAccessToken ();
					}
				} catch ( \Exception $error ) {
					// throw $error;
					return false;
				}
			} else {
				echo "class memcache not exists!";
				return false;
			}
		}
		
		return self::$accessToken;
	}
	protected static function saveAccessToken() {
		if ($_SERVER [‘HTTP_APPNAME’]) {
			// try {
			return file_put_contents ( self::$token . 'access_token.json', '{"access_token":"' . self::$access_token . '","expires_time":' . time () + self::$expires_time - 5 . '}' );
			// } catch ( \Exception $error ) {
			// throw $error;
			// }
		} else {
			if (class_exists ( "memcache" )) {
				try {
					$memcache = new \Memcache ();
					$memcache->connect ( "127.0.0.1", 11211 );
					$memcache->set ( self::$token . self::$appid, self::$accessToken, MEMCACHE_COMPRESSED, self::$expiresTime );
					$memcache->close ();
				} catch ( \Exception $error ) {
					// throw $error;
					return false;
				}
			} else {
				echo "class memcache not exists!";
				return false;
			}
		}
		return true;
	}
	protected static function getCurlData() {
		$start = time ();
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::$appid . "&secret=" . self::$secret;
		$output = Curl::callWebServer ( $url );
		self::$accessToken = $output ["access_token"];
		if (! self::$accessToken)
			die ( $output ["errmsg"] );
		self::$expiresTime = $output ["expires_in"] - (time () - $start);
		return self::$accessToken;
	}
}