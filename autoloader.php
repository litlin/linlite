<?php

namespace linlite;

class Autoload {
	public static function register() {
		spl_autoload_register ( array (
				new self (),"autoload" 
		), true );
	}
	protected static function autoload($className = "") {
		if (empty ( $className ))
			return false;
		if (($file = __DIR__ . "/" . $className . ".php") && file_exists ( $file )) {
			require_once $file;
		} elseif (($file = __DIR__ . "/vendor/" . $className . ".php") && file_exists ( $file )) {
			require_once $file;
		} elseif (($file = __DIR__ . DIRECTORY_SEPARATOR . "Application" . DIRECTORY_SEPARATOR . str_replace ( "\\", DIRECTORY_SEPARATOR, $className ) . ".php") && file_exists ( $file )) {
			require_once $file;
		} else {
			return false;
		}
	}
}