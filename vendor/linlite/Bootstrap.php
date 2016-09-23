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
		$uri = array_filter ( $uri );
		
		while ( true ) {
			if (empty ( $uri ))
				break;
		}
		if (! empty ( $uri )) {
			$path = ucfirst ( strtolower ( array_shift ( $uri ) ) );
			if (is_dir ( "Application/" . $path )) {
				self::$_module = $path;
			} else {
				$className = "linlite\\Home\\Controller\\" . $path . "Controller";
				if (self::checkClass ( $className )) {
					self::$_controller = $path;
				}
			}
			
			empty ( $uri ) || ($controller = ucwords ( strtolower ( array_shift ( $uri ) ) ));
			empty ( $uri ) || ($action = strtolower ( array_shift ( $uri ) ));
			empty ( $uri ) || ($vars = $uri);
		}
		return self::callAction ();
	}
	private static function callAction() {
		$controller = self::$_controller;
		$className = "linlite\\" . self::$_module . "\\Controller\\" . $controller . "Controller";
		if (static::checkClass ( $className )) {
			$clsHandler = new $className ();
			$method = self::$_action;
			if (method_exists ( $clsHandler, $method )) {
				$clsHandler::$method ( self::$_vars );
			} elseif (method_exists ( $clsHandler, "_empty" )) {
				$clsHandler::_empty ( $method );
			} else {
				exit ( "<h2>$controller.php:  $controller::$method Not Found</h2>" );
			}
		} else {
			exit ( "<h2>$controller.php:  $controller Not Found</h2>" );
		}
	}
	private static function checkClass($className) {
		if (class_exists ( $className ))
			return true;
		return false;
	}
	private static function checkAction($controller, $action) {
		if (method_exists ( new $controller (), $action ))
			return true;
		return false;
	}
}