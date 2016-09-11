<?php

namespace linlite;

class MyAutoload {
	// const NAMESPACE_PREFIX = 'Linlite\\';
	private static $prefix = "linlite\\";
	/**
	 * 向PHP注册在自动载入函数
	 */
	public static function register() {
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
		$len = strlen ( self::$prefix );
		if (strncmp ( self::$prefix, $className, $len ) === 0) {
			$filePath = str_replace ( "\\", DIRECTORY_SEPARATOR, substr ( $className, $len ) );
		} else {
			$filePath = str_replace ( "\\", DIRECTORY_SEPARATOR, $className );
		}
		$filePath = realpath ( __DIR__ . (empty ( $filePath ) ? '' : DIRECTORY_SEPARATOR) . $filePath . '.php' );
		
		// set_exception_handler ( array(new self(),"handleException"));
		if (static::checkFile ( $filePath )) {
			require $filePath;
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