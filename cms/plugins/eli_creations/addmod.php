	<?php
	if ($page=='eli_creations') {
	
		$table = "_back_creations";	
		$base_wysiwyg_name = "wysiwyg_name"; 		
		$new_global_ref = date('YmdHis');
		
		if ($addmod==0) {
			$global_ref = $new_global_ref;
		} else {
			$global_ref = $addmod;
		}
							
		// Traitement form //
		if (isset($_POST['submit_check'])) {
			
			if (isset($HTTP_POST_FILES)) { $file_sys = $HTTP_POST_FILES; }
			else { $file_sys = $_FILES; }
										
			// FILL EVERY LANGUAGES WITH POSTED IF EMPTY //
			$exceptions = array();
			$post_keys = array_keys($_POST);
			for ($i=0;$i<count($_POST);$i++) {
				$key = $post_keys[$i];
				if (empty($_POST[$key])) {					
					$type_key = substr($key, 3);
					if (array_search($type_key, $exceptions)==false) {
						if (isset($_POST[$_POST['post_lang']."_".$type_key])) {
							$_POST[$key] = $_POST[$_POST['post_lang']."_".$type_key];
						}
					}
				}
			}
						
			// FORCE COPY DELETE CHECKBOX //
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
				$i=0;
				while(isset($_POST[$temp_lang.'_'.$i.'_img_global_ref'])) {
					if (isset($_POST[$temp_lang.'_'.$i.'_img_delete'])) {
						for ($l2=0;$l2<$nb_lang;$l2++) {
							$temp_lang2 = eParams::$available_languages[$l2];
							//echo 'copy '.($temp_lang.'_'.$i.'_img_delete').' to '.($temp_lang2.'_'.$i.'_img_delete')."<br />\n";
							$_POST[$temp_lang2.'_'.$i.'_img_delete'] = $_POST[$temp_lang.'_'.$i.'_img_delete'];
						}
					}
					$i++;
				}
			}
								
			// FILL EVERY LANGUAGES WITH FILES IF EMPTY //
			$exceptions = array();
			$post_keys = array_keys($file_sys);			
			for ($i=0;$i<count($file_sys);$i++) {
				$key = $post_keys[$i];
				
				$temp_lang = substr($key, 0, 2);
				//if (empty($_POST[$temp_lang.'_old_image'])) {	
					// IF NO OLD IMAGE //
															
					if (empty($file_sys[$key]['name']) || (is_array($file_sys[$key]['name']) && empty($file_sys[$key]['name'][0]))) {					
						$type_key = substr($key, 3);					
						if (array_search($type_key, $exceptions)===false) {
							//echo $key.'-'.$_POST['post_lang']."_".$type_key."<br />\n";
							if (isset($file_sys[$_POST['post_lang']."_".$type_key])) {
								$file_sys[$key] = $file_sys[$_POST['post_lang']."_".$type_key];
								//print_r($file_sys[$key]);
							}
						}
					}
				//}
			}
			
			$done_images = array();
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
				
				if (!empty($_POST[$temp_lang.'_newcat'])) {
					$_POST[$temp_lang.'_cat'] = $_POST[$temp_lang.'_newcat'];
				}
				if (!empty($_POST[$temp_lang.'_old_image'])) {
					$filename = $_POST[$temp_lang.'_old_image'];
				}
				
				$root_img = '../images/';
				$folder = 'creations/'.eText::str_to_url($_POST[$temp_lang.'_title']);
								
				if (empty($file_sys[$temp_lang.'_image']['name']) == false) {
					
					// MAIN IMAGE //
					$file_datas = $file_sys[$temp_lang.'_image'];
					$filename = $file_sys[$temp_lang.'_image']['name'];
					$name = substr($filename, 0, strrpos($filename, '.'));
					$extension = eFile::get_extension($file_datas['type']);
					$width=160;
					$height=120;
					$rotation = 0;	
					$resize = 'crop';
					
					if (array_search($name, $done_images)===false) {
						array_push($done_images, $name);
						
						$result = eFile::add_image($file_datas, $folder, $name, $extension, $width, $height, $rotation, $resize);
						
						if (!is_array($result)) {
							eMain::add_error($result);
						} else {
							
							$width = $result['width'];
							$height = $result['height'];
							$weight = $file_datas['size'];
							
						}
					}
					
					$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."_cms_images 
					(global_ref, name, folder, extension, description, width, height, weight, resize, align, openbig, slideshow_pos, gallery_pos) 
					VALUES 
					('".eMain::$sql->protect_sql($global_ref)."', 
					'".eMain::$sql->protect_sql($name)."', 
					'".eMain::$sql->protect_sql($folder)."', 
					'".eMain::$sql->protect_sql($extension)."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
					".intval($width).", 
					".intval($height).", 
					".intval($weight).", 
					'".eMain::$sql->protect_sql($resize)."', 
					'center', 
					1, 
					0, 
					1);";
					
					if (!eMain::$sql->sql_query($rq)) {							
						eMain::add_error('image could not be added to DB');							
					} else {
						echo "<h1>Image enregistrée !</h1>\n";
					}
					
				}			
												
				if ($addmod==0) {
					$rq = "INSERT INTO ".eParams::$prefix."_".$temp_lang."$table (global_ref, title, cat, role, tech, languages, description, target, image, annee, hidden, visible) VALUES (
						'".$new_global_ref."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_cat'])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_role'])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_tech'])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_languages'])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."', 
						'".eMain::$sql->protect_sql($_POST[$temp_lang.'_target'])."', 
						'".eMain::$sql->protect_sql($filename)."', 
						'".intval($_POST[$temp_lang.'_annee'])."', 
						'".intval($_POST[$temp_lang.'_hidden'])."', 
						'".intval($_POST[$temp_lang.'_visible'])."');";
				} else {
					$rq = "UPDATE ".eParams::$prefix."_".$temp_lang."$table SET 
						title='".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
						cat='".eMain::$sql->protect_sql($_POST[$temp_lang.'_cat'])."', 
						role='".eMain::$sql->protect_sql($_POST[$temp_lang.'_role'])."', 
						tech='".eMain::$sql->protect_sql($_POST[$temp_lang.'_tech'])."', 
						languages='".eMain::$sql->protect_sql($_POST[$temp_lang.'_languages'])."', 
						target='".eMain::$sql->protect_sql($_POST[$temp_lang.'_target'])."', 
						description='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."', 
						image='".eMain::$sql->protect_sql($filename)."', 
						annee='".intval($_POST[$temp_lang.'_annee'])."', 
						hidden='".intval($_POST[$temp_lang.'_hidden'])."', 
						visible='".intval($_POST[$temp_lang.'_visible'])."' 
					WHERE global_ref='".$addmod."';";
				}
				if (!eMain::$sql->sql_query($rq)) {					
					eMain::add_error('unable to process the query');					
				} else {
					echo "<h1>Données correctement enregistrées !</h1>";
				}
				
				$i=0;
				while(isset($_POST[$temp_lang.'_'.$i.'_img_global_ref'])) {
					if (isset($_POST[$temp_lang.'_'.$i.'_img_delete'])) {
						
						$img_datas = eMain::$sql->sql_to_array("SELECT gallery_pos FROM ".eParams::$prefix."_".$temp_lang."_cms_images WHERE global_ref='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$i.'_img_global_ref'])."' LIMIT 1");
						if ($img_datas['nb']==0) {
							die("SELECT gallery_pos, folder FROM ".eParams::$prefix."_".$temp_lang."_cms_images WHERE global_ref='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$i.'_img_global_ref'])."' LIMIT 1");
							$gallery_pos = 0; 
						}
						else { $gallery_pos = $img_datas['datas'][0]['gallery_pos']; }
												
						$rq_img = "DELETE FROM ".eParams::$prefix."_".$temp_lang."_cms_images WHERE global_ref=".$_POST[$temp_lang.'_'.$i.'_img_global_ref'];
						eMain::$sql->sql_query($rq_img);
						
						$rq_img = "UPDATE ".eParams::$prefix."_".$temp_lang."_cms_images SET global_ref=CONCAT('".$global_ref."', gallery_pos-1), gallery_pos=gallery_pos-1 WHERE gallery_pos>".$gallery_pos;
						eMain::$sql->sql_query($rq_img);
						
						$small_img = $_POST[$temp_lang.'_'.$i.'_img_filename'];
						$full_img = eFile::add_suffix($small_img);
						
						$small_img = $root_img.$folder.'/'.$small_img;
						$full_img = $root_img.$folder.'/'.$full_img;
						
						if (file_exists($full_img)) { unlink($full_img); } else { echo $full_img." doesn't exist <br />\n"; }
						if (file_exists($small_img)) { unlink($small_img); } else { echo $small_img." doesn't exist <br />\n"; }
						
					} else if (!empty($_POST[$temp_lang.'_'.$i.'_img_description'])) {
						$rq_img = "UPDATE ".eParams::$prefix."_".$temp_lang."_cms_images SET description=\"".$_POST[$temp_lang.'_'.$i.'_img_description']."\" WHERE global_ref='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$i.'_img_global_ref'])."'";
						eMain::$sql->sql_query($rq_img);
					}
					if (isset($rq_img)) {  }
					
					$i++;
				}
				
				// Secondary Images //
				$gallery_pos = eMain::$sql->sql_to_array("SELECT gallery_pos FROM ".eParams::$prefix."_".$temp_lang."_cms_images WHERE folder='".$folder."' ORDER BY gallery_pos DESC LIMIT 1");
				if ($gallery_pos['nb']==0) { $gallery_pos = 0; }
				else { $gallery_pos = $gallery_pos['datas'][0]['gallery_pos']; }
												
				for ($m=0;$m<count($file_sys[$temp_lang.'_images']['name']);$m++) {
					if (!empty($file_sys[$temp_lang.'_images']['name'][$m])) {
						
						$file_datas = array();
						$img_keys = array_keys($file_sys[$temp_lang.'_images']);
						for ($k=0;$k<count($img_keys);$k++) {
							$file_datas[$img_keys[$k]] = $file_sys[$temp_lang.'_images'][$img_keys[$k]][$m];
						}
																		
						$filename = $file_datas['name'];
						$name = substr($filename, 0, strrpos($filename, '.'));
						$extension = eFile::get_extension($file_datas['type']);
						$width=160;
						$height=120;
						$rotation = 0;	
						$resize = 'crop';
						
						if (array_search($name, $done_images)===false) {
							array_push($done_images, $name);
						
							$result = eFile::add_image($file_datas, $folder, $name, $extension, $width, $height, $rotation, $resize);
							
							if (!is_array($result)) {
								eMain::add_error($result);
							} else {
								
								$width = $result['width'];
								$height = $result['height'];
								$weight = $file_datas['size'];
							}
						}
						
						$gallery_pos++;
						$this_global_ref = $global_ref.$gallery_pos;
													
						$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."_cms_images 
						(global_ref, name, folder, extension, description, width, height, weight, resize, align, openbig, slideshow_pos, gallery_pos) 
						VALUES 
						('".eMain::$sql->protect_sql($this_global_ref)."', 
						'".eMain::$sql->protect_sql($name)."', 
						'".eMain::$sql->protect_sql($folder)."', 
						'".eMain::$sql->protect_sql($extension)."', 
						'', 
						".intval($width).", 
						".intval($height).", 
						".intval($weight).", 
						'".eMain::$sql->protect_sql($resize)."', 
						'center', 
						1, 
						0, 
						".($gallery_pos).");";
						
						if (!eMain::$sql->sql_query($rq)) {							
							eMain::add_error('image could not be added to DB');							
						} else {
							echo "<h1>Image enregistrée !</h1>\n";
						}
												
					}
				}
					
				
			} // END FOR LANGUAGES
			
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
					
					$tmp_file = $file_sys[$temp_lang.'_images']['tmp_name'];
					$i=0;
					while(isset($tmp_file[$i]) && !empty($tmp_file[$i])) {					
						$tmp_filename = substr($tmp_file[$i], strrpos($tmp_file[$i], '\\'));	
						$tmp_filename = substr($tmp_filename, strrpos($tmp_filename, '/'));						
						
						$tmp_folder = "tmp/";	
						
						if (file_exists($tmp_folder.$tmp_filename)) {							
							unlink($tmp_folder.$tmp_filename);
						}
						
						$i++;
					} // IF NOT EMPTY FILE
					
				} // END FOR LANGUAGES
			} // END IF FILE UPLOADED
						
		}
		
		// Affichage datas //		
		$values = array();
		if ($addmod==0) {
		
			$content = array();			
			$content['title']="";
			$content['cat']="";
			$content['role']="";
			$content['tech']="";
			$content['languages']="";
			$content['description']="";
			$content['target']="";
			$content['annee']="";
			$content['image']='';
			$content['hidden']='0';
			$content['visible']='1';
			$content['folder']='creations';
			$content['global_ref']=$new_global_ref;
						
			for ($l=0;$l<$nb_lang;$l++) {
				$values[eParams::$available_languages[$l]] = $content;
			}
			
			$title = "Ajout d'une création";
			
		
		} else {
						
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
								
				$rq = "SELECT * FROM ".eParams::$prefix."_".$temp_lang."$table WHERE global_ref=$addmod;";
				
				$result_datas = eMain::$sql->sql_to_array($rq);
				$content = $result_datas['datas'][0];

				$values[$temp_lang] = $content;
				$values[$temp_lang]['folder'] = 'creations/'.eText::str_to_url($values[$temp_lang]['title']);
			}
						
			$title = "Modification de la création \"".$values[$_SESSION['lang']]['title']."\"";
						
		}
	
		if (eMain::get_errors_nb()>0) {
			eMain::show_errors();
		}
	
