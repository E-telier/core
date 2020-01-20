<?php

	if (!empty(eCMS::$url_datas) && strpos(eMain::root_url(), '127.0.0.1')===false) {

		$sql_user = 'etelierint';
		$sql_host = 'etelierint.mysql.db';
		$sql_password = 'eINT2020';
		$sql_database = 'etelierint';
		$sql_port = null;
		

		$sql_stats = new eSQL();
		$sql_stats->connect_sql($sql_user, $sql_host, $sql_password, $sql_database, $sql_port); 

		$rq = "INSERT INTO stats (site, date, quoi, qui, vu) 
		VALUES ('".eParams::$site_name."', NOW(), '".eMain::root_url().$_SESSION['lang'].'/'.implode('/', eCMS::$url_datas).'=>'.eCMS::$page_datas['reference']." POST(".preg_replace('/pass([^=]*?)=[^&]+?&/im', 'pass$1=***&', http_build_query($_POST)).") GET(".preg_replace('/pass([^=]*?)=[^&]+?&/im', 'pass$1=***&', http_build_query($_GET)).")', '".gethostbyaddr($_SERVER['REMOTE_ADDR'])." | ".$_SERVER["HTTP_USER_AGENT"]."', 0)";
		
		$sql_stats->sql_query($rq);
		
		$sql_stats->disconnect_sql();
	}
?>