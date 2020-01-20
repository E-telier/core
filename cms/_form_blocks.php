	<?php
		$table = "_cms_blocks";
	
		$base_wysiwyg_name = "wysiwyg_name"; 
			
		$bgcolor_datas = array();
		for ($l=0;$l<$nb_lang;$l++) {
			$temp_lang = eParams::$available_languages[$l];
			$bgcolor_datas[$temp_lang] = eMain::$sql->sql_to_array("SELECT DISTINCT bgcolor FROM ".eParams::$prefix.'_'.$temp_lang."_cms_blocks ORDER BY bgcolor ASC;");
		}
		               
		$pages_datas = array();
		for ($l=0;$l<$nb_lang;$l++) {
			$temp_lang = eParams::$available_languages[$l];
			$pages_datas[$temp_lang] = eMain::$sql->sql_to_array("SELECT id, reference FROM ".eParams::$prefix.'_'.$temp_lang."_cms_pages ORDER BY reference ASC;");
		}
		
		$sections_datas = array();
		for ($l=0;$l<$nb_lang;$l++) {
			$temp_lang = eParams::$available_languages[$l];
			$sections_datas[$temp_lang] = eMain::$sql->sql_to_array("SELECT DISTINCT childof FROM ".eParams::$prefix.'_'.$temp_lang."_cms_pages WHERE childof<>reference ORDER BY childof ASC;");
		}
			
		// Traitement form //
		if (isset($_POST['submit_check'])) {
								
			// FILL EVERY LANGUAGES WITH POSTED IF EMPTY //
			$exceptions = array("pages_ref", "sections_ref", 'bgcolor');
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
		
				$pages_string = '';
				if (isset($_POST['page_'.$temp_lang.'_all'])) {
					$pages_string = '*';
				} else {
					for ($b=0;$b<$pages_datas[$temp_lang]['nb'];$b++) {			
						$reference_url = eText::str_to_url($pages_datas[$temp_lang]['datas'][$b]['reference']);
						if (isset($_POST['page_'.$temp_lang.'_'.$reference_url])) {						
							$pages_string .= '*'.$pages_datas[$temp_lang]['datas'][$b]['reference'];
						}
					}
					if (!empty($pages_string)) { $pages_string .= '*'; }
				}
								
				$sections_string = '';
				if (isset($_POST['section_'.$temp_lang.'_all'])) {
					$sections_string = '*';
				} else {
					for ($b=0;$b<$sections_datas[$temp_lang]['nb'];$b++) {						
						if (isset($_POST['section_'.$temp_lang.'_'.eText::str_to_url($sections_datas[$temp_lang]['datas'][$b]['childof'])])) {						
							$sections_string .= '*'.$sections_datas[$temp_lang]['datas'][$b]['childof'];							
						}
					}
					if (!empty($sections_string)) { $sections_string .= '*'; }
				}
				
				$_POST[$temp_lang.'_reference'] = eText::str_to_url($_POST[$temp_lang.'_reference']);
				
				$bgcolor = $_POST[$temp_lang.'_bgcolor'];
				$bgcolor = trim(str_replace('#', '', $bgcolor));
										
				if ($addmod==0) {
					$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."$table (global_ref, reference, title, bgcolor, textalign, content, content_block, pages_ref, sections_ref, position) VALUES (
					'".eMain::$sql->protect_sql($new_global_ref)."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_reference'])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
					'".eMain::$sql->protect_sql($bgcolor)."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_textalign'])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_type'])."', 
					'".eMain::$sql->protect_sql($pages_string)."', 
					'".eMain::$sql->protect_sql($sections_string)."', 
					'".eMain::$sql->protect_sql($_POST[$temp_lang.'_position'])."');";
				} else {
					$rq = "UPDATE ".eParams::$prefix.'_'.$temp_lang."$table SET 
					reference='".eMain::$sql->protect_sql($_POST[$temp_lang.'_reference'])."', 
					title='".eMain::$sql->protect_sql($_POST[$temp_lang.'_title'])."', 
					bgcolor='".eMain::$sql->protect_sql($bgcolor)."', 
					textalign='".eMain::$sql->protect_sql($_POST[$temp_lang.'_textalign'])."', 
					content='".eMain::$sql->protect_sql($_POST[$temp_lang.'_'.$base_wysiwyg_name])."', 
					content_block='".eMain::$sql->protect_sql($_POST[$temp_lang.'_type'])."', 
					pages_ref='".eMain::$sql->protect_sql($pages_string)."', 
					sections_ref='".eMain::$sql->protect_sql($sections_string)."', 
					position='".eMain::$sql->protect_sql($_POST[$temp_lang.'_position'])."' 
					WHERE global_ref=".eMain::$sql->protect_sql($addmod).";";
				}
				if (!eMain::$sql->sql_query($rq)) {
					echo "<div class=\"error\">ERROR CMS002 : Unable to process the query<br />$rq<br />".mysqli_error($mysqli)."</div>";
				} else {
					echo '<div class="success">Données '.$temp_lang.' correctement enregistrées !</div>';
				}
			} // END FOR LANGUAGES
		}
		
		// Affichage datas //
		if ($addmod==0) {
		
			$content = array();
			$content['reference']="";
			
			$rq = "SELECT id FROM ".eParams::$prefix.'_'.$_SESSION['lang']."$table";
			$result_datas = eMain::$sql->sql_to_array($rq);
			$nb = $result_datas['nb'];;			
			$content['position']=$nb+1;
			
			$content['title']="";
			$content['content']="";
			$content['content_block']=0;
			$content['pages_ref'] = array();
			$content['sections_ref'] = array();
			$content['bgcolor'] = '';
			$content['textalign'] = 'initial';
			
			for ($l=0;$l<$nb_lang;$l++) {
				$values[eParams::$available_languages[$l]] = $content;
			}
			
			$title = "Ajout d'un text-block";
		
		} else {
		
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
								
				$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE global_ref=$addmod;";
						
				$result_datas = eMain::$sql->sql_to_array($rq);
				$content = $result_datas['datas'][0];
				$content['pages_ref'] = explode('*', $content['pages_ref']);
				$content['sections_ref'] = explode('*', $content['sections_ref']);
				
				$content['title'] = eText::html_quotes($content['title']);
				
				$values[$temp_lang] = $content;
				
			}
				
			$title = 'Modification du text-block référence "'.$values[$_SESSION['lang']]['reference'].'"';
				
		}
				
