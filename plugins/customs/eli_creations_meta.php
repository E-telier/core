<?php

	global $crea_datas;
	
	if (count(eCMS::$datas_url)>1 && eCMS::$datas_url[0]=='creations') {
				
		$crea_title = str_replace('|slash|', '/', urldecode(eCMS::$datas_url[1]));
						
		$rq = "SELECT * FROM ".eCMS::$table_prefix."_back_creations WHERE title='".eMain::$sql->protect_sql($crea_title)."' LIMIT 1";
		$crea_datas = eMain::$sql->sql_to_array($rq);
		$crea_datas = $crea_datas['datas'][0];
				
		if ($crea_datas['visible']==0) {
			$error_404 = true;
		} else {
			
			$page_datas['title'] = eParams::$site_name.' : '.$crea_title." - Créations ".$crea_datas['cat']; 
			$page_datas['keywords'] = eParams::$site_name.", ".$crea_title.", Créations, ".$crea_datas['cat'];
			$page_datas['description'] = eText::no_style($crea_datas['description']);
						
		}	
		
	}
?>