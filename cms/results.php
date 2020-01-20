<?php

	$multilingual = true;

	switch ($page) {
		case "pages":
			$cols_values = array("id", "reference", "childof", "menu_name", "menu_position", "title", "views", "(reference=childof) AS parent");
			$cols_names = array("id", "reference", "childof", "menu_name", "menu_position", "title", "views");
			$table = eCMS::$table_prefix."_cms_pages";
						
			$sql_order = "ORDER BY childof ASC, parent DESC, menu_position ASC, reference ASC, id ASC;";
			$titlename = "pages";
			break;
		case "blocks":
			$cols_values = array("reference", "title", "content", "pages_ref", "sections_ref", "position");
			$table = eCMS::$table_prefix."_cms_blocks";
			$sql_order = "ORDER BY pages_ref ASC, sections_ref ASC, position ASC, reference ASC, id ASC;";
			$titlename = "text-blocks";
			break;
		case "users":
			$cols_values = array("login", "access_level");
			$table = eParams::$prefix."_users";
			
			if (!isset($_GET['show'])) {
				$sql_conditions = "WHERE domain='*' OR domain LIKE '%*admin*%'";
				$usertype = 'admin';
			} else {	
				if ($_GET['show']!='all') {
					$sql_conditions = "WHERE domain LIKE '%*".$_GET['show']."*%'";						
				}
				$usertype = $_GET['show'];
			}
			
			if (isset($_POST['search_txt'])) {
				
				if (!isset($sql_conditions)) { $sql_conditions = "WHERE true"; }
				
				$sql_conditions .= " AND (false";
				$sql_conditions .= " OR login LIKE '%".$_POST['search_txt']."%'";
				$sql_conditions .= " OR firstname LIKE '%".$_POST['search_txt']."%'";
				$sql_conditions .= " OR lastname LIKE '%".$_POST['search_txt']."%'";
				$sql_conditions .= " OR email LIKE '%".$_POST['search_txt']."%'";
				$sql_conditions .= ")";
			}
			
			$sql_order = "ORDER BY access_level ASC, login ASC, id ASC;";
			$titlename = "utilisateurs";
			
			$multilingual = false;
			
			break;
		case "styles":
			$cols_values = array("name", "text_font", "text_color", "text_size", "background_color", "activated");
			$table = eParams::$prefix."_cms_styles";
			$sql_order = "ORDER BY activated DESC, name ASC, id ASC;";
			$titlename = "styles";
			
			$multilingual = false;
			
			break;
		case "params":
			$cols_values = array("description", "keywords", "banner_content", "footer_content", "activated");			
			$table = eCMS::$table_prefix."_cms_params";
			$sql_order = "ORDER BY activated DESC, id ASC;";
			$titlename = "paramètres";
			break;	
		case "images":
			$cols_values = array("CONCAT(name, '.', extension) AS image", "width", "height", "align");
			$cols_names = array('image', "width", "height", "align");
			
			$modules_datas = eMain::$sql->sql_to_array("SELECT reference, activated FROM ".eParams::$prefix."_cms_modules WHERE reference='slideshow' OR reference='gallery' ORDER BY FIELD(reference, 'slideshow', 'gallery');");						
			for ($i=0;$i<$modules_datas['nb'];$i++) {
				if ($modules_datas['datas'][$i]['activated']=='1') {
					$cols_values[] = $modules_datas['datas'][$i]['reference'].'_pos';
					$cols_names[] = $modules_datas['datas'][$i]['reference'].' position';
				}
			}
			
			$folder_value = 'folder';
			
			$cols_values = array_merge($cols_values, array('name', 'extension', 'description', 'weight', 'folder'));
			
			$table = eCMS::$table_prefix."_cms_images";
			$sql_order = "ORDER BY folder ASC, name ASC, id ASC;";
			$titlename = "images";
			break;
		case "fichiers":	
			$cols_values = array("name");		
			$titlename = "fichiers";
			
			$multilingual = false;
			
			break;			
	}
	
	$action_str = '';
	
	// PLUGINS //
	for ($m=0;$m<eCMS::$modules['nb'];$m++) {
		if (eCMS::$modules['datas'][$m]['backoffice']!='') {			
			include('plugins/'.eCMS::$modules['datas'][$m]['backoffice'].'/results.php');
		}
	}
	
	if ($multilingual) {
		$cols_values[] = 'global_ref';
	}
	$cols_values[] = 'id';
	
	if (!isset($cols_names)) {
		$cols_names = $cols_values;
	}
		
	$sort = 'ASC';
	if (isset($_GET['orderby'])) {		
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		}
		
		$col_index = array_search($_GET['orderby'], $cols_names);
		$col_value = $cols_values[$col_index];
		$as_index = strpos($col_value, ' AS ');
		if ($as_index!==false) {
			$col_value = substr($col_value, $as_index+4);
		}
		
		$sql_order = "ORDER BY ".$col_value.' '.$sort;
		
		if (isset($folder_value)) {
			$sql_order = str_replace("ORDER BY ", "ORDER BY ".$folder_value.' ASC, ', $sql_order);
		}
	}
		
	if (isset($_POST['new_file'])) {
		$file_datas = eFile::upload_file($_FILES['filedatas'], array('folder'=>'../uploaded_files/'));
				
		if (!empty($file_datas['error'])) { 
			//echo $file_datas['error']; 
			if ($file_datas['error']!="no_file") {
				echo "<div class=\"error\">";
				echo styleToHTML("[h2]Une erreur s'est produite[/h2]");
				
				if ($file_datas['error']=='forbidden_type') {
					echo "Erreur".' : ';
					echo "type de fichier interdit";
				}
				if (strpos($file_datas['error'], 'larger_than_')!==false) {
					$max_size = (floatval(substr($file_datas['error'], strlen('larger_than_')))/(1024*1024)).' Mo';
					echo "Erreur".' : ';
					echo str_replace('MAXSIZE', $max_size, "fichier plus large que MAXSIZE");
				}
				if (strpos($file_datas['error'], 'upload_failed_in_')!==false) {
					$folder = '"'.substr($file_datas['error'], strlen('upload_failed_in_')).'"';
					echo "Erreur".' : ';
					echo str_replace('FOLDER', $folder, "téléchargement raté vers le dossier FOLDER");
				}	
				echo "</div>";
			}
		}
	}
	
	if (isset($_POST['position'])) {
		$col_name = $_POST['col_name'];
		$table = $_POST['table'];	
		$content = eMain::$sql->sql_to_array("SELECT COUNT(id) AS id_num, COUNT(DISTINCT $col_name) AS pos_num FROM $table");
		$content = $content['datas'][0];
		
		if ($content['id_num']==$content['pos_num']) {
			// MOVE ALL POSITIONS //
			
			if ($_POST['position']<$_POST['old_position']) {
				$rq_mod_pos = "UPDATE $table SET $col_name = $col_name + 1 WHERE $col_name >= ".$_POST['position']." AND $col_name < ".$_POST['old_position'];	
			} else {
				$rq_mod_pos = "UPDATE $table SET $col_name = $col_name - 1 WHERE $col_name <= ".$_POST['position']." AND $col_name > ".$_POST['old_position'];	
			}
					
			//echo $rq_mod_pos;
			if (!eMain::$sql->sql_query($rq_mod_pos)) { die(eMain::$sql->get_error()); }
			
		} 
		
		$rq_mod_pos = "UPDATE $table SET $col_name = ".$_POST['position']." WHERE id = ".$_POST['id'];	
		if (!eMain::$sql->sql_query($rq_mod_pos)) { die(eMain::$sql->get_error()); }
				
	}

	if (isset($_POST['del'])) {
		if ($page=='fichiers') {
			unlink('../uploaded_files/'.$_POST['del']);
		} else {
		
			if (!$multilingual) {
				$field = 'id'; 
				
				$rq = "DELETE FROM $table WHERE ".$field."='".$_POST['del']."';";
				
				if (!eMain::$sql->sql_query($rq)) {
					echo "
				<div class=\"error\">ERROR CMS003 : Unable to delete this item<br />".eMain::$sql->get_error()."<br />".$rq."</div>
					";
					} 				
			}
			else { 
								
				$field = 'global_ref'; 
				
				for ($l=0;$l<$nb_lang;$l++) {
					$temp_lang = eParams::$available_languages[$l];
					$temp_table = substr($table, strlen(eCMS::$table_prefix));
					$temp_table = eParams::$prefix.'_'.$temp_lang.$temp_table;
					$rq = "DELETE FROM $temp_table WHERE ".$field."='".eMain::$sql->protect_sql($_POST['del'])."';";
					
					
					if ($page=='images') {
						$rq_img = "SELECT name, extension, folder FROM $temp_table WHERE ".$field."=".eMain::$sql->protect_sql($_POST['del'])." LIMIT 1;";						
						$content = eMain::$sql->sql_to_array($rq_img);	
						$content = $content['datas'][0];
						$filename = eText::str_to_url($content['name']).'.'.$content['extension'];
						$filename_full = eText::str_to_url($content['name']).'_full'.'.'.$content['extension'];
						$folder = $content['folder'];
					}
					
					if (!eMain::$sql->sql_query($rq)) {
						echo "
				<div class=\"error\">ERROR CMS003 : Unable to delete this item<br />".eMain::$sql->get_error()."<br />".$rq."</div>
						";
					} else if ($page=='images') {
						
						if (!empty($folder)) {
							$root_folder = '../images/'.$folder.'/';
						} else {
							$root_folder = '../images/';
						}
						
						if (file_exists($root_folder.$filename)) {
							unlink($root_folder.$filename);							
						} else {
							echo $root_folder.$filename." doesn't exist <br />\n";
						}
						if (file_exists($root_folder.$filename_full)) {
							unlink($root_folder.$filename_full);							
						} else {
							echo $root_folder.$filename_full." doesn't exist <br />\n";
						}
						
						if (eFile::is_dir_empty($root_folder)) {
							rmdir($root_folder);							
						} else {
							echo $root_folder." isn't empty <br />\n";
						}
					}
				} // END FOR LANG				
				
			}
						
		}
	}

	$nb_values = count($cols_values);
	$values_string="";
	for ($v=0;$v<$nb_values;$v++) {
		if ($v>0) { $values_string .= ", "; }
		$values_string .= $cols_values[$v];
	}
		
