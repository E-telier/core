<?php
class eLang {
			
	public static function set_lang($select_lang = false, $redirect = false) {
				
		if (isset($_GET['lang'])) {
			$select_lang = $_GET['lang'];
		}
		
		if (!isset($_SESSION['lang']) || $_SESSION['lang']!=$select_lang) {
			if ($select_lang!=false && $select_lang!='') {	
				// SET SELECTED //
							
				if (!in_array($select_lang, eParams::$available_languages)) {
					// language not available
					$select_lang = self::get_default();
									
					if ($redirect) {
						self::redirect($select_lang);
					}
				}
				
				$_SESSION['lang'] = $select_lang;
				unset($_SESSION["translation"]);

				setcookie('lang', $select_lang, time()+60*60*24*365);
			} else if ($redirect) {
				// language not in URL
				$select_lang = self::get_default();
									
				if ($redirect) {
					self::redirect($select_lang);
				}
			}
		}
		
		if (!isset($_SESSION['lang'])) {
			// SET AUTO //			
			$_SESSION['lang'] = self::get_default();			
		}

		if (!isset($_SESSION["translation"])) {
			eLang::loadLanguage();				
		}
	}
	
	private static function redirect($lang_url) {
		$root_url = eMain::root_url();
		$query_url = str_replace($root_url, '', eMain::cur_page_url());
		
		$redirect = $root_url.$lang_url.'/'.$query_url;
		
		http_response_code(307);
		header('Location:'.$redirect);
		die();
	}
	
	private static function get_default() {
			
		$default = eParams::$default_lang;
			
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$languages = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$language = substr(strtolower($languages[0]), 0, 2);

			if (in_array($language, eParams::$available_languages)) {
				$default = $language;
			} else {
				$default = eParams::$default_lang;
			}
			
		} 
		
		return $default;
	}
	
	private static function loadLanguage() {
						
		$_SESSION["translation"] = array();

		$rq = "SELECT id, reference, ".$_SESSION['lang']." FROM ".eParams::$prefix."_dictionnary;";
		$lang_datas = eMain::$sql->sql_to_array($rq);
		for ($i=0;$i<$lang_datas['nb'];$i++) {
			$reference = $lang_datas['datas'][$i]['reference'];
			$reference = str_replace("\n", '\n', $reference);			
			$_SESSION["translation"][$reference] = $lang_datas['datas'][$i][$_SESSION['lang']];
		}
		
	}
	
	public static function translate($ref, $transform="") {

		$translation = $ref;
		
		$ref = eText::iso_strtolower($ref);
		
		if (array_key_exists($ref, $_SESSION["translation"])) {
			//echo "isset ".$ref.'<br />';
			if (!empty($_SESSION["translation"][$ref])) {
				//echo "!empty ".$ref.'<br />';
				$translation = $_SESSION["translation"][$ref];
			}
		} else {

			$rq = "INSERT into ".eParams::$prefix."_dictionnary (reference) VALUES ('".str_replace("'", "\'", $ref)."');";
			if (!eMain::$sql->sql_query($rq)) { echo eMain::$sql->get_error(); }

			$_SESSION["translation"][$ref] = "";

		}

		if (!empty($transform)) {
			if ($transform=="strtoupper") {
				$translation = eText::iso_strtoupper($translation);
			} else if ($transform=="ucfirst") {
				$translation = eText::iso_ucfirst($translation);
			} else {
				$translation = call_user_func($transform, $translation);
			}
		}

		return $translation;

	}
	public static function show_translate($ref, $transform='ucfirst', $iso=true) {
		
		$translation = self::translate($ref, $transform);
		
		if ($iso) {
			$translation = eText::iso_htmlentities($translation);
		}
		
		echo $translation;
	}
	
	public static function get_json_translate() {
		
		if (!isset($_POST['get_translation'])) { return false; }
		
		unset($_POST['get_translation']);
		
		if (isset($_POST['lang'])) {
			self::set_lang($_POST['lang']);
			unset($_POST['lang']);
		}
		
		eMain::start_app();
		
		$translations = array();
		$request = explode('|', $_POST['request']);
		for ($i=0;$i<count($request);$i++) {
			$translations[$request[$i]] = self::translate($request[$i], $_POST['transform']);
		}
		
		return json_encode($translations);
		
	}
}
echo eLang::get_json_translate();
?>