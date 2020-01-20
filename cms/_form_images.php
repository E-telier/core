	<?php
		
		$table = "_cms_images";
		$root_folder='../images/';
								
		// Traitement form //
		if (isset($_POST['submit_check'])) {
			
			$former_files = array();
		
			if (isset($HTTP_POST_FILES)) { $file_sys = $HTTP_POST_FILES; }
			else { $file_sys = $_FILES; }
					
			// FILL EVERY LANGUAGES WITH POSTED IF EMPTY //
			$exceptions = array();
			$post_keys = array_keys($_POST);
			for ($i=0;$i<count($_POST);$i++) {
				$key = $post_keys[$i];
				if (empty($_POST[$key])) {					
					$type_key = substr($key, 3);					
					if (array_search($type_key, $exceptions)===false) {
						if (isset($_POST[$_POST['post_lang']."_".$type_key])) {
							$_POST[$key] = $_POST[$_POST['post_lang']."_".$type_key];
							//echo $key.' '.$_POST[$_POST['post_lang']."_".$type_key].'<br />';
						}
					}
				}
			}
						
			// FILL EVERY LANGUAGES WITH FILES IF EMPTY //
			$exceptions = array();
			$post_keys = array_keys($file_sys);			
			for ($i=0;$i<count($file_sys);$i++) {
				$key = $post_keys[$i];
				
				$temp_lang = substr($key, 0, 2);
				if (empty($_POST[$temp_lang.'_old_image'])) {	
					// IF NO OLD IMAGE //
				
					if (empty($file_sys[$key]['name'])) {					
						$type_key = substr($key, 3);					
						if (array_search($type_key, $exceptions)===false) {
							//echo $_POST['post_lang']."_".$type_key."\n";
							if (isset($file_sys[$_POST['post_lang']."_".$type_key])) {
								$file_sys[$key] = $file_sys[$_POST['post_lang']."_".$type_key];
								//echo $file_sys[$key]."\n";
							}
						}
					}
				}
			}
				
			$result='';
			$separator='{ itemDelimiter }';
				
			$done_images = array();
			$new_global_ref = date('YmdHis');
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];	
																	
				// IMAGES //						
				@$image = strtolower($file_sys[$temp_lang.'_image']['name']);
				$imgMod = false;
				$name = eText::str_to_url($_POST[$temp_lang.'_name']);
				$folder = $_POST[$temp_lang.'_folder'];
				if (!empty($_POST[$temp_lang.'_new_folder'])) { $folder = $_POST[$temp_lang.'_new_folder']; }
				
				if ($folder!='') {
					$folder.='/';
				}
											
				if (empty($image) == false) {
					$file_datas = $file_sys[$temp_lang.'_image'];
									
					// PARAMS //					
					if (empty($name)) { 
						$name = substr($image, 0, strrpos($image, '.'));						
						if (!mb_detect_encoding($name, 'UTF-8', true)) { echo $name; $name = utf8_encode($name); echo $name; } // DETECT WINDOWS FILE						
						if (empty($_POST[$temp_lang.'_description'])) { $_POST[$temp_lang.'_description'] = $name; }
						$name = eText::str_to_url($name); 						
					}
					
					// INCREMENT NAME //
					$rq = "SELECT id FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE (name='$name' OR name REGEXP '^".$name."_[0-9]*$') AND global_ref<>$addmod";
					$result_datas = eMain::$sql->sql_to_array($rq);
					$nb = $result_datas['nb'];;
					if ($nb>0) {
						$name .= '_'.($nb+1);
					}
										
					if (empty($_POST[$temp_lang.'_description'])) { $_POST[$temp_lang.'_description'] = $name; }			
					
					if (array_search($name, $done_images)===false) {
						array_push($done_images, $name);
						
						$type_file = $file_datas['type'];
						$extension = eFile::get_extension($type_file);
						
						$width=$_POST[$temp_lang.'_width'];
						$height=$_POST[$temp_lang.'_height'];
						$rotation = $_POST[$temp_lang.'_rotation'];	
						$resize = $_POST[$temp_lang.'_resize'];
						$result = eFile::add_image($file_datas, $folder, $name, $extension, $width, $height, $rotation, $resize);
						
						if (is_array($result)===true) {
							$imgMod = true;
							
							$width = $result['width'];
							$height = $result['height'];
							
						} else {
							eMain::add_error($result);
						}
						
					} // END IF ALREADY TREATED
					else {
						$imgMod = true;
						
						$width = (!empty($_POST[$temp_lang.'_width'])) ? $_POST[$temp_lang.'_width'] : $width;
						$height = (!empty($_POST[$temp_lang.'_height'])) ? $_POST[$temp_lang.'_height'] : $height;
					}
				
				} else if (!empty($_POST[$temp_lang.'_old_image'])) {
					// EDIT EXISTING IMAGE DATAS //					
					$extension = $_POST[$temp_lang.'_old_extension'];
					
					$former_folder = $_POST[$temp_lang.'_old_folder'];
					if ($former_folder!='') { $former_folder.='/'; }
										
					if ($_POST[$temp_lang.'_old_image']!=$name || $former_folder!=$folder) {
						// COPY FOR THIS LANGUAGE (others may still use old name) //
						copy($root_folder.$former_folder.$_POST[$temp_lang.'_old_image'].'_full'.".".$extension, $root_folder.$folder.$name.'_full'.".".$extension);
						copy($root_folder.$former_folder.$_POST[$temp_lang.'_old_image'].".".$extension, $root_folder.$folder.$name.".".$extension);
						
						$former_files[] = array(
							'path'=>$root_folder.$former_folder.$_POST[$temp_lang.'_old_image'].".".$extension,
							'fullpath'=>$root_folder.$former_folder.$_POST[$temp_lang.'_old_image'].'_full'.".".$extension,
							'name'=>$_POST[$temp_lang.'_old_image'],
							'folder'=>substr($former_folder, 0, -1)
						);
						
					}
					
					$rotation = ($_POST[$temp_lang.'_rotation']=='auto') ? 0 : $_POST[$temp_lang.'_rotation'];					
										
					$full_size = getimagesize($root_folder.$folder.$name.'_full'.".".$extension);
					if ($rotation!=0) {
						// ROTATE FULL //
						$src_path = $root_folder.$folder.$name.'_full'.".".$extension;
						$dest_path = $src_path;
						eFile::resize_image($src_path, $dest_path, $full_size[0], $full_size[1], 'deform', $full_size, null, $rotation);						
						$full_size = getimagesize($root_folder.$folder.$name.'_full'.".".$extension);
					}
										
					$size = getimagesize($root_folder.$folder.$name.".".$extension);
					$dest_size = eFile::fill_empty_size_within_ratio($full_size, $_POST[$temp_lang.'_width'], $_POST[$temp_lang.'_height']);
					$width = $dest_size[0];
					$height = $dest_size[1];
					if ($height != $size[1] || $width != $size[0] || $rotation!=0 || $_POST[$temp_lang.'_resize']!=$_POST[$temp_lang.'_old_resize']) {
						// RESAMPLE IMAGE //
						$src_path = $root_folder.$folder.$name.'_full'.".".$extension;
						$dest_path = $root_folder.$folder.$name.".".$extension;
						eFile::resize_image($src_path, $dest_path, $width, $height, $_POST[$temp_lang.'_resize'], $full_size);
					}
					
					$imgMod = true;					
				}
				
				if ($imgMod == true) {
					$openbig=0;
					if (isset($_POST[$temp_lang.'_openbig'])) {
						$openbig=1;
					}
					$slideshow_pos=intval($_POST[$temp_lang.'_slideshow_pos']);					
					$gallery_pos=intval($_POST[$temp_lang.'_gallery_pos']);
					
					if (empty($_POST[$temp_lang.'_align'])) {
						$_POST[$temp_lang.'_align'] = 'left';
					}
										
					$dest_path = $root_folder.$folder.$name.".".$extension;
					$weight = filesize($dest_path);
					
					$folder = substr($folder, 0, -1);
									
					if ($addmod==0) {
						$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."$table 
						(global_ref, name, folder, extension, description, width, height, weight, resize, align, openbig, slideshow_pos, gallery_pos) 
							VALUES 
							('".eMain::$sql->protect_sql($new_global_ref)."', 
							'".eMain::$sql->protect_sql($name)."', 
							'".eMain::$sql->protect_sql($folder)."', 
							'".eMain::$sql->protect_sql($extension)."', 
							'".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', 
							".intval($width).", 
							".intval($height).", 
							".intval($weight).", 
							'".eMain::$sql->protect_sql($_POST[$temp_lang.'_resize'])."', 
							'".eMain::$sql->protect_sql($_POST[$temp_lang.'_align'])."', 
							".intval($openbig).", 
							".intval($slideshow_pos).", 
							".intval($gallery_pos).");";
					} else {
						$rq = "UPDATE ".eParams::$prefix.'_'.$temp_lang."$table SET name='".eMain::$sql->protect_sql($name)."', folder='".eMain::$sql->protect_sql($folder)."', extension='".eMain::$sql->protect_sql($extension)."', description='".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', width=".$width.", height=".$height.", weight=".$weight.", resize='".eMain::$sql->protect_sql($_POST[$temp_lang.'_resize'])."', align='".eMain::$sql->protect_sql($_POST[$temp_lang.'_align'])."', openbig=".$openbig.", slideshow_pos=".$slideshow_pos.", gallery_pos=".$gallery_pos." WHERE global_ref=".$addmod.";";
					}
					
					$result = '';
					if (!eMain::$sql->sql_query($rq)) {
						$error_msg = "ERROR CMS002 : Unable to process the query<br />$rq<br />".eMain::$sql->get_error();
						echo "<div class=\"error\">".$error_msg."</div>";
						
						$result .= $error_msg."\n";
						
					} else {
						echo '<div class="success">Données '.$temp_lang.' correctement enregistrées !</div>';
						
						$success_msg = "lang=".$temp_lang . $separator;
						$success_msg .= "name=".$name . $separator;
						$success_msg .= "extension=".$extension . $separator;
						$success_msg .= "description=".$_POST[$temp_lang.'_description'] . $separator;
						$success_msg .= "width=".$width . $separator;
						$success_msg .= "height=".$height . $separator;
						$success_msg .= "align=".$_POST[$temp_lang.'_align'] . $separator;
						$success_msg .= "resize=".$_POST[$temp_lang.'_resize'] . $separator;		
						
						$result .= $success_msg."\n";						
						
					}
										
				} // END IF IMG MOD //			
			} // END FOR LANGUAGES
			
			if (isset($_POST['ajax'])) {
				echo "<!-- AJAX RESULT -->".$result."<!-- END OF AJAX RESULT -->";				
			}
			
			// DELETE TEMP FILE //
			if (count($done_images)>0) {
				for ($l=0;$l<$nb_lang;$l++) {
					$temp_lang = eParams::$available_languages[$l];
					
					$tmp_file = $file_sys[$temp_lang.'_image']['tmp_name'];
					//echo $tmp_file."<br />\n";
					if (!empty($tmp_file)) {					
						$tmp_filename = substr($tmp_file, strrpos($tmp_file, '\\'));	
						$tmp_filename = substr($tmp_filename, strrpos($tmp_filename, '/'));						
						
						$tmp_folder = "tmp/";	
						
						if (file_exists($tmp_folder.$tmp_filename)) {							
							unlink($tmp_folder.$tmp_filename);
						}
					} // IF NOT EMPTY FILE
					
				} // END FOR LANGUAGES
			} // END IF FILE UPLOADED
						
			// DELETE UNUSED FILE //
			for($i=0;$i<count($former_files);$i++) {
				if (file_exists($former_files[$i]['path'])) {
					$unused = true;
					for ($l=0;$l<$nb_lang;$l++) {
						$temp_lang = eParams::$available_languages[$l];	
						$rq = "SELECT id FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE name='".$former_files[$i]['name']."' AND folder='".$former_files[$i]['folder']."' LIMIT 1;";						
						if (eMain::$sql->sql_to_num($rq)>0) {							
							$unused = false;
							break;
						}
					}
					if ($unused) {						
						unlink($former_files[$i]['path']);
						unlink($former_files[$i]['fullpath']);					
					}
				}
			}
			// DELETE UNUSED FOLDER //
			for($i=0;$i<count($former_files);$i++) {
				if (is_dir($root_folder.$former_files[$i]['folder'])) {
					if (eFile::is_dir_empty($root_folder.$former_files[$i]['folder'])) {
						rmdir($root_folder.$former_files[$i]['folder']);
					}
				}
			}
			
			
		} // END IF SUBMIT
				
		// Affichage datas //
		if ($addmod==0) {
		
			$content = array();
			$content['name'] = isset($_GET['name']) ? $_GET['name'] : "";
			$content['description']="";
			$content['width'] = isset($_GET['width']) ? $_GET['width'] : "";
			$content['height'] = isset($_GET['height']) ? $_GET['height'] : "";
			$content['align']="";
			$content['openbig']=0;
			$content['slideshow_pos']=0;
			$content['gallery_pos']=0;
			$content['resize']='crop';
			$content['folder'] = '';
			
			for ($l=0;$l<$nb_lang;$l++) {
				$values[eParams::$available_languages[$l]] = $content;
			}
		
			$title = "Ajout d'une nouvelle image";
		} else {
		
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
				
				$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE global_ref=$addmod;";
				$result_datas = eMain::$sql->sql_to_array($rq);
				$content = $result_datas['datas'][0];
				
				$values[$temp_lang] = $content;				
			}
			
			$title = 'Modification de l\'image référence "'.$values[$_SESSION['lang']]['name'].'"';
					
		}