?>
	<h1>Gestion des <?php echo $titlename; ?></h1>	
<?php	

	if ($page=='users') {
?>
	<div id="search_user" class="" style="position:relative;">
		<a href="<?php echo eMain::root_url(); ?>?p=users&show=all" class="button <?php if ($usertype=='all') { ?>selected<?php } ?>">Tous</a>
<?php 
	for ($a=0;$a<count(eUser::$access_types);$a++) {
?>
		<a href="<?php echo eMain::root_url(); ?>?p=users&show=<?php echo eUser::$access_types[$a]; ?>" class="button <?php if ($usertype==eUser::$access_types[$a]) { ?>selected<?php } ?>"><?php echo eLang::show_translate('usertype '.eUser::$access_types[$a]); ?></a>
<?php 
	}
?>
		<div style="position:absolute; top:0px; right:0px;">
			<form name="form_search" method="post" action=""><input id="search_txt" name="search_txt" type="text" placeholder="Référence / nom" value="" /> <input type="submit" value="Recherche" name="search_btn" /></form>
		</div>
	</div>
<?php		
	}
	
	if ($page!='fichiers') {
?>
	<form name="add_top" method="post" action="index.php?p=<?php echo $page; ?>&addmod=0">		
		<div class="form_menu">						
			<div class="article_btn" onclick="javascript:document.forms['add_top'].submit();">				
				<img src="design/picto_ajout.jpg" width="22" height="22" alt="ajouter" />Ajouter une entrée	
			</div>
			<div class="clear"> </div>
		</div>
	</form>
<?php
	} // END IF ADD
	
	$nb_lang = count(eParams::$available_languages);
	if (!$multilingual) { $nb_lang=1; }		
	if ($nb_lang>1) { include('_controls_lang.php'); }

	for ($l=0;$l<$nb_lang;$l++) {
		
		$temp_lang = eParams::$available_languages[$l];
				
		if ($page=='fichiers') {
			$files_datas = eFile::explore_folder("../uploaded_files", 'file');
			
			$nb = count($files_datas);
			
			for ($i=0;$i<$nb;$i++) {
				$files_datas[$i] = array("name"=>$files_datas[$i], "id"=>$files_datas[$i]);
			}
			
			$result_datas['nb'] = $nb;
			$result_datas['datas'] = $files_datas;
			
		} else {
			
			if (!$multilingual) {
				$temp_table = $table;
			} else {
				$temp_table = substr($table, strlen(eCMS::$table_prefix));
				$temp_table = eParams::$prefix.'_'.$temp_lang.$temp_table;
			}
			
			if (!isset($sql_conditions)) {
				$sql_conditions = 'WHERE true';
			}
			
			$rq = "SELECT $values_string FROM $temp_table ".$sql_conditions.' '.$sql_order;
			$result_datas = eMain::$sql->sql_to_array($rq);
			
		}
?>	
	<div class="lang_block" id="block_<?php echo $temp_lang; ?>">
	
<?php	
	include('results_table_header.php');
	
	for($i=0;$i<$result_datas['nb'];$i++) {	
	
		$content = $result_datas['datas'][$i];
		
		if (isset($folder_value) && $content[$folder_value]!=$current_folder) {
			$current_folder = $content[$folder_value];
			echo '</table>';
			include('results_table_header.php');
		}
		
		echo "
		<tr>
			<td>".($i+1)."</td>
			
		";
				
		for($c=0;$c<$nb_cols;$c++) {
						
			$col_name = $cols_values[$c];
			$as = stripos($col_name, ' AS ');
			if ($as!==false) { $col_name = substr($col_name, $as+4); }
			$col_value = $content[$col_name];
			
			// pages_ref //			
			if ($cols_values[$c]=="pages_ref") {
				if ($col_value=='*') {
					$col_value = 'all';
				} else {										
					$col_value = str_replace('*', ', ', substr($col_value, 1, strlen($col_value)-2));
				}
			}
						
			$max_length = 15;
			
			// LONG UNCUT VALUE //
			if ($cols_values[$c]=="reference" || $cols_values[$c]=="childof") {
				$separator = "_";				
			} else if ($cols_values[$c]=="pages_ref" || $cols_values[$c]=="sections_ref") {
				$separator = "*";				
			} 			
						
			if (isset($separator)) {
				$overflow = strlen($col_value)-$max_length;
				if ($overflow>0) {
					$list_path = explode($separator, $col_value);
					$nb_path = count($list_path);
					$middle = intval($nb_path*0.5);
					$new_string = "";
					for ($p=0;$p<$nb_path;$p++) {
						if ($p>0) {
							if ($p==$middle) {$new_string .= " "; }
							$new_string .= $separator;
						}
						$new_string .= $list_path[$p];						
					}
					$col_value = $new_string;
				}
			}
			
			// LONG LINKS //
			$col_value = preg_replace("/&([^- \n\r&]{15,})/im", "\n&$1\n", $col_value);
			
			// NO STYLE //
			$col_value = eText::no_style($col_value, true);			
			$col_value = eText::no_html($col_value, false);
			$col_value = nl2br($col_value);
			
			// LONG TEXT //
			$col_value = trim($col_value);
			if (eText::iso_strlen($col_value)>128) {
				$col_value = eText::iso_substr($col_value, 0, 128).' [...]';
			}
			
			// IMAGE //
			if ($cols_names[$c]=='image') {
				$folder = '';
				if ($content['folder']!='') { $folder = $content['folder'].'/'; }
				$col_value = '<div align="center"><a href="'.eMain::root_url().'../images/'.$folder.$content['name'].'_full.'.$content['extension'].'" target="_blank"><img src="'.eMain::root_url().'../images/'.$folder.$content['image'].'" title="'.$content['description'].'" /></a><br />'.$content['image'].' ('.eText::format_number($content['weight']/1024, false).' KB)</div>';	
			}
			
			// POSITION //
			if (stripos($cols_names[$c], 'position')!==false && stripos($cols_names[$c], ' AS ')===false) {
				$new_col_value = '';
				$new_col_value = '
				<form name="position_'.$i.'" method="post">
					<input type="hidden" name="col_name" value="'.$cols_values[$c].'" />
					<input type="hidden" name="table" value="'.$temp_table.'" />
					<select value="'.$col_value.'" name="position" onchange="this.form.submit();">
						';
				for($z=0;$z<=$result_datas['nb'];$z++) {	
				
					$selected = '';
					if ($col_value==($z)) {
						$selected = 'selected="selected"';
					}
				
					$new_col_value .= '
						<option '.$selected.'>'.($z).'</option>
						';
				}
				$new_col_value .= '		
					</select>
					<input type="hidden" name="old_position" value="'.$col_value.'" />
					<input type="hidden" name="id" value="'.$content['id'].'" />
				</form>';
				
				$col_value = $new_col_value;
			}
						
			// SPECIAl //
			if ($page=="fichiers") {
				$col_value = '<a href="'.eMain::cur_folder_url().'../uploaded_files/'.$col_value.'" target="_blank">'.$col_value.'</a>';				
			}
			if ($page=="faq") {
				if ($cols_values[$c]=='id_category') {
				
					$rq_cat = "SELECT category FROM $table_ref".$temp_lang."_back_faq_categories WHERE id=".$col_value." LIMIT 1";					
					$cat = eMain::$sql->sql_to_array($rq_cat);
					$cat = $cat['datas'][0];
				
					$col_value = $cat['category'];
				}
			}
						
			// PLUGINS EXCEPTIONS //
			for ($m=0;$m<eCMS::$modules['nb'];$m++) {
				if (eCMS::$modules['datas'][$m]['backoffice']!='' && file_exists('plugins/'.eCMS::$modules['datas'][$m]['backoffice'].'/results_exceptions.php')) {
					include('plugins/'.eCMS::$modules['datas'][$m]['backoffice'].'/results_exceptions.php');
				}
			}
											
			echo "
			<td>".($col_value)."</td>
			";
		}

		if (!$multilingual) { $addmod = $content['id']; }
		else { $addmod = $content['global_ref']; }
			
	
		$mod_str = "";
		$user_datas = eUser::getInstance()->get_datas('login');
		if (($page=="users" && $content['login']=="admin" && $user_datas['login']!='admin') || ($page=='fichiers')) {
			
		} else {
					
			$mod_str = "<div class=\"button\"><a href=\"index.php?p=".$page."&addmod=".$addmod."&lang=".$temp_lang."\">Modifier</a></div>";
		}
		
		$del_str = "";
		if ($page=="users" && $content['login']=="admin") {
			// NOT POSSIBLE TO DELETE ADMIN USER
		} else {			
			$del_str = "<form method=\"post\" style=\"white-space: nowrap;\"><input type=\"checkbox\" name=\"del\" value=\"".$addmod."\" />&nbsp;<input type=\"submit\" name=\"delete\" value=\"Supprimer\" /></form>";				
		}
		
?>			
			<td>
				<!-- EDIT -->
				<?php echo $mod_str; ?>
				<hr>
				<?php echo $del_str; ?>
			</td>	
<?php
		if (!empty($action_str)) {
			// PLUGINS EXCEPTIONS //
			$colname = 'action';
			$this_action = $action_str;
			
			// PLUGINS EXCEPTIONS //
			for ($m=0;$m<eCMS::$modules['nb'];$m++) {
				if (eCMS::$modules['datas'][$m]['backoffice']!='') {
					include('plugins/'.eCMS::$modules['datas'][$m]['backoffice'].'/results_exceptions.php');
				}
			}
?>			
			<td>
				<!-- ACTION -->				
				<?php 					
					$this_action = str_replace('{ id }', $content['id'], $this_action); 
					if (isset($content['global_ref'])) { $this_action = str_replace('{ global_ref }', $content['global_ref'], $this_action); }
					$this_action = str_replace('{ lang }', $temp_lang, $this_action); 
					echo $this_action;
				?>
			</td>
<?php
		} // END IF ACTION
		
		echo 
		"
		</tr>
		";
	
	} // END FOR LINE
	
	unset($current_folder);
?>
	</table>
	</div><!-- END OF LANG BLOCK -->
<?php	
	} // END FOR LANGUAGES
	
		if ($page=="fichiers") {
?>
	<form id="add_file" name="add_file" method="post" action="" enctype="multipart/form-data">
		<div class="form_menu">
			<div class="article_btn">
				<input type="file" name="filedatas" value="" />
			</div>
			<div class="article_btn" onclick="document.forms['add_file'].submit();">				
				<img src="design/picto_ajout.jpg" width="22" height="22" alt="ajouter" />Ajouter le fichier
				<input type="hidden" name="new_file" value="true" />
			</div>
			<div class="clear"> </div>
		</div>
	</form>
<?php
		} else {
?>
	<form name="add" method="post" action="index.php?p=<?php echo $page; ?>&addmod=0">		
		<div class="form_menu">			
			<div class="article_btn" onclick="javascript:document.forms['add'].submit();">				
				<img src="design/picto_ajout.jpg" width="22" height="22" alt="ajouter" />Ajouter une entrée	
			</div>
		</div>		
	</form>
<?php
		} // END IF FILES
