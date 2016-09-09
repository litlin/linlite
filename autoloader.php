<?php

namespace linlite;

class MyAutoload {
	// const NAMESPACE_PREFIX = 'Linlite\\';
	/**
	 * 向PHP注册在自动载入函数
	 */
	public static function register() {
		spl_autoload_register ( array (
				new self (),'autoload' 
		) );
	}
	
	/**
	 * 根据类名载入所在文件
	 */
	public static function autoload($className) {
		$namespace = substr ( $className, 0, strrpos ( $className, "\\" ) );
		$prefix = strstr ( $className, "\\", true );
		$basename = substr($className, strrpos ( $className, "\\" )+1 );
		if ($namespace === $prefix) {
			$filename = $basename . ".php";
			if (file_exists ( $filename )) {
				return require $filename;
			} else {
				return false;
			}
		} else {
			$filename ="Application" . str_replace ( "\\", "/", substr ( $namespace, strlen($prefix) ) ) ."/". $basename.".php";
// 			echo $filename;die();
			if (file_exists ( $filename )) {
				return require $filename;
			} else {
				return false;
			}
		}
	}
}
MyAutoload::register ();