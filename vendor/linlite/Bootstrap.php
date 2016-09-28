<?php

namespace linlite\linlite;

class Bootstrap {
	private static $_module = "Home";
	private static $_controller = "Index";
	private static $_action = "index";
	private static $_vars;
	public static function start() {
		$uri = explode ( '/', $_SERVER ['REQUEST_URI'] );
		array_shift ( $uri );
		$_SERVER [‘HTTP_APPNAME’] && array_shift ( $uri );
		
		static::defineVars ( $uri );
		static::callAction ();
	}
	private static function defineVars() {
		$vars = ( array ) func_get_arg ( 0 );
		$process = 1;
		while ( $vars ) {
			$path = array_shift ( $vars );
			switch ($process) {
				case 1 :
					if ($path) {
						$path = ucfirst ( strtolower ( $path ) );
						if (is_dir ( "Application" . DIRECTORY_SEPARATOR . $path )) {
							self::$_module = $path;
							$process = 2;
						} else {
							if (count ( $vars ) > 2)
								exit ( "<b>module $path not exists!<br>" );
							goto process2;
						}
					}
					break;
				case 2 :
					process2:
					if ($path) {
						$path = ucwords ( strtolower ( $path ) );
						$className = "linlite\\" . self::$_module . "\\Controller\\" . $path . "Controller";
						if (self::checkClass ( $className )) {
							self::$_controller = $path;
							$process = 3;
						} else {
							if (count ( $vars ) > 1)
								exit ( "<b>Controller $path not exists!</b>" );
							goto process3;
						}
					}
					break;
				case 3 :
					process3:
					if ($path) {
						$path = strtolower ( $path );
						if (self::checkAction ( $path )) {
							self::$_action = $path;
							$process = 4;
						} else {
							if (count ( $vars ) > 0)
								self::$_action = "_empty";
							self::$_vars = $path;
							goto end;
						}
					}
					break;
				case 4 :
				default :
					process4:
					self::$_vars = $path;
					$process ++;
					break;
			}
		}
		end:
	}
	private static function callAction() {
		$controller = self::$_controller;
		$className = "linlite\\" . self::$_module . "\\Controller\\" . $controller . "Controller";
		
		if (static::checkClass ( $className )) {
			$clsHandler = new $className ();
			$method = self::$_action;
			if (method_exists ( $clsHandler, $method )) {
				$clsHandler->$method ( self::$_vars );
			} elseif (method_exists ( $clsHandler, "_empty" )) {
				$clsHandler->_empty ( $method );
			} else {
				exit ( "<h2>action $method not exists!</h2>" );
			}
		} else {
			exit ( "<h2>$controller.php:  $controller Not Found</h2>" );
		}
	}
	private static function checkModule() {
		$module = func_get_arg ( 0 ) ? func_get_arg ( 0 ) : self::$_module;
		if ($module && is_dir ( "Application" . DIRECTORY_SEPARATOR . $module ))
			return true;
		return false;
	}
	private static function checkClass() {
		$className = func_get_arg ( 0 ) ? func_get_arg ( 0 ) : "linlite\\" . self::$_module . "\\Controller\\" . self::$_controller . "Controller";
		$filepath = str_replace ( "linlite", getcwd () . DIRECTORY_SEPARATOR . "Application", str_replace ( "\\", DIRECTORY_SEPARATOR, $className ) ) . ".php";
		if (realpath ( $filepath ) === false) {
			$filepath = str_replace ( getcwd () . DIRECTORY_SEPARATOR . "Application", getcwd () . DIRECTORY_SEPARATOR . "vendor", $filepath );
			if (realpath ( $filepath ) === false)
				return false;
		}
		if (class_exists ( $className ))
			return true;
		return false;
	}
	private static function checkAction() {
		$action = func_get_arg ( 0 ) ? func_get_arg ( 0 ) : self::$_action;
		$controller = "linlite\\" . self::$_module . "\\Controller\\" . self::$_controller . "Controller";
		$clsHandler = new $controller ();
		if (method_exists ( $clsHandler, $action ))
			return true;
		return false;
	}
}