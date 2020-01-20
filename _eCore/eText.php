<?php
class eText {
	
	public static function str_to_url($string, $keep_ext = false) {
			
		$string = self::iso_strtolower($string);	
				
		$string = trim($string);				
		$string = str_replace(",", "", $string);
		$string = str_replace(";", "", $string);
		$string = str_replace(":", "", $string);
		if (!$keep_ext) { $string = str_replace(".", "", $string); }
		$string = str_replace("\"", "", $string);
		$string = str_replace("\\", "", $string);
		$string = str_replace("/", "", $string);
		$string = str_replace("%", "pct", $string);
		$string = str_replace(" & ", "+", $string);
		
		$string = str_replace("’", "_", $string);
		$string = str_replace("'", "_", $string);
		$string = str_replace(" ", "_", $string);		
		//$string = strtr($string, "’' ","___");
				
		$string = strtr($string, "()","--");
				
		$string = self::strip_accents($string);		
	
		return $string;
	
	}
	
	public static function strip_accents($str)
    {
        $str = self::iso_htmlentities($str);
        
		//echo $str.'<br />';
        $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
		//echo $str.'<br />';
        $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str);
        $str = preg_replace('#\&[^;]+\;#', '', $str);
        
        return $str;
    }
	
	
	public static function iso_strtoupper($chaine) {
		/*
		$chaine=strtoupper($chaine);
		$chaine=trim($chaine); 
		$chaine = strtr($chaine, "äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø","ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ");
		*/
		
		$chaine = mb_strtoupper($chaine, "UTF-8");
		
		return $chaine;
	}
	public static function iso_strtolower($chaine) {				
		$chaine = mb_strtolower($chaine, "UTF-8");		
		return $chaine;
	}
	public static function iso_ucfirst($chaine) {		
		if (strlen($chaine)>0) {
			$first_char = mb_substr($chaine, 0, 1, 'UTF-8');
			$first_char = self::iso_strtoupper($first_char);
			$chaine = $first_char.mb_substr($chaine, 1, strlen($chaine)-1, 'UTF-8');
		}
		return $chaine;
	}
	public static function iso_substr($string, $start, $length) {
		return mb_substr($string, $start, $length, 'UTF-8');
	}
	public static function iso_htmlentities($string) {
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}
	public static function iso_strlen($string) {
		return mb_strlen( $string, 'utf8' );
	}
	
	public static function html_quotes($string) {
		return str_replace("\"", "&quot;", $string);
	}
	
	public static function execute_php_in_html($string) {
				
		while(strpos($string, "<?php")!==false) {
			
			$pos = strpos($string, "<?php");
			$start_string = substr($string, 0, $pos);
			
			$end_pos = strpos($string, "?>");
			$php_string = substr($string, $pos+5, $end_pos-($pos+5));
										
			$string = substr($string, $end_pos+2);
			
			echo $start_string;
			eval($php_string);
		
		}
				
		echo $string;
		
	}
	
	public static function manage_html_chars($string) {
	
		$new_string = "";
	
		while(strpos($string, "[HTML]")!==false) {
			
			$pos = strpos($string, "[HTML]");
			$start_string = substr($string, 0, $pos);
			
			$end_pos = strpos($string, "[/HTML]");
			$html_string = substr($string, $pos+6, $end_pos-($pos+6));
			
			$html_string = str_replace("[", "|[|", $html_string);
			$html_string = str_replace("]", "|]|", $html_string);
					
			$html_string = str_replace("\n", "|newline|", $html_string);
			$html_string = str_replace("\r", "|return|", $html_string);
					
			$end_string = substr($string, $end_pos+7);
			
			$new_string .= self::iso_htmlentities($start_string).$html_string;
						
			$nextFirstChar = "";
			if (strpos($end_string, "\n\r")===0) {
				$nextFirstChar = "|newline||return|";	
			} else if (strpos($end_string, "\r\n")===0) {
				$nextFirstChar = "|return||newline|";	
			} else if (strpos($end_string, "\n")===0) {
				$nextFirstChar = "|newline|";	
			} else if (strpos($end_string, "\r")===0) {
				$nextFirstChar = "|return|";	
			}
					
			if (!empty($nextFirstChar)) {
				$nb_chars = 1;
				if (strlen($nextFirstChar)>10) {
					$nb_chars = 2;
				}
				$end_string = substr($end_string, $nb_chars);
			}			
			$string = $nextFirstChar.$end_string;
								
		}
		
		if (!isset($pos)) { $new_string = self::iso_htmlentities($string); }
		else { $new_string .= self::iso_htmlentities($end_string);}
		
		return $new_string;
		
	}
		
	public static function manage_return_chars($string) {
	
		while(strpos($string, "[NO-RETURN]")!==false) {
			
			$pos = strpos($string, "[NO-RETURN]");
			$start_string = substr($string, 0, $pos);
			
			$end_pos = strpos($string, "[/NO-RETURN]");
			$no_return_string = substr($string, $pos+11, $end_pos-($pos+11));
			
			$no_return_string = str_replace("\n", "|newline|", $no_return_string);
			$no_return_string = str_replace("\r", "|return|", $no_return_string);
			
			$end_string = substr($string, $end_pos+12);
			
			$string = $start_string.$no_return_string.$end_string;
		
		}
				
		$string = str_replace("]\n\r", "]|newline||return|", $string);
		$string = str_replace("]\r\n", "]|return||newline|", $string);
		$string = str_replace("]\n[", "]|newline|[", $string);
		$string = str_replace("]\r[", "]|return|[", $string);
				
		$string = nl2br($string);
				
		$string = str_replace("|newline|", "\n", $string);
		$string = str_replace("|return|", "\r", $string);
		
		return $string;
	
	}
	
	public static function manage_lists($string) {
						
		$string = str_replace("\r\n[dotlist]", "|newline|[dotlist]", $string);
		$string = str_replace("\n[dotlist]", "|newline|[dotlist]", $string);
				
		$string = str_replace("\r\n[/dotlist]", "[/dotlist]", $string);
		$string = str_replace("\n[/dotlist]", "[/dotlist]", $string);
		
		$string = preg_replace("/\[dotlist\][ ]+/sim", "[dotlist]", $string);
		
		$string = str_replace("[/dotlist]\r\n", "[/dotlist]|newline|", $string);
		$string = str_replace("[/dotlist]\n", "[/dotlist]|newline|", $string);
		
		//echo "\n<br />4/ ".self::iso_htmlentities($string);
					
		while(strpos($string, "[dotlist]")!==false) {
			$pos = strpos($string, "[dotlist]");
			$start_string = substr($string, 0, $pos);
			
			$end_pos = strpos($string, "[/dotlist]");
			$list_string = substr($string, $pos+9, $end_pos-($pos+9));
						
			$replace = "<ul>|newline|<li>";
			$list_string = preg_replace("/[\r]*\n\-[ ]+/", $replace, $list_string, 1);
			//$list_string = preg_replace("/\n- /", $replace, $list_string, 1);
						
			$replace = "</li>|newline|<li>"; 
			$list_string = str_replace("\r\n- ", $replace, $list_string);
			$list_string = str_replace("\n- ", $replace, $list_string);
			
			$end_string = substr($string, $end_pos+10);
			
			$string = $start_string.$list_string."</li>|newline|</ul>".$end_string;
						
		}
		
		//echo "\n<br />5/ ".self::iso_htmlentities($string);
		
		$string = str_replace("\r\n[numlist]", "|newline|[numlist]", $string);
		$string = str_replace("\n[numlist]", "|newline|[numlist]", $string);
				
		$string = str_replace("\r\n[/numlist]", "|newline|[/numlist]", $string);
		$string = str_replace("\n[/numlist]", "|newline|[/numlist]", $string);
		
		$string = preg_replace("/\[numlist\][ ]+/sim", "[numlist]", $string);
		
		$string = str_replace("[/numlist]\r\n", "[/numlist]|newline|", $string);
		$string = str_replace("[/numlist]\n", "[/numlist]|newline|", $string);
					
		while(strpos($string, "[numlist]")!==false) {
			$pos = strpos($string, "[numlist]");
			$start_string = substr($string, 0, $pos);
			
			$end_pos = strpos($string, "[/numlist]");
			$list_string = substr($string, $pos+9, $end_pos-($pos+9));
						
			$replace = "<ol>|newline|<li>";
			$list_string = preg_replace("/[\r]*\n- /", $replace, $list_string, 1);
			//$list_string = preg_replace("/\n- /", $replace, $list_string, 1);
						
			$replace = "</li>|newline|<li>"; 
			$list_string = str_replace("\r\n- ", $replace, $list_string);
			$list_string = str_replace("\n- ", $replace, $list_string);
			
			$end_string = substr($string, $end_pos+10);
			
			$string = $start_string.$list_string."</li>|newline|</ol>".$end_string;
						
		}	
		
		return $string;
		
	}
	
	public static function manage_images($texte) {
	
		$rootURL = eMain::root_url();
		$rootURL = str_replace('/cms/', '/', $rootURL);
	
		// Images //
		$newtexte = "";
		$pos = 0;
		//echo strpos($texte, "[img=", $pos);
		while (strpos($texte, "[img=", $pos)!==false && strpos($texte, " /img]", $pos)!==false) {
			$startpos = strpos($texte, "[img=", $pos);
			$newtexte .= substr($texte, $pos, ($startpos-$pos));
			$endpos = strpos($texte, " /img]", $startpos+5);
			$name = substr($texte, $startpos+5, ($endpos-($startpos+5)));
						
			$rq = "SELECT * FROM ".eParams::$prefix."_".$_SESSION['lang']."_cms_images WHERE name=\"".$name."\";";
			//echo $rq;
			$result_datas = eMain::$sql->sql_to_array($rq);
			if ($result_datas['nb']>0) {
				$contenu = $result_datas['datas'][0];
			} else {
				$contenu['align'] = "left";
				$contenu['name'] = "img_notfound";
				$contenu['extension'] = "gif";
				$contenu['width'] = 100;
				$contenu['height'] = 100;
				$contenu['description'] = "Image not found";
				$contenu['openbig'] = 0;
				$contenu['folder'] = '';
			}
			
			if ($contenu['folder']!='') {
				$contenu['folder'].='/';
			}
			
			$img_str = '<img src="'.$rootURL.'images/'.$contenu['folder'].$contenu['name'].'.'.$contenu['extension'].'" width="'.$contenu['width'].'" height="'.$contenu['height'].'" title="'.$contenu['description'].'" alt="'.$contenu['description'].'" contenteditable="false" />';
						
			if ($contenu['openbig'] == 1) {
				$img_str = "<a href=\"".$rootURL."images/".$contenu['folder'].$contenu['name'].'_full.'.$contenu['extension']."\" target=\"_blank\">".$img_str."</a>";
			}
			
			$string = '<div class="img '.$contenu['align'].' img_'.$contenu['align'].' '.$contenu['name'].'">'.$img_str.'</div>';
									
			$newtexte .= $string;
			$pos = $endpos+6;
		}		
		if ($newtexte!="") { $newtexte .= substr($texte, $pos); $texte=$newtexte; }
				
		return $texte;
	}
	
	public static function style_to_html($texte) {
			
		// SPECIAL //
		$texte = str_replace("<?php", "<div class=\"php\"><!-- <?php", $texte);
		$texte = str_replace("?>", "?> --></div><!-- END OF PHP -->", $texte);
		
		$texte = str_replace("<iframe", "<div class=\"iframe\"><!-- <iframe", $texte);
		$texte = str_replace("</iframe>", "</iframe> --></div><!-- END OF IFRAME -->", $texte);
				
		$blocks = array();
							
		while(preg_match("/\[block=([^ ]+?) \/block\]/im", $texte, $blocks)) {
			$block_datas = eMain::$sql->sql_to_array("SELECT title, bgcolor, textalign, content FROM ".eParams::$prefix.'_'.$_SESSION['lang']."_cms_blocks WHERE reference='".$blocks[1]."';");
			$this_block = array();
			if ($block_datas['nb']>0) {
				$this_block = $block_datas['datas'][0];
				$block_content = $this_block['content'];
			} else {
				$block_content = 'missing block';
			}
			
			if (!empty($this_block['title'])) { $block_content = '[h3]'.$this_block['title']."[/h3]\n".$block_content; };
			
			$style = '';
			if (!empty($this_block['bgcolor'])) { $style .= 'background-color:#'.$this_block['bgcolor']; };
			if (!empty($this_block['textalign']) && $this_block['textalign']!='initial') { $style .= 'text-align:'.$this_block['textalign']; };
			
			$texte = str_replace('[block='.$blocks[1].' /block]', '[HTML]<div class="block '.$blocks[1].'" style="'.$style.'">[/HTML]'.$block_content.'[HTML]</div><!-- END OF BLOCK -->[/HTML]', $texte);
		}
						
		$texte = preg_replace("/\[module=([^ ]+?) \/module\]/im", "[HTML]<div class=\"module $1\"></div><!-- END OF MODULE -->[/HTML]", $texte); // MODULE FOR WYSIWYG
		
		//echo eText::iso_htmlentities($texte)."<br />\n";
		
		$texte = str_replace("’", "'", $texte);
					
		// HTML //
		$texte = preg_replace("/([ \n\r\t]+)(http[s]*:\/\/[^ \n\r]+)/", "$1[url='$2']$2[/url] ", $texte);
		$texte = preg_replace("/([ \n\r\t]+)([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)([ \n\r]+)/", "$1[url='$2']$2[/url]$3", $texte);
		$texte = self::manage_html_chars($texte);
						
		// LISTS //
		//echo "\n<br />1/ ".self::iso_htmlentities($texte);
		$texte = self::manage_lists($texte);
		//echo "\n<br />2/ ".self::iso_htmlentities($texte);
				
		// RETURN //
		$texte = self::manage_return_chars($texte);	
		//echo "\n<br />3/ ".self::iso_htmlentities($texte);
						
		$texte = str_replace("  ", "&nbsp; ", $texte);
		$texte = str_replace("[puce]", "<img src=\"".eMain::root_url()."design/point.png\" width=\"7\" height=\"7\" class=\"puce\" alt=\"*\" />&nbsp; ", $texte);
				
		$texte = str_replace("[space]", "&nbsp;", $texte);
				
		$texte = str_replace("[paragraph]", "<div class='paragraph'>", $texte);
		$texte = str_replace("[/paragraph]", "</div>", $texte);
		
		$texte = str_replace("[right]", "<div class='align_right'>", $texte);
		$texte = str_replace("[/right]", "</div>", $texte);
		
		$texte = str_replace("[title", "<h", $texte);
		$texte = str_replace("[/title", "</h", $texte);
		
		$pattern = "/url=\'([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)/";
		$replacement = "url='mailto:$1";
		$texte = preg_replace($pattern, $replacement, $texte);
				
		$pattern = "/\[url='([^']*)'\](.*?)\[\/url\]/";
		$replacement = "<a href=\"$1\" target=\"_blank\">$2</a>";
		$texte = preg_replace($pattern, $replacement, $texte);
		
		$texte = str_replace("[link='", "<a href='".eMain::root_url().$_SESSION['lang'].'/', $texte);
		$texte = str_replace("[/link]", "</a>", $texte);
		
		$texte = str_replace("bold]", "b>", $texte);
		$texte = str_replace("italic]", "i>", $texte);
		$texte = str_replace("underlined]", "u>", $texte);
		
		$texte = str_replace("[blue]", "<span style=\"color:#36365C;\">", $texte);
		$texte = str_replace("[red]", "<span style=\"color:#CC3300;\">", $texte);
		$texte = str_replace("[green]", "<span style=\"color:#336600;\">", $texte);
		$texte = str_replace("[gray]", "<span style=\"color:#999999;\">", $texte);
		
		$closeColor = array("[/blue]", "[/red]", "[/green]", "[/gray]");
		$texte = str_replace($closeColor, "</span>", $texte);
		
		$texte = str_replace("[...]", "(...)", $texte);
		$texte = str_replace("[…]", "(…)", $texte);
		
		//echo "\n<br />4/ ".self::iso_htmlentities($texte);
		
		$texte = self::manage_images($texte);
		
		//echo "\n<br />5/ ".self::iso_htmlentities($texte);
				
		$texte = str_replace("[", "<", $texte);
		$texte = str_replace("]", ">", $texte);
		
		$texte = str_replace("|<|", "[", $texte);
		$texte = str_replace("|>|", "]", $texte);
						
		return $texte;
					
	}
	
	public static function html_to_style($string) {
		
		// IMAGES //
		while(strpos($string, 'class="img')!==false) {
			$pos = strpos($string, 'class="img');
						
			$start = strrpos(substr($string, 0, $pos), '<div');
			$end = strpos($string, '</div>', $pos) + 6;
			
			//die( $pos.'-'.$start.'-'.$end);
			
			$img_end = strpos($string, '"', $pos+7);
			$img_str = substr($string, $pos+7, $img_end-$pos-7);			
			$img_start = strrpos(substr($img_str, 0, $img_end), ' ');
			
			//die( $img_start.'-'.$img_str.'-'.$img_end);
			$img_str = substr($img_str, $img_start+1, $img_end);
			
			//die( $img_start.'-'.$img_str.'-'.$img_end);
			
			$string = substr($string, 0, $start) . '[img='.$img_str.' /img]' . substr($string, $end);
		}
	
		// RETURNS //
		$string = str_replace("\r", "", $string);
		$string = str_replace("\t", "", $string);
				
		$string = preg_replace("/[\n]*<ul>[\n ]*/sim", "<ul>", $string);
		$string = preg_replace("/[\n]*<ol>[\n ]*/sim", "<ol>", $string);
		$string = str_ireplace("[\n]*<([\/]*)li>[\n]*", "<$1li>", $string);
		
		$string = str_replace("\n", "[spacing]", $string);
		
		$string = str_ireplace("<br />", "<br>", $string);
			
		$string = str_ireplace("<br>[spacing]", "|newline|", $string);
		$string = str_ireplace("<br>", "|newline|", $string);
		
		$string = str_ireplace("</p>[spacing]", "</p>", $string);
		$string = str_ireplace("</div>[spacing]", "</div>", $string);
						
		$string = preg_replace('/\|newline\|(?=(?:.(?!<li>))*<\/li>)/Um', '', $string);
						
		$string = str_ireplace("</p>|newline|", "</p>", $string);
		$string = preg_replace('/^<p>\|newline\|<\/p>([\s\S]+)/Um', '|firstline|$1', $string); // REMOVE FIRST EMPTY P
		$string = preg_replace('/^<p>([\s\S]+)/Um', '$1', $string); // REMOVE FIRST P
		$string = str_ireplace("<p>|newline|</p>", "|newline|", $string);		
		$string = str_ireplace("<p>", "|newline|", $string);
		$string = str_ireplace("</p>", "", $string);
				
		$string = str_ireplace("</div>|newline|", "</div>", $string);
		$string = preg_replace('/^<div>\|newline\|<\/div>([\s\S]+)/Um', '|firstline|$1', $string); // REMOVE FIRST EMPTY DIV
		$string = str_ireplace("<div>|newline|</div>", "|newline|", $string);
		$string = preg_replace('/^<div>([\s\S]+)/Um', '$1', $string); // REMOVE FIRST DIV
		$string = str_ireplace("<div>", "|newline|", $string);
		$string = str_ireplace("</div>", "", $string);
		
		$string = str_ireplace("|firstline|", "", $string);
		
		/*
		$string = str_ireplace("<br>\n", "[N1]\n\n", $string);
		$string = str_ireplace("<br>", "[N2]\n", $string);
		*/
		
		// SIMPLE //
		$pattern = "/<([\/]?)b>/sim";
		$replacement = "[$1bold]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<([\/]?)i>/sim";
		$replacement = "[$1italic]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<([\/]?)u>/sim";
		$replacement = "[$1underlined]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<([\/]?)small>/sim";
		$replacement = "[$1small]";
		$string = preg_replace($pattern, $replacement, $string);
		
		// SPAN //
		$pattern = "/<span style=\\\\\"color:#36365C;\\\\\">(.*?)<\/span>/sim";
		$replacement = "[blue]$1[/blue]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<span style=\\\\\"color:#CC3300;\\\\\">(.*?)<\/span>/sim";
		$replacement = "[red]$1[/red]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<span style=\\\\\"color:#336600;\\\\\">(.*?)<\/span>/sim";
		$replacement = "[green]$1[/green]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<span style=\\\\\"color:#999999;\\\\\">(.*?)<\/span>/sim";
		$replacement = "[gray]$1[/gray]";
		$string = preg_replace($pattern, $replacement, $string);
		
		// LINK //
		$pattern = "/<a href=\\\\\"\.\.\/([^\"]*)\\\\\">(.*?)<\/a>/sim";
		$replacement = "[link='$1']$2[/link]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<a href=\\\\\"([^\"]*)\\\\\" target=\\\\\"_blank\\\\\">(.*?)<\/a>/sim";
		$replacement = "[url='$1']$2[/url]";
		$string = preg_replace($pattern, $replacement, $string);
		
		// TITLES //		
		$pattern = "/<([\/]?)h([0-9])>/sim";
		$replacement = "[$1title$2]";
		$string = preg_replace($pattern, $replacement, $string);
		
		// LISTS //		
		$pattern = "/<ul>(.*?)<\/ul>/sim";//<\/ul>
		$replacement = "|newline|[dotlist]$1|newline|[/dotlist]";
		$string = preg_replace($pattern, $replacement, $string);
		
		$pattern = "/<ol>(.*?)<\/ol>/sim";//<\/ol>
		$replacement = "|newline|[numlist]$1|newline|[/numlist]";
		$string = preg_replace($pattern, $replacement, $string);
				
		$pattern = "/<li>(.*?)<\/li>/sim";//<\/li>
		$replacement = "\n- $1";
		$string = preg_replace($pattern, $replacement, $string);
						
		// HTML //		
		$string = str_replace("<", "[HTML]<", $string);
		$string = str_replace(">", ">[/HTML]", $string);
		$string = str_replace("[/HTML][HTML]", "", $string);
		$string = str_replace("[/HTML]\n[HTML]", "", $string);
		
		$string = str_replace(">|newline|", "> |newline|", $string);
		$string = str_replace("]|newline|", "] |newline|", $string);
		$string = str_replace("|newline|", "\n", $string);
		$string = str_replace("[spacing]", " ", $string);
		
		$string = html_entity_decode($string, ENT_COMPAT, 'utf-8');
		
		return $string;
		
	}
			
	public static function no_style($text, $multiline=false) {
		
		$new_text = "";
		
		while(strpos($text, "]")>0) {
			$new_text = $new_text.substr($text, 0, strpos($text, "["));
			$text = substr($text, strpos($text, "]")+1);
		}
		$new_text.=$text;
		
		if (!$multiline) {
			$new_text = str_replace("\n", "", $new_text);
			$new_text = str_replace("\r", "", $new_text);
		}
		return $new_text;
	
	}
	public static function no_html($string, $delete_spacing = true) {
		$new_string = "";
		$string = html_entity_decode($string, ENT_COMPAT, 'utf-8');
		
		// DELETE CSS //
		$string = preg_replace('/<style.*<\/style>/ sim', '', $string);
		
		// DELETE TAGS //
		while(strpos($string, ">")>0) {
			$new_string = $new_string.substr($string, 0, strpos($string, "<"));			
			$string = substr($string, strpos($string, ">")+1);			
		}
		$new_string.=$string;
		
		if ($delete_spacing) {
			$new_string = str_replace("\n", "", $new_string);
			$new_string = str_replace("\r", "", $new_string);
			$new_string = str_replace("\t", "", $new_string);
		}
		
		return $new_string;
	}
	
	public static function no_script($string) {
		$string = preg_replace('/<script[^<]*?>/', '', $string);
		$string = str_replace('</script>', '', $string);
		return $string;
	}
	
	public static function get_singular($type) {
		return substr($type, 0, strlen($type)-1);
	}
	public static function get_plural($type) {		
		if (strrpos($type, 'man')!==false) {
			if (strlen(substr($type, strrpos($type, 'man')))==3) {
				return substr_replace($type, 'men', strrpos($type, 'man'));
			}
		}
		return $type.'s';
	}
	
	public static function format_date($date, $format = 'Y-m-d') {
		
		$space = strpos($date, ' ');
		if ($space!==false) {
			$date = substr($date, 0, $space);
		}
		
		switch($format) {
			case 'Y-m-d':
				$date_a = explode('/', $date);
				if (count($date_a)>=3) {
					
					if (strlen($date_a[2])==2) {
						$date_a[2] = '20'.$date_a[2];
					}
					
					$date = $date_a[2].'-'.$date_a[1].'-'.$date_a[0];
				}
				break;

			case 'd/m/Y':
				$date_a = explode('-', $date);
				if (count($date_a)>=3) {
					$date = $date_a[2].'/'.$date_a[1].'/'.$date_a[0];
				}
				break;			
		}
		
		return $date;		
		
	}
	
	public static function format_number($number, $show_empty_decimals = true, $decimals='') {
		$number = $number.'';
		
		if ($decimals=='') {
			$decimals = 2;					
		}
		
		$decimal_pos = strpos($number, '.');
		if ($decimal_pos!==false) {
			$number = round(floatval($number), $decimals).'';
		} else if ($show_empty_decimals) {
			$number .= '.';
			for ($i=0;$i<$decimals;$i++) {
				$number .= '0';
			}
		}
							
		$number = str_replace('.', ',', $number);
				
		return $number;
	}
	public static function compute_number($number) {
		// convert "," to "."
		$number = str_replace(',', '.', $number);

		// remove everything except numbers and dot "."
		$number = preg_replace("/[^0-9\.-]/", "", $number);

		// remove all seperators from first part and keep the end
		if (strpos($number, '.')!==false) {
			$before = substr($number, 0, strrpos($number, '.'));
			$before = str_replace('.', '', $before);
			$after = substr($number, strrpos($number, '.'));
			$number =  $before . $after;
		}

		// return float
		return (float) $number;
	}
	
	public static function indentHTML($html, $nb = 5) {
		
		$indent = "";
		for ($i=0;$i<$nb;$i++) {
			$indent .= "\t";
		}
		
		return str_replace("\n", "\n".$indent, $html);
	}
	
}
?>