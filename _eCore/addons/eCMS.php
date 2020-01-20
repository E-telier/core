<?php
class eCMS {
		
	// AUTO PARAMS //
	public static $datas_url;
	public static $table_prefix;
	
	public static $page_datas;
	public static $url_datas;
	
	public static $modules;
	
	public static function start_cms() {
					
		$folderURL = eMain::root_url();
		$currentURL = eMain::cur_page_url();
		
		self::$url_datas = substr($currentURL, strlen($folderURL));
		self::$url_datas = explode("/", self::$url_datas);
				
		// Manage parameters //
		$lastIndex = count(self::$url_datas)-1;
		$lastDatas = self::$url_datas[$lastIndex];
		if (strpos($lastDatas, '#')!==false) {
			self::$url_datas[$lastIndex] = substr($lastDatas, 0, strpos($lastDatas, '#'));
			$lastDatas = self::$url_datas[$lastIndex];
		}
		if (strpos($lastDatas, '?')!==false) {
			self::$url_datas[$lastIndex] = substr($lastDatas, 0, strpos($lastDatas, '?'));
			$lastDatas = self::$url_datas[$lastIndex];
		}
		if ($lastDatas=='' && count(self::$url_datas)>1) {	
			unset(self::$url_datas[$lastIndex]);
		}
				
		$cms_strpos = strpos($folderURL, '/cms/');
		if ($cms_strpos!==false) {
			$current_language = eParams::$available_languages[0];			
		} else {
			$current_language = self::$url_datas[0];
			array_shift(self::$url_datas);
		}
		
		eLang::set_lang($current_language, true);		
		self::$table_prefix = eParams::$prefix.'_'.$current_language;
		
		if (count(self::$url_datas)>0 && strpos(self::$url_datas[0], ".")===false) {
			$rewritedURL = true;
		} else { $rewritedURL = false; }
					
		self::$datas_url = self::$url_datas;
				
		//////////////////////////////////////////////////////////////////////////////////////////////
		
		// MODULES //
		self::$modules = eMain::$sql->sql_to_array("SELECT * FROM ".eParams::$prefix."_cms_modules WHERE activated=1 ORDER BY name ASC;");
		
		if ($cms_strpos===false) {
						
			// PAGE //
			if (count(self::$url_datas)>=2 && self::$url_datas[0]=='download') {
				
				// DOWNLOAD FILE //
				$file_url = 'uploaded_files/'.urldecode(self::$url_datas[1]);
				//die($file_url);
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $file_url);
				finfo_close($finfo);
				//$mime='application/pdf';
				header('Content-Type: '.$mime);		
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 				
				readfile($file_url);	
	
				$download = true;
				
			} else {
				
				header('Content-Type: text/html; charset=utf-8');
																
				// PARAMETRES //		
				$rq = "SELECT * FROM ".self::$table_prefix."_cms_params WHERE activated>0 ORDER BY id DESC LIMIT 1;";
				$result_datas = eMain::$sql->sql_to_array($rq);
				
				$page_datas = $result_datas['datas'][0];
				
				$download = false;
								
			}
					
			$error_404 = false;		
			if (isset($_GET['p'])) {				// GET PAGE //
								
				$rq = "SELECT * FROM ".self::$table_prefix."_cms_pages WHERE reference=\"".eMain::$sql->protect_sql($_GET['p'])."\" LIMIT 1;";			
			} else if ($download==false) {
			
				if ($rewritedURL==true) {
					$rq = "SELECT * FROM ".self::$table_prefix."_cms_pages WHERE reference=\"".eMain::$sql->protect_sql(self::$url_datas[0])."\" LIMIT 1;";
				
				} else {
					if (isset(self::$url_datas[0])) {
						// ACCESS FILE DOESN'T EXIST //
						$error_404 = true;
					} else {
						// DEFAULT PAGE //
						$rq = "SELECT * FROM ".self::$table_prefix."_cms_pages WHERE menu_position>0 ORDER BY menu_position ASC, reference ASC LIMIT 1;";					
					}
						
				}
			}
			
			if ($download==false) {
				if (!$error_404) {	
				
					$rq = eCMS::get_access_page($rq);

					$result_datas = eMain::$sql->sql_to_array($rq);
					if ($result_datas['nb']>0) {
						// merge header //			
						if ($result_datas['datas'][0]['description']=='') {
							$result_datas['datas'][0]['description'] = $page_datas['description'];
						}
						if ($result_datas['datas'][0]['keywords']=='') {
							$result_datas['datas'][0]['keywords'] = $page_datas['keywords'];
						}
						$page_datas = array_merge($page_datas, $result_datas['datas'][0]);						
					} else {
						$error_404 = true;
					}
								
					$rq = "UPDATE ".self::$table_prefix."_cms_pages SET views=views+1 WHERE id=".intval($page_datas['id']).";";
					eMain::$sql->sql_query($rq);
					
				}
								
				// SET SPECIAL META FOR PLUGINS //			
				for ($m=0;$m<self::$modules['nb'];$m++) {
					if (self::$modules['datas'][$m]['meta']!='') {
						include('plugins/'.self::$modules['datas'][$m]['meta'].'.php');
					}
				}
						
				if ($error_404) {
					$page_datas['id']=0;
					$page_datas['reference']="ERROR_404";
					$page_datas['access']="*";
					$page_datas['childof']="ERROR_404";
					$page_datas['menu_position']=0;
					$page_datas['title']="";
					$page_datas['keywords']="";
					$page_datas['description']="";
					$page_datas['content'] = "[h2]ERROR 404 : Page not found[/h2]";
								
					http_response_code(404);
					
				} else if ($page_datas['combined']==1) {
					header('location:'.eMain::root_url().$_SESSION['lang'].'/'.$page_datas['childof'].'/#'.$page_datas['reference']);
				}
				
				self::$page_datas = $page_datas;
				
			}			
			
		}
		
	}
	
	static public function get_access_page($rq) {
		$access_sql = "WHERE (access='*') AND";
		if (eUser::getInstance()->checked) {
			$access = eUser::getInstance()->get_datas('domain');
			
			if ($access['domain']=='*') {
				// give this man a access now !
				$access_sql = str_replace(') AND', " OR true) AND", $access_sql);
			} else {
				// check if access is allowed
				$access = explode('*', $access['domain']);
				for ($i=0;$i<count($access);$i++) {
					if ($access[$i]!='') {
						$access_sql = str_replace(') AND', " OR access LIKE '%*".eMain::$sql->protect_sql($access[$i])."*%') AND", $access_sql);
					}
				}
			}
		} else {
			$access_sql = str_replace(') AND', " OR access LIKE '%*visitor*%') AND", $access_sql);
		}
		$rq = str_replace('WHERE', $access_sql, $rq);
				
		return $rq;
	}
		
	static public function findSection($parent_ref) {
			
		$rq_parent = "SELECT menu_position, reference, childof FROM ".eParams::$prefix."_cms_pages WHERE reference='".$parent_ref."'";
		$result_parent = eMain::$sql->sql_to_array($rq_parent);
		$content_parent = $result_parent['datas'][0];
		if ($content_parent['childof']==$content_parent['reference']) {
			$section = $content_parent;
		} else {
			$section = self::findSection($content_parent['childof']);
		}		
		return $section;			
	}
	
	static public function includeModulesInHTML($html, $part) {
		
		$indent = 2;
		switch($part) {
			case 'header': $indent = 5; break;
			case 'content': $indent = 8; break;
			case 'block': $indent = 9; break;
			case 'footer': $indent = 5; break;
		}
										
		$html = str_replace("<div class=\"iframe\"><!-- <iframe", "<iframe", $html);
		$html = str_replace("</iframe> --></div><!-- END OF IFRAME -->", "</iframe>", $html);
			
		$start_module = '<div class="module ';
		$end_module = '"></div><!-- END OF MODULE -->';
						
		while(strpos($html, $start_module)!==false) {
			
			$pos = strpos($html, $start_module);
			$before = substr($html, 0, $pos);
			
			$end_pos = strpos($html, $end_module);
						
			$module_ref = substr($html, $pos+strlen($start_module), $end_pos-($pos+strlen($start_module)));
			
			for ($m=0;$m<self::$modules['nb'];$m++) {
				if (self::$modules['datas'][$m]['reference']==$module_ref) {
					$module_file = self::$modules['datas'][$m]['filename'];
				}
			}
						
			$html = substr($html, $end_pos+strlen($end_module));
			
			echo eText::indentHTML($before, $indent);
			include('plugins/'.$module_file.'.php');
		
		}
						
		echo eText::indentHTML($html, $indent);
	}
	
}
?>