?>
	<!-- START OF PAGE -->
	<h1><?php echo eText::style_to_html($title); ?></h1>
	
	<br />
	<?php 
	if (!isset($_GET['popup'])) { 
		include('_controls_lang.php'); 
	} else {
	?>
	<script type="text/javascript">
		<!--
		$(document).ready(function() { 
		
			//alert(current_lang);
		
			$('.popup .form_lang').css({'display':'none'});
			$('.popup #form_'+current_lang).css({'display':'block'});
		});
		-->		
	</script>
	<?php
	}
	?>
	
	<div class="addmod">
	
	<script type="text/javascript">
	<!--
		var maxWidth = 960;
		function updateWidth(domObj, width, height) {
		
			var tName = domObj.name.split('_');
			var tLang = tName[0];
			var tWidthName = tLang+'_'+tName[1];
		
			if (domObj.value=='') {
				$('input[name="'+tWidthName+'_percent"]').val('');
				$('input[name$="'+tWidthName+'"]').val('');
			} else {
				var tValue;
				if (typeof width === 'undefined') {
					tValue = parseInt(domObj.value);
				} else {
					tValue = width;
					$('input[name="'+tLang+'_width"]').val(width);
					$('input[name="'+tLang+'_height"]').val(height);
					tWidthName = tLang+'_width';
				}
				
				if (domObj.name==tWidthName || typeof width !== 'undefined') {
					// Px to % //
					var percent = (100*tValue)/maxWidth;
					percent = Math.round(percent);
					//alert(tWidthName+'_percent');
					$('input[name="'+tWidthName+'_percent"]').val(percent);
				} else {
					// % to PX
					var pixels = (tValue/100)*maxWidth;
					pixels = Math.round(pixels);
					$('input[name="'+tWidthName+'"]').val(pixels);
					$('input[name="'+tLang+'_height"]').val('');
				}
			}
		}
				
		$(document).ready(function() {
			$('input[name$="_width"]').each(function() {				
				updateWidth(this);
			});
		});
	-->
	</script>	
	
	<form method="post" name="add_<?php echo $page; ?>" id="add_<?php echo $page; ?>" enctype="multipart/form-data">
		