?>
	<h1><?php echo eText::style_to_html($title); ?></h1>
	<br />
	<?php include('_controls_lang.php'); ?>
	<div class="addmod">
	<form method="post" name="add" id="add">
<?php
	for ($l=0;$l<$nb_lang;$l++) {
		$temp_lang = eParams::$available_languages[$l];
?>		
	<div id="form_<?php echo $temp_lang; ?>" class="form_lang">
		<table cellspacing="0">
			<tr>
				<td width="20%">Référence du block : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['reference']; ?>" name="<?php echo $temp_lang; ?>_reference" /></td>
			</tr>
			<tr>
				<td>Position du block : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['position']; ?>" name="<?php echo $temp_lang; ?>_position" /></td>
			</tr>
			<tr>
				<td>Type de block : </td>
				<td>
					<input type="radio" value="0" id="side_block" name="<?php echo $temp_lang; ?>_type" <?php if ($values[$temp_lang]['content_block']=='0') { echo 'checked="checked"'; } ?> /><label for="side_block">Side block</label>
					<input type="radio" value="1" id="content_block" name="<?php echo $temp_lang; ?>_type" <?php if ($values[$temp_lang]['content_block']=='1') { echo 'checked="checked"'; } ?> /><label for="content_block">Content block</label>
				</td>
			</tr>
			<tr>
				<td>
					Référence à la page :<br />					
				</td>
				<td>
<?php
				$selected = '';
				if (count($values[$temp_lang]['pages_ref'])==2 && empty($values[$temp_lang]['pages_ref'][0]) && empty($values[$temp_lang]['pages_ref'][1])) { $selected='checked="checked"'; }
?>
					<div style="width:32%;display:inline-block;"><input type="checkbox" value="on" id="page_<?php echo $temp_lang; ?>_all" name="page_<?php echo $temp_lang; ?>_all" <?php echo $selected; ?> /><label for="page_<?php echo $temp_lang; ?>_all">Toutes</label></div>
<?php
				for ($b=0;$b<$pages_datas[$temp_lang]['nb'];$b++) {
					
					$this_page = $pages_datas[$temp_lang]['datas'][$b];
					
					$selected = '';					
					if (array_search($this_page['reference'], $values[$temp_lang]['pages_ref'])!==false) { $selected='checked="checked"'; }
?>
					<div style="width:32%;display:inline-block;"><input type="checkbox" value="on" id="page_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($this_page['reference']); ?>" name="page_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($this_page['reference']); ?>" <?php echo $selected; ?> /><label for="page_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($this_page['reference']); ?>"><?php echo eText::iso_htmlentities($this_page['reference']); ?></label></div>
<?php
				}
?>
				</td>
			</tr>
			<tr>
				<td>
					Référence à la section :<br />					
				</td>
				<td>
<?php
				$selected = '';
				if (count($values[$temp_lang]['sections_ref'])==2 && empty($values[$temp_lang]['sections_ref'][0]) && empty($values[$temp_lang]['sections_ref'][1])) { $selected='checked="checked"'; }
?>
					<div style="width:32%;display:inline-block;"><input type="checkbox" value="on" id="section_<?php echo $temp_lang; ?>_all" name="section_<?php echo $temp_lang; ?>_all" <?php echo $selected; ?> /><label for="section_<?php echo $temp_lang; ?>_all">Toutes</label></div>
<?php
				for ($b=0;$b<$sections_datas[$temp_lang]['nb'];$b++) {
					
					$section = $sections_datas[$temp_lang]['datas'][$b];
					
					$selected = '';
					if (array_search($section['childof'], $values[$temp_lang]['sections_ref'])!==false) { $selected='checked="checked"'; }
?>
					<div style="width:32%;display:inline-block;"><input type="checkbox" value="on" id="section_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($section['childof']); ?>" name="section_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($section['childof']); ?>" <?php echo $selected; ?> /><label for="section_<?php echo $temp_lang; ?>_<?php echo eText::str_to_url($section['childof']); ?>"><?php echo eText::iso_htmlentities($section['childof']); ?></label></div>
<?php
				}
?>
				</td>
			</tr>
			<tr>
				<td>Alignement du texte : </td>
				<td>
					<input type="radio" value="initial" id="initial" name="<?php echo $temp_lang; ?>_textalign" <?php if ($values[$temp_lang]['textalign']=='initial') { echo 'checked="checked"'; } ?> /><label for="initial">Automatique</label>
					<input type="radio" value="left" id="left" name="<?php echo $temp_lang; ?>_textalign" <?php if ($values[$temp_lang]['textalign']=='left') { echo 'checked="checked"'; } ?> /><label for="left"><img src="design/left-align.gif" width="" height="" alt="gauche"></label>
					<input type="radio" value="right" id="right" name="<?php echo $temp_lang; ?>_textalign" <?php if ($values[$temp_lang]['textalign']=='right') { echo 'checked="checked"'; } ?> /><label for="right"><img src="design/right-align.gif" width="" height="" alt="droite"></label>
					<input type="radio" value="center" id="center" name="<?php echo $temp_lang; ?>_textalign" <?php if ($values[$temp_lang]['textalign']=='center') { echo 'checked="checked"'; } ?> /><label for="center"><img src="design/center-align.gif" width="" height="" alt="centre"></label>
					<input type="radio" value="justify" id="justify" name="<?php echo $temp_lang; ?>_textalign" <?php if ($values[$temp_lang]['textalign']=='justify') { echo 'checked="checked"'; } ?> /><label for="justify"><img src="design/justify-align.gif" width="" height="" alt="justifié"></label>
				</td>
			</tr>
			<tr>
				<td>Titre du block : </td>
				<td><input type="text" value="<?php echo $values[$temp_lang]['title']; ?>" name="<?php echo $temp_lang; ?>_title" size="64" /></td>
			</tr>
			<tr>
				<td>Couleur du block : </td>
				<td>
<?php
					$selected = '';
					if ($values[$temp_lang]['bgcolor']=='') { $selected='checked="checked"'; }					
?>
					<input type="radio" value="" id="auto" name="<?php echo $temp_lang; ?>_bgcolor_radio" <?php echo $selected; ?> onchange="$('#<?php echo $temp_lang; ?>_bgcolor').val(this.value); if ($(this).val()=='other') { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'inline-block'}); } else { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'none'}); }" /><label for="auto">Aucune (automatique)</label>
					<br />
<?php
				for ($b=0;$b<$bgcolor_datas[$temp_lang]['nb'];$b++) {
					$bgcolor = $bgcolor_datas[$temp_lang]['datas'][$b];
					if (!empty($bgcolor['bgcolor'])) {
						$selected = '';
						if ($values[$temp_lang]['bgcolor']==$bgcolor['bgcolor']) { $selected='checked="checked"'; }
?>
					<input type="radio" value="<?php echo $bgcolor['bgcolor']; ?>" id="bgcolor_<?php echo $temp_lang.'_'.$b; ?>" name="<?php echo $temp_lang; ?>_bgcolor_radio" <?php echo $selected; ?> onchange="$('#<?php echo $temp_lang; ?>_bgcolor').val(this.value); if ($(this).val()=='other') { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'inline-block'}); } else { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'none'}); }" /><label for="bgcolor_<?php echo $temp_lang.'_'.$b; ?>" style="vertical-align:middle; background-color:#<?php echo $bgcolor['bgcolor']; ?>; width:38px; height:38px; display:inline-block;"> </label>
<?php
					}
				}
?>
					<br />
					<input type="radio" value="other" id="other" name="<?php echo $temp_lang; ?>_bgcolor_radio" onchange="$('#<?php echo $temp_lang; ?>_bgcolor').val(''); if ($(this).val()=='other') { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'inline-block'}); } else { $('#custom_<?php echo $temp_lang; ?>_bgcolor').css({'display':'none'}); }" /><label for="other">Autre</label>
					<div id="custom_<?php echo $temp_lang; ?>_bgcolor" style="display:none;">
						<label for="<?php echo $temp_lang; ?>_bgcolor">Couleur hexadécimale perso :</label> 
						#<input type="text" id="<?php echo $temp_lang; ?>_bgcolor" name="<?php echo $temp_lang; ?>_bgcolor" value="<?php echo $values[$temp_lang]['bgcolor']; ?>" size="6" />
					</div>
				</td>
			</tr>
		</table>
		
		<div class="align_right">
			<div class="submit" onclick="$('#post_lang').val('<?php echo $temp_lang; ?>'); eForm.submit(this);">ENREGISTRER</div>
		</div>
		
		<b>Contenu du block : </b><br />
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