?>
	<script type="text/javascript">
	
		var windowReady = false;
		var documentReady = false;
		
		window.onload = function() {
			windowReady = true;
			
			if (documentReady) {
				init();
			}
		}
		$(document).ready(function() {		
			documentReady = true;
			
			if (windowReady) {
				init();
			}
		});
	
		function init() {			
			$('.lang_block').each(function(l) {				
				if ($(this).find('table').length>1) {
					
					var tCols = new Array();
					$('.lang_block:eq(0) table:last-child tr:first-child td').each(function(i) {
						tCols[i] = $(this).width();
					});
					
					$(this).find('table').each(function(i) {						
						if (i>0) {
							var tHeight = $(this).height();
							$(this).css({'max-height':tHeight+'px'}).addClass('closed');							
						} 
						$(this).find('tr:first-child td').each(function(i) {
							$(this).css({'width':tCols[i]+'px', 'min-width':'0px', 'max-width':'50%'});
						});
						
					});
					
					$(this).find('h2').click(function() {
						var tNext = $(this).next('table');
						if (tNext.hasClass('closed')) {
							tNext.removeClass('closed');
							$(this).removeClass('closed');
						} else {
							tNext.addClass('closed');
							$(this).addClass('closed');
						}						
					}).addClass('closed');
				}
			});
		}
	</script>