<?php
	for ($l=0;$l<$nb_lang;$l++) {
		$temp_lang = eParams::$available_languages[$l];
?>	
	<div id="form_<?php echo $temp_lang; ?>" class="form_lang">

		<b>Nom : </b><br />
		<input type="text" value="<?php echo $values[$temp_lang]['name']; ?>" name="<?php echo $temp_lang; ?>_name" size="96" />
		<br /><br />
		<b>Dossier : </b><br />
		<select name="<?php echo $temp_lang; ?>_folder">
			<option value="">root</option>
<?php
		$folders_datas = eMain::$sql->sql_to_array("SELECT DISTINCT folder FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE folder<>'' ORDER BY folder;");
		for($f=0;$f<$folders_datas['nb'];$f++) {
			$selected = '';
			if ($folders_datas['datas'][$f]['folder']==$values[$temp_lang]['folder']) { $selected = 'selected="selected"';}
?>
			<option <?php echo $selected; ?>><?php echo eText::iso_htmlentities($folders_datas['datas'][$f]['folder']); ?></option>
<?php
		}
?>
		</select>
		<input type="text" name="<?php echo $temp_lang; ?>_new_folder" value="" placeholder="new folder name" />
		<input type="hidden" name="<?php echo $temp_lang; ?>_old_folder" value="<?php echo $values[$temp_lang]['folder']; ?>" />
		<br /><br />
		<b>Description : </b><br />
		<input type="text" value="<?php echo $values[$temp_lang]['description']; ?>" name="<?php echo $temp_lang; ?>_description" size="96" />
		<br /><br />		
		<b>Largeur et Hauteur : (vides = défaut = dimensions d'origine)</b><br />
		Largeur <input type="text" value="<?php echo $values[$temp_lang]['width']; ?>" name="<?php echo $temp_lang; ?>_width" size="6" onchange="updateWidth(this);" class="auto_width" />	pixels = <input type="text" value="" name="<?php echo $temp_lang; ?>_width_percent" size="3" onchange="updateWidth(this);" class="auto_width" /> % (de la zone de texte du site)<br />
		Hauteur <input type="text" value="<?php echo $values[$temp_lang]['height']; ?>" name="<?php echo $temp_lang; ?>_height" size="6" class="auto_width" /> pixels
		<br /><br />
		<b>Méthode de redimensionnement : (si largeur/hauteur spécifiée)</b><br />
		<input type="radio" name="<?php echo $temp_lang.'_resize'; ?>" value="crop" <?php if ($values[$temp_lang]['resize']=='crop') { ?>checked="checked"<?php } ?> /> Couper surplus
		<input type="radio" name="<?php echo $temp_lang.'_resize'; ?>" value="deform" <?php if ($values[$temp_lang]['resize']=='deform') { ?>checked="checked"<?php } ?> /> Déformer
		<input type="radio" name="<?php echo $temp_lang.'_resize'; ?>" value="full" <?php if ($values[$temp_lang]['resize']=='full') { ?>checked="checked"<?php } ?> /> Garder tout
		<input type="hidden" name="<?php echo $temp_lang.'_old_resize'; ?>" value="<?php echo $values[$temp_lang]['resize']; ?>" />
		<br /><br />
		<b>Rotation : </b><br />
		<select name="<?php echo $temp_lang; ?>_rotation">
			<option value="auto">Automatique</option>
			<option value="-90">Sur la droite</option>
			<option value="90">Sur la gauche</option>
			<option value="180">Retournée</option>
		</select>
		<br />
		<br />
		<b>Alignement : </b><br />
		<select name="<?php echo $temp_lang; ?>_align">
			<option value="">Choisir un alignement horizontal</option>
			<option value="left" <?php if ($values[$temp_lang]['align']=="left") { echo "selected"; } ?> >Gauche</option>
			<option value="center" <?php if ($values[$temp_lang]['align']=="center") { echo "selected"; } ?> >Centre</option>
			<option value="right" <?php if ($values[$temp_lang]['align']=="right") { echo "selected"; } ?> >Droite</option>
		</select>	
		<br /><br />		
		<b>Cliquer pour agrandir : </b>
		<input type="checkbox" value="on" name="<?php echo $temp_lang; ?>_openbig" <?php if ($values[$temp_lang]['openbig']==1) { ?>checked="checked"<?php } ?> />
		<br /><br />
		<b>Intégrer au slideshow : </b>
		<input type="text" size="3" value="<?php echo $values[$temp_lang]['slideshow_pos']; ?>" name="<?php echo $temp_lang; ?>_slideshow_pos" class="auto_width" /> (0 = défaut = absent du slideshow)
		<br /><br />
		<b>Intégrer à la galerie : </b>
		<input type="text" size="3" value="<?php echo $values[$temp_lang]['gallery_pos']; ?>" name="<?php echo $temp_lang; ?>_gallery_pos" onchange="return false; if (this.value>0) { updateWidth(this, 200, 150); }" class="auto_width" /> (0 = défaut = absent de la galerie)
		<br /><br />
		<b>Fichier : </b><br />
		<input type="hidden" name="<?php echo $temp_lang; ?>_old_image" value="<?php if ($addmod!=0) { echo $values[$temp_lang]['name']; } ?>" />
		<input type="hidden" name="<?php echo $temp_lang; ?>_old_extension" value="<?php if ($addmod!=0) { echo $values[$temp_lang]['extension']; } ?>" />
		<input type="file" name="<?php echo $temp_lang; ?>_image" accept="image/*" /><br />
		<?php if ($addmod!=0) { 
			$width = $values[$temp_lang]['width'];
			$height = $values[$temp_lang]['height'];
			if ($width>800) {
				$ratio = $width/$height;
				$width = 800;
				$height = round(800/$ratio);
				echo "ATTENTION : aperçu redimensionné à $width*$height";
			}
			$folder = $values[$temp_lang]['folder'];
			if ($folder!='') { $folder .= '/'; }
			echo '<img src="'.$root_folder.$folder.$values[$temp_lang]['name'].'.'.$values[$temp_lang]['extension'].'?d='.date("YmdHis").'" width="'.$width.'" height="'.$height.'" >'; } 
		?>
		<br /><br />
		<div class="float-right">
			<div class="submit button" onclick="$('#add_<?php echo $page; ?> .post_lang').val('<?php echo $temp_lang; ?>'); <?php if (!isset($_GET['popup'])) { ?>document.forms['add_<?php echo $page; ?>'].submit()<?php } else { ?>eForm.asyncSubmitForm('add_<?php echo $page; ?>')<?php }?>;">ENREGISTRER</div>
		</div>
		<div class="clear"> </div>
		
	</div><!-- END OF LANG CONTAINER -->
<?php
	} // END FOR LANGUAGES
?>	
		<input type="hidden" name="submit_check" value="1" />
		<input type="hidden" name="post_lang" class="post_lang" value="default" />
	</form>
	</div> <!--END OF ADDMOD -->
	<!-- END OF PAGE -->