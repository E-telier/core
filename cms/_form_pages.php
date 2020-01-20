	<?php
		$table = "_cms_pages";
	
		$base_wysiwyg_name = "wysiwyg_name"; 
		
		$access_types = eUser::$access_types;
		array_unshift($access_types, 'visitor');
	
		// Traitement form //
		if (isset($_POST['submit_check'])) {
							
			// FILL EVERY LANGUAGES WITH POSTED IF EMPTY //
			$exceptions = array("childof", "menu_name");			
			$post_keys = array_keys($_POST);			
			for ($i=0;$i<count($_POST);$i++) {
				$key = $post_keys[$i];
				if (empty($_POST[$key])) {					
					$type_key = substr($key, 3);
					if (array_search($type_key, $exceptions)===false) {
						if (isset($_POST[$_POST['post_lang']."_".$type_key])) {
							$_POST[$key] = $_POST[$_POST['post_lang']."_".$type_key];
						}
					}
				}
			}
			
			$new_global_ref = date('YmdHis');
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
						
				$reference = eText::str_to_url($_POST[$temp_lang.'_reference']);
				if (empty($_POST[$temp_lang.'_menu_name'])) { $menu_name = $reference; }
				else { $menu_name = $_POST[$temp_lang.'_menu_name']; }
				
				if (empty($_POST[$temp_lang.'_childof'])) { $childof = $reference; }
				else { $childof = $_POST[$temp_lang.'_childof']; }
				
				if (!isset($_POST[$temp_lang.'_combined'])) {
					$_POST[$temp_lang.'_combined'] = 0;
				}
								
				$access = '';
				if (isset($_POST[$temp_lang.'_access_all'])) {
					$access = '*';
				} else {
					for ($a=0;$a<count($access_types);$a++) {
						if (isset($_POST[$temp_lang.'_access_'.$access_types[$a]])) {
							$access .= '*'.$access_types[$a];
						}
					}
					if (!empty($access)) {
						$access .= '*';
					}
				}
								
				if ($addmod=='0') {
					$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."$table (global_ref, reference, childof, combined, access, menu_name, menu_position, title, description, keywords, content) VALUES (
					'".eMain::$sql->protect_sql($new_global_ref)."', 
					'".eMain::$sql->protect_sql($reference)."', 
					'".eMain::$sql->protect_sql($childof)."', 
					".eMain::$sql->protect_sql($_POST[$temp_lang.'_combined']).", 
					'".eMain::$sql->protect_sql($access)."', 
					'".eMain::$sql->protect_sql($menu_name)."', 
					".eMain::$sql->protect_sql($_POST[$temp_lang.'_menu_position']).", 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_keywords'])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."');";
				} else {
					$rq = "UPDATE ".eParams::$prefix.'_'.$temp_lang."$table SET 
					reference='".eMain::$sql->protect_sql($reference)."', 
					childof='".eMain::$sql->protect_sql($childof)."', 
					combined=".eMain::$sql->protect_sql($_POST[$temp_lang.'_combined']).", 
					access='".eMain::$sql->protect_sql($access)."', 
					menu_name='".eMain::$sql->protect_sql($menu_name)."', 
					menu_position=".eMain::$sql->protect_sql($_POST[$temp_lang.'_menu_position']).", 
					title='".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
					description='".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', 
					keywords='".eMain::$sql->protect_sql($_POST[$temp_lang.'_keywords'])."', 
					content='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."' 
					WHERE global_ref='".eMain::$sql->protect_sql($addmod)."';";
				}
				if (!eMain::$sql->sql_query($rq)) {
					echo "<div class=\"error\">ERROR CMS002 : Unable to process the query<br />$rq<br />".mysqli_error($mysqli)."</div>";
				} else {
					echo '<div class="success">Données '.$temp_lang.' correctement enregistrées !</div>';
				}
			} // END FOR LANGUAGES
		}
		
		// Affichage datas //
		$values = array();		
		if ($addmod=='0') {
		
			$content = array();
			$content['id']=0;
			$content['reference']="";
			$content['menu_name']="";
			$content['childof'] = "";
			$content['combined'] = 0;
			$content['access'] = '*';
			$content['title']="";
			$content['description']="";
			$content['keywords']="";
			$content['content']="";
			$content['menu_position']=0;
						
			for ($l=0;$l<$nb_lang;$l++) {
				$values[eParams::$available_languages[$l]] = $content;
			}
			
			$title = "Ajout d'une page";
		
		} else {
						
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
								
				$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE global_ref='$addmod';";
				
				$result_datas = eMain::$sql->sql_to_array($rq);
				$content = $result_datas['datas'][0];
																
				if (!get_magic_quotes_gpc()) {
					$content['title'] = eText::html_quotes($content['title']);
					$content['description'] = eText::html_quotes($content['description']);
					$content['keywords'] = eText::html_quotes($content['keywords']);
				}
				$values[$temp_lang] = $content;				
			}
						
			$title = 'Modification de la page référence "'.$values[$_SESSION['lang']]['reference'].'"';
						
		}
					
