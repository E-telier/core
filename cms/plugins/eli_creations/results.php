<?php
	switch ($page) {
		case "eli_creations":
			$cols_values = array("title", "annee", "role", "tech", "languages", "hidden", "visible", "views");
			$cols_names = array("title", "annee", "role", "tech", "languages", "hidden", "visible", "views");	
			$table = eCMS::$table_prefix."_back_creations";
			$sql_order = "ORDER BY hidden ASC, visible DESC, annee DESC, id DESC;";
			$titlename = "creations";
			
			break;
	}
?>