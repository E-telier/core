<?php
class eTools {
	
	public static function remove_array_item( $array, $item ) {
		$index = array_search($item, $array);
		if ( $index !== false ) {
			array_splice( $array, $index, 1 );
		}

		return $array;
	}
	
	public static function getShortenURL($long_url, $params = array()) {
						
		$shortRoot = str_replace('/www.', '/', eMain::root_url());		
		$base_url = $shortRoot.'?k=';
		
		$long_url = str_replace('&amp;', '&', $long_url);
		
		$shorturl = eMain::$sql->sql_to_array("SELECT shortkey FROM ".eParams::$prefix."_shorturl WHERE longurl='".eMain::$sql->protect_sql($long_url)."' LIMIT 1;");
		
		if ($shorturl['nb']>0) {
			return $base_url.$shorturl['datas'][0]['shortkey'];
		} 
		
		$alphabet = array();
		foreach(range('a','z') as $i) {
			$alphabet[] = $i;
		}
		
		$count = 0;
		do {
			$shortkey = '';
			for ($i=0;$i<4;$i++) {
				$random = rand(0, 35);
				if ($random<10) {
					$shortkey .= $random;
				} else {
					$shortkey .= $alphabet[$random-10];
				}
			}
			
			$count++;
		} while(sqlToNum("SELECT id FROM ".eParams::$prefix."_shorturl WHERE shortkey='".$shortkey."' LIMIT 1;")>0 && $count<1000);
		
		$params = json_encode($params);		
		eMain::$sql->sql_query("INSERT INTO ".eParams::$prefix."_shorturl (creation_date, longurl, shortkey, params) VALUES ('".date('Y-m-d')."', '".eMain::$sql->protect_sql($long_url)."', '".$shortkey."', '".eMain::$sql->protect_sql($params)."');");
		
		return $base_url.$shortkey.'&c='.$count;
		
	}
	
	public static function shorturl() {
		if (isset($_GET['k'])) {
			$longurl = sqlToArray("SELECT id, longurl FROM ".eParams::$prefix."_shorturl WHERE shortkey='".eMain::$sql->protect_sql($_GET['k'])."' LIMIT 1;");
			if ($longurl['nb']>0) {
				eMain::$sql->sql_query("UPDATE ".eParams::$prefix."_shorturl SET views=views+1 WHERE id=".$longurl['datas'][0]['id'].";");
				header('Location:'.$longurl['datas'][0]['longurl'], true);
				die('');
			}
		}
	}
	
}
?>