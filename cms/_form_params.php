<?php
		$table = "_cms_params";
	
		
		// Traitement form //
		if (isset($_POST['submit_check'])) {
		

					
			// FILL EVERY LANGUAGES WITH POSTED IF EMPTY //
			$exceptions = array("pages_ref", "sections_ref");
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
									
				if ($addmod==0) {
					$rq = "INSERT INTO ".eParams::$prefix.'_'.$temp_lang."$table (global_ref, description, keywords, banner_content, footer_content, activated) VALUES ('".eMain::$sql->protect_sql($new_global_ref)."', '".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', '".eMain::$sql->protect_sql($_POST[$temp_lang.'_keywords'])."', '".eMain::$sql->protect_sql($_POST[$temp_lang.'_banner_content'])."', '".eMain::$sql->protect_sql($_POST[$temp_lang.'_footer_content'])."', ".$_POST[$temp_lang.'_activated'].");";
				} else {
					$rq = "UPDATE ".eParams::$prefix.'_'.$temp_lang."$table SET description='".eMain::$sql->protect_sql($_POST[$temp_lang.'_description'])."', keywords='".eMain::$sql->protect_sql($_POST[$temp_lang.'_keywords'])."', banner_content='".eMain::$sql->protect_sql($_POST[$temp_lang.'_banner_content'])."', footer_content='".eMain::$sql->protect_sql($_POST[$temp_lang.'_footer_content'])."', activated=".$_POST[$temp_lang.'_activated']." WHERE global_ref=".$addmod.";";
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
			$content['description']="";
			$content['keywords']="";
			$content['banner_content']="";
			$content['footer_content']="";
			$content['activated']=1;
			
			for ($l=0;$l<$nb_lang;$l++) {
				$values[eParams::$available_languages[$l]] = $content;
			}
			
			$title = "Ajout d'un nouveau groupe de paramètres généraux";
		
		} else {
		
			for ($l=0;$l<$nb_lang;$l++) {
				$temp_lang = eParams::$available_languages[$l];
		
				$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."$table WHERE global_ref=$addmod;";
				$result_datas = eMain::$sql->sql_to_array($rq);

				$content = $result_datas['datas'][0];
				
				if (!get_magic_quotes_gpc()) {					
					$content['description'] = eText::html_quotes($content['description']);
					$content['keywords'] = eText::html_quotes($content['keywords']);
				}
				
				$values[$temp_lang] = $content;
				
			}
			
			$title = "Modification d'un groupe de paramètres généraux";
					
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
	
		<b>Description générale: </b><br />
		<input type="text" value="<?php echo $values[$temp_lang]['description']; ?>" name="<?php echo $temp_lang; ?>_description" size="128" />
		<br /><br />		
		<b>Mots-clés généraux : (séparés par des virgules) </b><br />
		<input type="text" value="<?php echo $values[$temp_lang]['keywords']; ?>" name="<?php echo $temp_lang; ?>_keywords" size="128" />
		<br /><br />		
		<b>Activation : </b><br />
		<select name="<?php echo $temp_lang; ?>_activated">
			<option value="0" <?php if ($values[$temp_lang]['activated']==0) { echo "selected"; } ?> >0 : Non</option>
			<option value="1" <?php if ($values[$temp_lang]['activated']==1) { echo "selected"; } ?> >1 : Oui</option>
		</select>
		<br /><br />
		
		<b>Contenu de la bannière : </b><br />
		<?php 
			$wysiwyg_name = $temp_lang."_banner_content"; 
			$wysiwyg_content = $values[$temp_lang]['banner_content']; 
			include('content_form.php'); 
		?>
		
		<br />
		
		<b>Contenu du bas de page : </b><br />
		<?php 
			$wysiwyg_name = $temp_lang."_footer_content"; 
			$wysiwyg_content = $values[$temp_lang]['footer_content']; 
			include('content_form.php'); 
		?>
		
		<br />		
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