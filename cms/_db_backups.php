<?php

	$folder = "_db_backups";
	$date = date("Y-m-d");

	$filesList = eFile::explore_folder($folder);
	$nb_files = count($filesList);
	
	//print_r($filesList);
	
	$done_backup = false;
	for ($f=0;$f<$nb_files;$f++) {
		//echo substr($filesList[$f], 0, 10)." ".$date;
		if (substr($filesList[$f], 0, strlen($date))==$date) {
			$done_backup = true;
			break;			
		}
	}
			
	if (!isset($force_backup)) { $force_backup = false; }
			
	if (!$done_backup || $force_backup == true) {
		/*
		$array_tables = array("blocks", "images", "pages", "params", "styles", "users");
		$nb_tables = count($array_tables);
		for ($t=0;$t<$nb_tables;$t++) {
			$array_tables[$t] = eCMS::$table_prefix."_cms_".$array_tables[$t];
		}
		*/
		
		$rq = "SHOW TABLES LIKE '".eCMS::$table_prefix."%';";
		$result_datas = eMain::$sql->sql_to_array($rq);
		//print_r($result_datas);
		for($t=0;$t<$result_datas['nb'];$t++) {
			$array_tables[] = $result_datas['datas'][$t][$result_datas['assoc'][0]];
		}
		
		backup_tables($array_tables);
	}
		
	function backup_tables($tables = '*') {
	
		global $folder, $date;
		global $mysqli;
	
		$return = "";
						
		//get all of the tables
		if($tables == '*') {
			$tables = array();
			$result = eMain::$sql->sql_query('SHOW TABLES');
			while($row = mysqli_fetch_row($result)) {
				$tables[] = $row[0];
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		//cycle through
		foreach($tables as $table) {
			$result = eMain::$sql->sql_query('SELECT * FROM '.$table);
			/*
			echo 'SELECT * FROM '.$table;
			echo "<br />";
			echo mysql_error();
			echo "<br />";
			*/
			
			$num_fields = mysqli_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(eMain::$sql->sql_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) {
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n","\\n",$row[$j]);
						$row[$j] = str_replace("\r","",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		//save file
		$handle = fopen($folder.'/'.$date.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
		
		echo "Backup de la DB réussi !";
		
	}
	

?>