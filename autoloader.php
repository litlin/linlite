<?php

namespace linlite;

class MyAutoload {
	// const NAMESPACE_PREFIX = 'Linlite\\';
	private static $prefix = "linlite\\";
	/**
	 * 向PHP注册在自动载入函数
	 */
	public static function register() {
		set_exception_handler ( "linlite\\MyAutoload::handleException" );
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
		$filename = str_replace ( "\\", DIRECTORY_SEPARATOR, strstr ( $className, static::$prefix ) ? substr ( $className, strlen ( static::$prefix ) ) : $className ) . ".php";
		(static::checkFile ( $filename ) && require $filename) || exit ( "file " . $filename . " not exists" );
		class_exists ( $className, false ) || exit ( "class " . $className . " not exists" );
	}
	private static function checkFile($filename) {
		if (file_exists ( $filename ) && is_readable ( $filename )) {
			return true;
		}
		return false;
	}
	public static function handleException($exception) {
		echo '发生错误:' . $exception;
	}
}
MyAutoload::register ();