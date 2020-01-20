<?php
if (!defined('MYSQL_ASSOC')) {
	define('MYSQL_BOTH',MYSQLI_BOTH);
	define('MYSQL_NUM',MYSQLI_NUM);
	define('MYSQL_ASSOC',MYSQLI_ASSOC);
}

class eSQL {
	
	private $mysqli;
	private $database;
	
	public function connect_sql($user="root", $host="127.0.0.1", $password="", $database="mysql", $port=null) {
					
		$this->mysqli = mysqli_connect($host,$user,$password, $database, $port)
			or die("connexion au serveur impossible") ;
		$db = mysqli_select_db($this->mysqli, $database)
			or die("connexion à la db impossible") ;
			
		/* Vérification de la connexion */
		if (mysqli_connect_errno()) {
			printf("Échec de la connexion : %s\n", mysqli_connect_error());
			exit();
		}

		mysqli_query($this->mysqli, 'SET NAMES utf8');
		
		$this->database = $database;
		
		return true;
	}
	
	public function disconnect_sql() {
		mysqli_close($this->mysqli);
		
		return true;
	}
	
	public function sql_query($rq, $return_last_id = false) {
				
		$result = mysqli_query($this->mysqli, $rq);
		
		if (!$result) {
			echo mysqli_error($this->mysqli)."\n".$rq;
			return false;
		} else if ($return_last_id) {
			return mysqli_insert_id($this->mysqli);
		}		
		
		return $result;
	}
	
	public function sql_to_array($rq, $returnEmptyAssoc = true) {
		
		//echo "<br />\n".$rq."<br />\n";
			
		$array = array();	
		$array['datas'] = array();
		$result = mysqli_query($this->mysqli, $rq);
		
		if (!$result) { die($rq."\n".mysqli_error($this->mysqli)); }
		
		$nb = mysqli_num_rows($result);
		for ($r=0;$r<$nb;$r++) {						
			$content = mysqli_fetch_array($result, MYSQL_ASSOC);
			$array['datas'][] = $content;
		}
		if ($nb>0) {
			$array['assoc'] = array_keys($content);				
		} else {
			$array['assoc'] = array();
			if ($returnEmptyAssoc) {
				$table = preg_replace('/.+ FROM ([^ ]+) .+/im', '$1', $rq);
				$table = preg_replace('/.+\.([^ ]+)/im', '$1', $table);
				if (!empty($table) && $table!='COLUMNS' && strpos($table, ' ')===false) {
					if (!$result) { echo '<!-- <b>Notice</b>:'.str_replace('pro_', 'fake_', $rq).' on line <b>938</b> -->'; }
					$assoc = self::sql_to_array("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".$this->database."' AND TABLE_NAME = '".$table."';");
					for ($a=0;$a<$assoc['nb'];$a++) {
						//print_r($assoc['datas'][$a]);
						$array['assoc'][] = $assoc['datas'][$a]['COLUMN_NAME'];
					}
				}
			}
		}
		$array['nb'] = $nb;
		
		return $array;
	}
	
	public function sql_to_num($rq) {
		
		$result = mysqli_query($this->mysqli, $rq);
		if (!$result) { echo $rq; }
		$nb = mysqli_num_rows($result);
		return $nb;
	}
	
	public function get_SQL_fields($table, $excludeID = false) {
		$array = eMain::$sql->sql_to_array("SELECT * FROM $table LIMIT 1;");
		$assoc = $array['assoc'];
		if ($excludeID) { $assoc = deleteOne('id', $assoc); }
		if ($excludeID) { $assoc = deleteOne('id_user', $assoc); }
		sort($assoc);
		return $assoc;
	}
	
	public function protect_sql($string) {
		
		$magic_quotes = false;
		if (function_exists('get_magic_quotes_gpc')) {
			$magic_quotes = get_magic_quotes_gpc();
		}
				
		if ($magic_quotes) {
			$string = stripslashes($string);			
		}
		
		$string = mysqli_real_escape_string($this->mysqli, $string);
		
		return  $string;
		
	}
	public function protect_implode($delimiter, $array) {
		$string = '';
		for ($i=0;$i<count($array);$i++) {
			if ($i>0) {
				$string .= $delimiter;
			}
			$string .= $this->protect_sql($array[$i]);
		}
		return $string;
	}

	public function get_error() {
		return mysqli_error($this->mysqli);
	}
	
	public function backup($tables = '*', $directory = '_db_backups') {
		$sql_string = "";
						
		//get all of the tables
		if($tables == '*') {
			$tables = array();
			$result_datas = eMain::$sql->sql_to_array('SHOW TABLES');
			for($t=0;$t<$result_datas['nb'];$t++) {
				$tables[] = $row[0];
			}
		}else if ($tables == 'prefix') {
			$tables = array();
			$rq = "SHOW TABLES LIKE '".eParams::$prefix."%';";
			$result_datas = eMain::$sql->sql_to_array($rq);
			
			for($t=0;$t<$result_datas['nb'];$t++) {
				$tables[] = $result_datas['datas'][$t][$result_datas['assoc'][0]];
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		//cycle through
		foreach($tables as $table) {
			$result_datas = eMain::$sql->sql_to_array('SELECT * FROM '.$table.' ORDER BY id ASC;');
			
			$num_fields = count($result_datas['assoc']);
			
			$sql_string.= 'DROP TABLE '.$table.';';
			$row2 = eMain::$sql->sql_to_array('SHOW CREATE TABLE '.$table);
			
			//print_r($row2);
			//die('lala');
			
			$sql_string.= "\n\n".$row2['datas'][0][$row2['assoc'][1]].";\n\n";
			
			for($t=0;$t<$result_datas['nb'];$t++) {
				$row = $result_datas['datas'][$t];
				$sql_string.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) {
					
					$field = $row[$result_datas['assoc'][$j]];
					
					$field = addslashes($field);
					$field = str_replace("\n","\\n",$field);
					$field = str_replace("\r","",$field);
					
					$sql_string.= '"'.$field.'"' ;
					if ($j<($num_fields-1)) { $sql_string.= ','; }
				}
				$sql_string.= ");\n";
			}
			
			$sql_string.="\n\n\n";
		}
		
		//save file
		$handle = fopen($directory.'/'.(date('Y-m-d_His')).'.sql','w+');
		fwrite($handle,$sql_string);
		fclose($handle);
		
		return true;
	}
	
}
?>