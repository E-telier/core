<?php

		$dossier = "photos_datas/";
		
		if (count($currentDatas)==1) {
		
?>
			<h1>Photos</h1>
			<br />
<?php
		
			$repertoire = opendir($dossier);
			$liste_dossier=array();
			while ($le_dossier = readdir($repertoire)) {
				if ($le_dossier!="." && $le_dossier!="..") {
					$ext = strtolower(substr($le_dossier, strlen($le_dossier)-5, 5));
					if (substr_count($ext, ".")<1) {
						$liste_dossier[] = $le_dossier;
					}
				}
			}
			
			sort($liste_dossier);
						
			$nb_dossiers = count($liste_dossier);
			for ($d=0;$d<$nb_dossiers;$d++) {
			
				$dir = $liste_dossier[$d];
				$realpath = $dossier.$dir;
										
				if(file_exists($realpath) && is_dir($realpath)) {
					$handle = opendir($realpath);
					while (false !== ($thumbfile = readdir($handle))) {
						//echo "<!-- $thumbfile -->";
						if (strpos($thumbfile, ".jpg") > 0) {
							//$thumbpath = $dir.$thumbfile;
							break;
						}
					}
					closedir($handle);
										
				} else {
					$thumbfile = "next.gif";
				}
			
				$fakedir = curPageURL()."/".$dir;
?>			
			<div class="album">
				<div class="img left"><a href="<?php echo $fakedir; ?>"><img src="<?php echo $realpath."/".$thumbfile; ?>" width="160" height="120" alt="<?php echo styleToHTML($dir); ?>" border="0" /></a></div>
				<div class="label"><a href="<?php echo $fakedir; ?>"><?php echo styleToHTML($dir); ?></a></div>
			</div>
<?php
				
			} // END OF FOR FOLDERS //
			
		} // END OF IF NO SELECTED FOLDER //

		else {
							
			$repertoire = opendir($dossier.$currentDatas[1]);
			$liste_image=array();
			while ($l_image = readdir($repertoire)) {
				if ($l_image!="." && $l_image!="..") {
					$ext = strtolower(substr($l_image, strlen($l_image)-3, strlen($l_image)));
					if ($ext=="jpg" || $ext=="gif" || $ext=="png" || $ext=="avi" || $ext=="wmv" || $ext=="wav") {
						$liste_image[] = $l_image;
					}
				}
			}
			sort($liste_image);
			
			$nb_img = count($liste_image);			
			if (count($currentDatas)>2) { 
				$num = $currentDatas[2];			
				if ($num>$nb_img) { $num = 1; }
				else if ($num < 1) { $num = $nb_img; }
				
				$fakeurl = substr($currentURL, 0, strrpos($currentURL, "/"));
				
			}
			else { 
				$num=1;
				$fakeurl = $currentURL;
			}			
			
			$tampon = 3;
			
			$premier = $num-$tampon;
			if ($premier<0) { 
				$diffAvant = $premier; 
				$premier=0; 
			} 
			else { $diffAvant = 0; }
			$dernier = $num+$tampon+abs($diffAvant);
			if ($dernier>$nb_img) { 
				$diffApres = $dernier-$nb_img; 
				$dernier = $nb_img; } 
			else { $diffApres = 0; }
			$premier = $premier - $diffApres;
			if ($premier<0) { $premier = 0; }
			
			echo "
				<h1><a href=\"javascript:history.back()\">Photos</a> &gt; ".$currentDatas[1]."</h1>
				<div class=\"right\">Photo ".$num." / ".$nb_img."</div>
				<div id=\"photos_galery\">					
			";
			for ($l=$premier;$l<$dernier;$l++) {
			
				if ($l==$premier) {
					$previous = $num-$tampon;
					if ($previous < 1) { $previous = $nb_img+$previous; }
					$versphoto = "<img src=\"".$folderURL."design/previous.gif\" width=\"64\" height=\"48\" class=\"mini-photo\" alt=\"pr&eacute;c&eacute;dent\" />";
					echo "
					<a href=\"".$fakeurl."/".($previous)."\">".$versphoto."</a> &nbsp; &nbsp; &nbsp; &nbsp;
					";
				} 
																						
				$image_name = $liste_image[$l];				
				if ($l+1==$num) { $class="selected"; } else { $class=""; }				
				$versphoto = "<img src=\"".$folderURL.$dossier.$currentDatas[1]."/".$image_name."\" width=\"64\" height=\"48\" alt=\"".$image_name."\" class=\"".$class."\" />";							
				echo "
				<a href=\"".$fakeurl."/".($l+1)."\">".$versphoto."</a>
				";
					
				if ($l==$dernier-1) {
					$next = $num + $tampon;
					if ($next > $nb_img) { $next = 1+($next-$nb_img); }
					$versphoto = "<img src=\"".$folderURL."design/next.gif\" width=\"64\" height=\"48\" class=\"mini-photo\" alt=\"suivant\" />";
					echo "
					&nbsp; &nbsp; &nbsp; &nbsp; <a href=\"".$fakeurl."/".($next)."\">".$versphoto."</a>
					";
				}
			}
			echo "
				</div><!-- END OF PHOTOS GALERY -->
				
			";
			
			$caract = getimagesize($dossier.$currentDatas[1]."/".$liste_image[$num-1]);
			$format = Array(640,480); 
				
			if ($caract[0]>$format[0]) { 
				$width=$format[0];
				$height = ($format[0]/$caract[0])*$caract[1];
			} else { 
				$width = $caract[0];
				$height = $caract[1];
			}
			if ($height>$format[1]) {
				$width = ($format[1]/$height)*$width;
				$height=$format[1];
			}
			echo "
			<div id=\"photo\">
				<a href=\"".$folderURL.$dossier.$currentDatas[1]."/".$liste_image[$num-1]."\" target=\"_blank\"><img src=\"".$folderURL.$dossier.$currentDatas[1]."/".$liste_image[$num-1]."\" width=\"".$width."\" height=\"".$height."\" border=\"0\" alt=\"".$folderURL.$dossier.$currentDatas[1]."/".$liste_image[$num-1]."\" title=\"".$folderURL.$dossier.$currentDatas[1]."/".$liste_image[$num-1]."\" /></a>
			</div>
			";
			
			
		}
?>