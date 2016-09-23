<?php

namespace linlite;

class MyAutoload {
	// const NAMESPACE_PREFIX = 'Linlite\\';
	private static $_prefix = "linlite\\";
	private static $_map = array ();
	/**
	 * 向PHP注册在自动载入函数
	 */
	public static function register() {
		self::$_map = include_once 'config/classmap.php';
		if (function_exists ( '__autoload' )) {
			// Register any existing autoloader function with SPL,
			// so we don't get any clashes
			spl_autoload_register ( '__autoload' );
		} else {
			spl_autoload_register ( array (
					'linlite\\MyAutoload','autoload' 
			) );
		}
	}
	
	/**
	 * 根据类名载入所在文件
	 */
	public static function autoload($className) {
		if (class_exists ( $className, false )) {
			return true;
		}
		if (self::$_map [$className])
			return include self::$_map [$className];
		$len = strlen ( self::$_prefix );
		if (strncmp ( self::$_prefix, $className, $len ) === 0) {
			$filePath = str_replace ( "\\", DIRECTORY_SEPARATOR, substr ( $className, $len ) );
		} else {
			$filePath = str_replace ( "\\", DIRECTORY_SEPARATOR, $className );
		}
		
		$path = realpath ( __DIR__ . DIRECTORY_SEPARATOR . "Application" . DIRECTORY_SEPARATOR . $filePath . '.php' );
		if ($path === false)
			$path = realpath ( __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . $filePath . '.php' );
			// set_exception_handler ( array(new self(),"handleException"));
		if ($path !== false) {
			$filePath = $path;
		} else {
			$filePath .= ".php";
		}
		if (static::checkFile ( $filePath )) {
			include $filePath;
		} else {
			throw static::handleException ( "file not exists!" );
		}
		if (! class_exists ( $className, false )) {
			throw static::handleException ( "class not exists!" );
		}
		return true;
	}
	private static function checkFile($filePath) {
		if (file_exists ( $filePath ) && is_readable ( $filePath )) {
			return true;
		}
		return false;
	}
	private static function handleException($e) {
		echo '<b>发生错误:</b><br>' . $e;
		exit ();
	}
}

MyAutoload::register ();