?>
	<h1><?php echo eText::style_to_html($title); ?></h1>
	<br />
<?php include('_controls_lang.php'); ?>
	<div class="addmod">
	<form method="post" name="add" enctype="multipart/form-data">
<?php
	for ($l=0;$l<$nb_lang;$l++) {
		$temp_lang = eParams::$available_languages[$l];
?>		
	<div id="form_<?php echo $temp_lang; ?>" class="form_lang">
		<table cellspacing="0">
			<tr>
				<td width="30%">Titre : <br /><small>(Vide = pas affiché)</small></td>
				<td>
					<input type="text" value="<?php echo $values[$temp_lang]['title']; ?>" name="<?php echo $temp_lang; ?>_title" size="64" />
				</td>
			</tr>	
			<tr>
				<td>Année : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['annee']; ?>" name="<?php echo $temp_lang; ?>_annee" size="4" /></td>
			</tr>
			<tr>
				<td>Catégorie : </td>
				<td>
					<select name="<?php echo $temp_lang; ?>_cat">
<?php
					$rqCat = "SELECT DISTINCT cat FROM ".eParams::$prefix."_".$temp_lang.$table;
					$cat_datas = eMain::$sql->sql_to_array($rqCat);			
					for ($o=0;$o<$cat_datas['nb'];$o++) {						
						if ($cat_datas['datas'][$o]['cat'] == $values[$temp_lang]['cat']) { echo "<option selected>".$cat_datas['datas'][$o]['cat']."</option>"; }
						else { echo "<option>".$cat_datas['datas'][$o]['cat']."</option>"; }
					}					
?>
					</select>
					<input name='<?php echo $temp_lang; ?>_newcat' type='text' value='' size='35'>
				</td>
			</tr>
			<tr>
				<td>Rôle : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['role']; ?>" name="<?php echo $temp_lang; ?>_role" size="64" /></td>
			</tr>
			<tr>
				<td>Technologie (CMS, Framework, ...) : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['tech']; ?>" name="<?php echo $temp_lang; ?>_tech" size="64" /></td>
			</tr>
			<tr>
				<td>Langages : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['languages']; ?>" name="<?php echo $temp_lang; ?>_languages" size="64" /></td>
			</tr>
			<tr>
				<td>Cible : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['target']; ?>" name="<?php echo $temp_lang; ?>_target" size="64" /></td>
			</tr>
			<tr>
				<td>Images</td>
				<td>
					<input type="hidden" name="<?php echo $temp_lang; ?>_old_image" value="<?php echo $values[$temp_lang]['image']; ?>" />
<?php
					if (!empty($values[$temp_lang]['image'])) {
						echo "<a href='../images/".$values[$temp_lang]['folder'].'/'.eFile::add_suffix($values[$temp_lang]['image'])."' target='blank'><img src='../images/".$values[$temp_lang]['folder'].'/'.$values[$temp_lang]['image']."' width='160' height='120' border='0'></a> <br>";
					}
					echo "<b>Image principale : </b><input type=\"file\" name=\"".$temp_lang."_image\" size='30'><br />
					<br />
					";
					
						
					$rq_img = "SELECT * FROM ".eParams::$prefix."_".$temp_lang."_cms_images WHERE global_ref<>'".$global_ref."' AND folder='".$values[$temp_lang]['folder']."' ORDER BY gallery_pos ASC;";
					$file_sys = eMain::$sql->sql_to_array($rq_img);
					
					echo "					
					<b>Ajouter des images secondaires : </b><input type=\"file\" name=\"".$temp_lang."_images[]\" multiple=\"multiple\" size='30'>
					<input type=\"hidden\" name=\"".$temp_lang."_nb_img\" value=\"".$file_sys['nb']."\">
					<br />
					<br />					
					";
												
					for ($i=0;$i<$file_sys['nb'];$i++) {
						$image = $file_sys['datas'][$i];
						$image['filename'] = $image['name'].'.'.$image['extension'];
?>
					<div style="display:inline-block; text-align:center; width:120px; margin:5px;">
						<a href="../images/<?php echo $values[$temp_lang]['folder'].'/'.eFile::add_suffix($image['filename']); ?>" target="_blank"><img src="../images/<?php echo $values[$temp_lang]['folder'].'/'.$image['filename']; ?>" width="80" height="60" alt="<?php echo $image['description']; ?>" title="<?php echo $image['description']; ?>" /></a>
						<br />
						<input type="hidden" name="<?php echo $temp_lang.'_'.$i; ?>_img_global_ref" value="<?php echo $image['global_ref']; ?>" />	
						<input type="hidden" name="<?php echo $temp_lang.'_'.$i; ?>_img_filename" value="<?php echo $image['filename']; ?>" />	
						<input type="checkbox" name="<?php echo $temp_lang.'_'.$i; ?>_img_delete" value="On"/> Supprimer <br />
						<input type="text" name="<?php echo $temp_lang.'_'.$i; ?>_img_description" value="<?php echo $image['description']; ?>" size="18" />
						
					</div>
<?php
						} // END FOR IMG
												
								
					
?>		
				</td>
			</tr>
			<tr>
				<td>Caché : </td>
				<td>
					<select name="<?php echo $temp_lang; ?>_hidden">
						<option value="1" <?php if ($values[$temp_lang]['hidden']=='1') { ?>selected="selected"<?php } ?>>Oui</option>
						<option value="0" <?php if ($values[$temp_lang]['hidden']=='0') { ?>selected="selected"<?php } ?>>Non</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Visible : </td>
				<td>
					<select name="<?php echo $temp_lang; ?>_visible">
						<option value="1" <?php if ($values[$temp_lang]['visible']=='1') { ?>selected="selected"<?php } ?>>Oui</option>
						<option value="0" <?php if ($values[$temp_lang]['visible']=='0') { ?>selected="selected"<?php } ?>>Non</option>
					</select>
				</td>
			</tr>
						
		</table>
		<br />
		<b>Description de la création : </b><br />
		<?php 			
			$wysiwyg_content = $values[$temp_lang]['description']; 
			$wysiwyg_name = $temp_lang.'_'.$base_wysiwyg_name;
			include('content_form.php'); 
		?>		
		
		<div class="float-right">
			<div class="submit" onclick="$('#post_lang').val('<?php echo $temp_lang; ?>'); document.forms['add'].submit();">VALIDER</div>
		</div>
		<div class="clear"> </div>
		
		</div><!-- END OF LANG CONTAINER -->
<?php
	} // END FOR LANGUAGES
?>	
		<input type="hidden" name="submit_check" value="1" />
		<input type="hidden" name="post_lang" id="post_lang" value="default" />
	</form>
	</div> <!--END OF ADDMOD -->
<?php
	} // END IF creations
?>		