<?php

ini_set('register_globals', 'Off');
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL | E_STRICT);
$hard_debug = true;

if ($hard_debug===true) {
	function exception_error_handler($errno, $errstr, $errfile, $errline ) {
		ob_end_clean();
		
		switch ($errno) {
			case 1:     $e_type = 'E_ERROR'; $exit_now = true; break;
			case 2:     $e_type = 'E_WARNING'; break;
			case 4:     $e_type = 'E_PARSE'; break;
			case 8:     $e_type = 'E_NOTICE'; break;
			case 16:    $e_type = 'E_CORE_ERROR'; $exit_now = true; break;
			case 32:    $e_type = 'E_CORE_WARNING'; break;
			case 64:    $e_type = 'E_COMPILE_ERROR'; $exit_now = true; break;
			case 128:   $e_type = 'E_COMPILE_WARNING'; break;
			case 256:   $e_type = 'E_USER_ERROR'; $exit_now = true; break;
			case 512:   $e_type = 'E_USER_WARNING'; break;
			case 1024:  $e_type = 'E_USER_NOTICE'; break;
			case 2048:  $e_type = 'E_STRICT'; break;
			case 4096:  $e_type = 'E_RECOVERABLE_ERROR'; $exit_now = true; break;
			case 8192:  $e_type = 'E_DEPRECATED'; break;
			case 16384: $e_type = 'E_USER_DEPRECATED'; break;
			case 30719: $e_type = 'E_ALL'; $exit_now = true; break;
			default:    $e_type = 'E_UNKNOWN'; break;
		  }
		
		die('<b>'.$e_type."</b>\n<br />\n".$errstr."\n<br />\n in ".$errfile."\n<br />\n on line <b>".$errline.'</b>');
		//throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	ob_start();
	set_error_handler("exception_error_handler");
}

require('eParams.php');
require('eUser.php');
require('eFile.php');
require('eSQL.php');
require('eText.php');
require('eTools.php');
require('eLang.php');
require('eMail.php');

class eMain {
	
	private static $error = array();
	
	public static $sql = null;
	
	private static $root_url = '';
	private static $cur_page_url = '';
	private static $cur_folder_url = '';
	
	public static function start_app() {
		
		session_start();
		
		self::$sql = new eSQL();
		eMain::$sql->connect_sql(eParams::$sql_user, eParams::$sql_host, eParams::$sql_password, eParams::$sql_database, eParams::$sql_port);
		
		eLang::set_lang();
		
		eUser::getInstance()->init();
		
		eTools::shorturl();
		
		/*
		echo dirname(__FILE__)."<br />";
		echo __DIR__."<br />";
		echo $_SERVER['DOCUMENT_ROOT']."<br />";		
		echo dirname($_SERVER['SCRIPT_NAME']);
		*/
		if (isset(eParams::$custom_api)) {
			require(__DIR__.'/../'.eParams::$custom_api);
		}
	}
	public static function end_app() {
		eMain::$sql->disconnect_sql();
		
		if (isset(eParams::$stats)) {
			require('addons/eStats.php');
		}
	}
	
	public static function add_error($error_msg) {		
		eMain::$error[] = $error_msg;
	}
	
	public static function encrypt($string, $coef = 0) {	
		if ($coef==0) { $coef = rand(10,99); }
		for ($i=0;$i<$coef;$i++) {
			$string = sha1($string);
			$last3 = substr($string, strlen($string)-3);
			$string = $last3[0] . substr($string, 0, intval(strlen($string)/2)) . $last3[1] . substr($string, intval(strlen($string)/2)) . $last3[2];
		}
		$array['coef'] = $coef;
		$array['encrypted'] = $string;

		return $array;
	}
	
	public static function show_errors($html = true, $translate = true) {
		for ($i=0;$i<count(eMain::$error);$i++) {
			
			$error_msg = eMain::$error[$i];
			if ($translate) { $error_msg = eLang::translate($error_msg); }
			
			if ($i>0) {
				$error_msg = "\n".$error_msg;
				if ($html) {
					$error_msg = "<br />\n".$error_msg;
				}
			}
			
			echo $error_msg;
		}
	}
	public static function get_errors_nb() {
		return count(self::$error);
	}
		
	public static function cur_page_url($clean_get = false, $clean_last_slash = false, $add_last_slash = false) {
		
		if (!empty(self::$cur_page_url) && !$clean_get) { return self::$cur_page_url; }

		if (strpos($_SERVER["REQUEST_URI"], "http")===false) {
			 
			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$pageURL = $protocol;
			if ($_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
		 
		} else {
			$pageURL = $_SERVER["REQUEST_URI"];
		}
			 
		if ($clean_get && strpos($pageURL, "?")!==false) {
			$pageURL = substr($pageURL, 0, strpos($pageURL, "?"));
		}
		if ($clean_last_slash && strrpos($pageURL, "/")==strlen($pageURL)-1) {
			$pageURL = substr($pageURL, 0, strlen($pageURL)-1);
		}
		if ($add_last_slash && strrpos($pageURL, "/")!=strlen($pageURL)-1) {
			$pageURL .= '/';
		}

		self::$cur_page_url = $pageURL;

		return $pageURL;
	}
	
	public static function cur_folder_url() {
		
		if (!empty(self::$cur_folder_url)) { return self::$cur_folder_url; }
		
		$currentURL = self::cur_page_url();		
		$folder_url = substr($currentURL, 0, strrpos($currentURL, "/")+1);
		
		self::$cur_folder_url = $folder_url;
		
		return $folder_url;
		
	}
	
	public static function root_url() {
		
		if (!empty(self::$root_url)) { return self::$root_url; }
		
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$folderURL = $protocol.$_SERVER["SERVER_NAME"].(substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"],"/")+1));
		
		self::$root_url = $folderURL;
		
		return $folderURL;
	}
	
}
?>