?>
	<h1><?php echo eText::style_to_html($title); ?></h1>
	<br />
<?php include('_controls_lang.php'); ?>
	<div class="addmod">
	<form method="post" name="add" id="add" enctype="multipart/form-data">
<?php
	for ($l=0;$l<$nb_lang;$l++) {
		$temp_lang = eParams::$available_languages[$l];
?>		
	<div id="form_<?php echo $temp_lang; ?>" class="form_lang">
		<table cellspacing="0">
			<tr>
				<td width="30%">Référence de la page : </td>
				<td>
					<input type="text" value="<?php echo $values[$temp_lang]['reference']; ?>" name="<?php echo $temp_lang; ?>_reference" />
				</td>
			</tr>
			<tr>
				<td>Section : </td>
				<td>
					<select name="<?php echo $temp_lang; ?>_childof">
						<option value="0">Liste des pages</option>
						<?php
							$rq = "SELECT reference, id FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE menu_position>0 ORDER BY childof ASC, menu_position ASC;";							
							$result_datas = eMain::$sql->sql_to_array($rq);
							$nb_pages = $result_datas['nb'];;
							for($i=0;$i<$nb_pages;$i++) {
								$contenu = $result_datas['datas'][$i];					
								if ($contenu['reference']==$values[$temp_lang]['childof']) { $selected="selected"; } else { $selected=""; }
								echo '
						<option value="'.$contenu['reference'].'" '.$selected.' >'.$contenu['reference'].'</option>
								';
							}
						?>
					</select>
					(Aucune = défaut = sa propre section)
				</td>
			</tr>
				<tr>
				<td>Combiner avec la page parent : </td>
				<td><input type="checkbox" value="1" <?php if($values[$temp_lang]['combined']==1) { ?>checked="checked"<?php } ?> name="<?php echo $temp_lang; ?>_combined" /></td>
			</tr>
<?php
	if (count($access_types)>0) {
?>
			</tr>
				<tr>
				<td>Accessibilité : </td>
				<td>
					<input type="checkbox" value="all" <?php if($values[$temp_lang]['access']=='*') { ?>checked="checked"<?php } ?> name="<?php echo $temp_lang; ?>_access_all" id="<?php echo $temp_lang; ?>_access_all" onchange="if ($(this).prop('checked')==true) { $(this).nextAll('input').prop('checked', false); }" />
					<label for="<?php echo $temp_lang; ?>_access_all">Tous</label>
<?php
		for ($a=0;$a<count($access_types);$a++) {
			$access = $access_types[$a];
?>					
					<input type="checkbox" value="all" <?php if(strpos($values[$temp_lang]['access'], '*'.$access.'*')!==false) { ?>checked="checked"<?php } ?> name="<?php echo $temp_lang; ?>_access_<?php echo $access; ?>" id="<?php echo $temp_lang; ?>_access_<?php echo $access; ?>" onchange="if ($(this).prop('checked')==true) { $('#<?php echo $temp_lang; ?>_access_all').prop('checked', false); }" />
					<label for="<?php echo $temp_lang; ?>_access_<?php echo $access; ?>"><?php echo $access; ?></label>
<?php
		} // END FOR ACCESS
?>					
				</td>
			</tr>
<?php
	} // END IF ACCESS
?>
			<tr>
				<td>Nom de la page dans le menu : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['menu_name']; ?>" name="<?php echo $temp_lang; ?>_menu_name" /> (Rien = défaut = réf. de la page)</td>
			</tr>
			<tr>
				<td>Position dans le menu : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['menu_position']; ?>" name="<?php echo $temp_lang; ?>_menu_position" size="8" /> (0 = défaut = absent du menu)</td>		
			</tr>
			<tr>
				<td>Titre de la page : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['title']; ?>" name="<?php echo $temp_lang; ?>_title" size="64" /></td>
			</tr>
			<tr>
				<td>Description de la page : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['description']; ?>" name="<?php echo $temp_lang; ?>_description" size="64" /></td>
			</tr>
			<tr>
				<td>Mots clés de la page : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['keywords']; ?>" name="<?php echo $temp_lang; ?>_keywords" size="64" /></td>
			</tr>
		</table>
		
		<div class="align_right">
			<div class="submit" onclick="$('#post_lang').val('<?php echo $temp_lang; ?>'); eForm.submit(this);">ENREGISTRER</div>
		</div>
		
		<b>Contenu de la page : </b><br />
		<?php 			
			$wysiwyg_content = $values[$temp_lang]['content']; 
			$wysiwyg_name = $temp_lang.'_'.$base_wysiwyg_name;
			include('content_form.php'); 
		?>		
		
		<div class="float-right">
			<div class="submit" onclick="$('#post_lang').val('<?php echo $temp_lang; ?>'); eForm.submit(this);">ENREGISTRER</div>
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