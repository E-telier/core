<?php 
		if (!isset($nb_wysiwyg)) {
			$nb_wysiwyg = 0;
		}
		$nb_wysiwyg++;

		if (!isset($wysiwyg_name)) {
			$wysiwyg_name = "wysiwyg_".$nb_wysiwyg;
		}
		if (!isset($onfocus)) {
			$onfocus = '';
		}
		
		$class = '';
		
		if (!isset($line_max_chars)) {
			$line_max_chars = 0;
		}
		if (!isset($max_lines)) {
			$max_lines = 0;
		} 
		if (!isset($max_chars)) {
			$max_chars = 0;
		}		
		if ($max_chars<1) {
			$max_chars = $max_lines*$line_max_chars;
		}
		
		if ($line_max_chars>0) {
			$class .= ' line_max_chars_'.$line_max_chars;
		}	
		if ($max_lines>0) {
			$class .= ' max_lines_'.$max_lines;
		}	
		if ($max_chars>0) {
			$class .= ' max_chars_'.$max_chars;
		}		
		if (!empty($class)) {
			$class = 'limits '.$class;
		}
		
		$superclass = '';
		if (isset($simple_editor)) { $superclass .= 'simple_editor'; }
												
		$default_content = eText::style_to_html($wysiwyg_content);
?>
					<div class="wysiwyg_container <?php echo $wysiwyg_name; ?> <?php echo $superclass; ?>">
						<script type="text/javascript">	
						<!--
						
							if ($('head').html().indexOf('wysiwyg.css')<0) {
								//$('head').append('<link rel="stylesheet" href="css/wysiwyg.css" type="text/css" />');
							}
						
							if (!$.isArray(pWysiwyg)) {
								var pWysiwyg = new Array();
							}							
							$(document).ready(function() {
														
								pWysiwyg['<?php echo $wysiwyg_name; ?>'] = new Wysiwyg('<?php echo $wysiwyg_name; ?>', '<?php echo $temp_lang; ?>');
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].init();	
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].pOnFocus='<?php echo $onfocus; ?>';
																
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].maxCharsPerLine=<?php echo $line_max_chars; ?>;						
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].maxLines=<?php echo $max_lines; ?>;
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].maxChars=<?php echo $max_chars; ?>;
								
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].temp_lang='<?php echo $temp_lang; ?>';

								$('#wysiwyg_<?php echo $wysiwyg_name; ?>').focus(function() {
									gEditorControl=pWysiwyg['<?php echo $wysiwyg_name; ?>'].pEditorControl;							
								});
								
<?php
								if (isset($simple_editor)) { 
?>
								pWysiwyg['<?php echo $wysiwyg_name; ?>'].setEditorControls('wysiwyg');
<?php
								}
?>
								
							});	
							
						-->
						</script>	
						
						<div id="controls_editor"><a id="code_btn_<?php echo $wysiwyg_name; ?>" class="selected_control_editor" href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].setEditorControls('code');">Code</a><a id="wysiwyg_btn_<?php echo $wysiwyg_name; ?>" href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].setEditorControls('wysiwyg');">Wysiwyg</a></div>
						<div class="group">
							
							<h2 style="color:#cccccc;">Prévisualisation <a href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].showPreview(true);">Afficher</a> / <a href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].showPreview(false);">Masquer</a></h2>
<?php													
							$style = '';
							if ($_GET['p']=='blocks') {	
								$class.=' block';
								if (!empty($values[$temp_lang]['bgcolor'])) { 
									$style.='background-color:#'.$values[$temp_lang]['bgcolor'].';';
									$rgb = hex2rgb($values[$temp_lang]['bgcolor']);
									if ($rgb[0]+$rgb[1]+$rgb[2]>=600) { $class.=' darktext'; } else { $class.=' lighttext'; }
								}
								if ($values[$temp_lang]['textalign']!='initial') { 
									$class.=' align_'.$values[$temp_lang]['textalign'];
								}
								if (!empty($style)) { $style = 'style="'.$style.'"'; }
							} else if ($_GET['p']=='pages') { $class.=' content'; }
?>
							<div class="simple_styles">
								<a class="simple_style" href="bold" onclick="addStyle('bold', '<?php echo $wysiwyg_name; ?>'); return false;"><img src="design/wysiwyg/bold-off.gif" onmouseover="this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-on.gif';" onmouseout="if (!$(this).hasClass('currentNode')) { this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-off.gif'; }" width="27" height="27" alt="Bold" /></a> 
								<a class="simple_style" href="italic" onclick="addStyle('italic', '<?php echo $wysiwyg_name; ?>'); return false;"><img src="design/wysiwyg/italic-off.gif" onmouseover="this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-on.gif';" onmouseout="if (!$(this).hasClass('currentNode')) { this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-off.gif'; }" width="27" height="27" alt="italic" /></a> 
								<a class="simple_style" href="underlined" onclick="addStyle('underlined', '<?php echo $wysiwyg_name; ?>'); return false;"><img src="design/wysiwyg/underline-off.gif" onmouseover="this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-on.gif';" onmouseout="if (!$(this).hasClass('currentNode')) { this.src=this.src.substring(0, this.src.lastIndexOf('-'))+'-off.gif'; }" width="27" height="27" alt="underlined" /></a> 
							</div>
							
							<div class="wysiwyg <?php echo $class; ?>" <?php echo $style; ?> id="wysiwyg_<?php echo $wysiwyg_name; ?>" style="" onfocus="if (pWysiwyg['<?php echo $wysiwyg_name; ?>'].pOnFocus=='empty') { $(this).html(''); pWysiwyg['<?php echo $wysiwyg_name; ?>'].pOnFocus=''; }"><?php echo eText::no_script(eText::style_to_html($wysiwyg_content, $temp_lang)); ?></div><!-- END OF WYSIWYG -->
							
							<h2 style="color:#cccccc;">Code <a href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].showCode(true);">Afficher</a> / <a href="javascript:pWysiwyg['<?php echo $wysiwyg_name; ?>'].showCode(false);">Masquer</a></h2>
							<textarea name="<?php echo $wysiwyg_name; ?>" class="<?php echo $class; ?>" id="<?php echo $wysiwyg_name; ?>" onfocus="gEditorControl=pWysiwyg['<?php echo $wysiwyg_name; ?>'].pEditorControl;"><?php echo $wysiwyg_content; ?></textarea>		
												
							<div class="align_right">
								<div class="submit" onclick="$('#post_lang').val('<?php echo $temp_lang; ?>'); eForm.submit(this);">ENREGISTRER</div>
							</div>
														
							<table class="style-table" cellspacing="0">
								<tr>
									<td width="25%">
										<b>Structure :</b>
									</td>
									<td>
										<input type="button" value="Titre" onclick="addStyle('title1', '<?php echo $wysiwyg_name; ?>');" style="font-size: 16px;font-weight:bold;">
										<input type="button" value="Sous-titre" onclick="addStyle('title2', '<?php echo $wysiwyg_name; ?>');" style="font-size: 14px;">
										<input type="button" value="Paragraphe" onclick="addStyle('paragraph', '<?php echo $wysiwyg_name; ?>');">
										<input type="button" value="Liste pucée" onclick="if (this.value!=0) { addStyle('dotlist', '<?php echo $wysiwyg_name; ?>'); }">		
										<input type="button" value="Liste numérique" onclick="if (this.value!=0) { addStyle('numlist', '<?php echo $wysiwyg_name; ?>'); }">	
									</td>
								</tr>
								<tr>
									<td width="25%">
										<b>Forme :</b>
									</td>
									<td>
										<input type="button" value="Gras" onclick="addStyle('bold', '<?php echo $wysiwyg_name; ?>');" style="font-weight: bold;">
										<input type="button" value="Italique" onclick="addStyle('italic', '<?php echo $wysiwyg_name; ?>');" style="font-style: italic;">
										<input type="button" value="Souligné" onclick="addStyle('underlined', '<?php echo $wysiwyg_name; ?>');" style="font-style: underline;">
										<input type="button" value="Petit" onclick="if (this.value!=0) { addStyle('small', '<?php echo $wysiwyg_name; ?>'); }" style="font-size: x-small;">
									</td>
								</tr>
								<tr>
									<td width="25%">
										<b>Couleur :</b>
									</td>
									<td>
										<input type="button" value="Bleu" onClick="addStyle('blue', '<?php echo $wysiwyg_name; ?>');" class="blue">
										<input type="button" value="Rouge" onClick="addStyle('red', '<?php echo $wysiwyg_name; ?>');" class="red">
										<input type="button" value="Vert" onClick="addStyle('green', '<?php echo $wysiwyg_name; ?>');" class="green">
										<input type="button" value="Gris" onClick="addStyle('gray', '<?php echo $wysiwyg_name; ?>');" class="gray">
									</td>
								</tr>	
								<tr>
									<td width="25%">
										<b>Alignement :</b>
									</td>
									<td>
										<input type="button" value="Gauche" onClick="addStyle('left', '<?php echo $wysiwyg_name; ?>');" style="text-align:left;">
										<input type="button" value="Centre" onClick="addStyle('center', '<?php echo $wysiwyg_name; ?>');" style="text-align:center;">
										<input type="button" value="Droit" onClick="addStyle('right', '<?php echo $wysiwyg_name; ?>');" style="text-align:right;">
										<input type="button" value="Justifié" onClick="addStyle('justify', '<?php echo $wysiwyg_name; ?>');" style="text-align:justify;">
									</td>
								</tr>
								<?php
								if (!isset($simple_form)) { $simple_form=false; }
								if ($simple_form!=true) {
								?>
								<tr>
									<td width="25%">
										<b>Zone spéciale :</b>
									</td>
									<td>
										<input type="button" value="Zone pour code HTML" onclick="addStyle('HTML', '<?php echo $wysiwyg_name; ?>');">
										<input type="button" value="Zone sans retour de ligne" onclick="addStyle('NO-RETURN', '<?php echo $wysiwyg_name; ?>');">
									</td>
								</tr>
								<tr>
									<td width="25%">
										<b>Ajouter un lien :</b>
									</td>
									<td>
										Lien interne
										<select onchange="javascript:if (this.value!=0) { addStyle('link=\''+(this.value)+'\'', '<?php echo $wysiwyg_name; ?>'); this.value = 0; }">
											<option value="0">Liste des pages</option>
											<?php
												$rq = "SELECT reference, id FROM ".eParams::$prefix.'_'.$temp_lang."_cms_pages ORDER BY reference ASC;";
												$result_datas = eMain::$sql->sql_to_array($rq);
												$nb_pages = $result_datas['nb'];;
												for($i=0;$i<$nb_pages;$i++) {
													$content_page = $result_datas['datas'][$i];
													echo "
											<option value=\"".$content_page['reference']."\" >".$content_page['reference']."</option>
													";
												}
											?>
										</select>
										<br />
										Lien externe <input type="text" id="<?php echo $wysiwyg_name; ?>_lien" name="lien" value="" /> <input type="button" value="Ajouter ce lien" onclick="addURL('<?php echo $wysiwyg_name; ?>');" style="font-style: underline;">
										<br />
										Lien vers un fichier 
										<select id="<?php echo $wysiwyg_name; ?>_file" name="file" value="" onchange="addStyle('url'+'=\'download/'+this.value+'\'', '<?php echo $wysiwyg_name; ?>');">
											<option value="">Liste des fichiers</option>
											<?php
											
											$list_files = eFile::explore_folder('../uploaded_files', 'file');
											
											for ($i=0;$i<count($list_files);$i++) {
											?>
											<option><?php echo $list_files[$i]; ?></option>
											<?php											
											} // END FOR FILES											
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<b>Ajouter une image :</b><br />
									</td>
									<td>
										<select id="<?php echo $wysiwyg_name; ?>_addimage" class="custom_select img_select" onchange="javascript:if (this.value!=0) { addStyle('img='+(this.value)+' /img', '<?php echo $wysiwyg_name; ?>', NO_CLOSING); this.value = 0; }">
											<option value="0">Liste des images</option>
											<?php
												$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."_cms_images ORDER BY folder ASC, name ASC;";
												$result_datas = eMain::$sql->sql_to_array($rq);
												$nb_images = $result_datas['nb'];
												$img_list = array();
												for($i=0;$i<$nb_images;$i++) {									
													$content_img = $result_datas['datas'][$i];
													if ($content_img['folder']!='') { $content_img['folder'].= '/'; }
													$img_list[] = $content_img;
													echo "
											<option value=\"".$content_img['name']."\" class=\"img_option\" style=\"background-image:url('../images/".$content_img['folder'].$content_img['name'].".".$content_img['extension']."');\">".$content_img['name']."</option>
													";
												}
											?>
										</select>										
										<script type="text/javascript">
											<!--
											$(document).ready(function() {
												tImgList = new Array();
												<?php
													for($i=0;$i<$nb_images;$i++) {
														$ref = $img_list[$i]['name'];
														$filename = $ref.".".$img_list[$i]['extension'];
														$width = $img_list[$i]['width'];
														$height = $img_list[$i]['height'];
														$description = $img_list[$i]['description'];
														$align = $img_list[$i]['align'];
												?>
												tImgList['<?php echo $ref; ?>'] = new Array();
												tImgList['<?php echo $ref; ?>']['filename'] = '<?php echo $filename; ?>';
												tImgList['<?php echo $ref; ?>']['width'] = '<?php echo $width; ?>';
												tImgList['<?php echo $ref; ?>']['height'] = '<?php echo $height; ?>';
												tImgList['<?php echo $ref; ?>']['description'] = '<?php echo addslashes($description); ?>';
												tImgList['<?php echo $ref; ?>']['align'] = '<?php echo $align; ?>';
												<?php
													} // END OF FOR img_list
												?>
																								
												pWysiwyg['<?php echo $wysiwyg_name; ?>'].pImgList = tImgList;
																								
											});
											-->
										</script>										
										<input type="button" onclick="ePopup.createPopup('addimg', {'url':'index.php?p=images&addmod=0', 'class':'large addimg'});" value="Ajouter une nouvelle image" />
										
									</td>
								</tr>
								<?php
								if ($page=="pages" || $page=='params' || $page=='blocks') {
									
									if ($page=='params') {
										$values[$temp_lang]['reference'] = '*';
									}
									
									$rq = "SELECT * FROM ".eParams::$prefix.'_'.$temp_lang."_cms_blocks WHERE content_block=1 ORDER BY position;";
									if ($page!='blocks') { $rq = str_replace('WHERE', "WHERE (pages_ref='*' OR pages_ref LIKE '%*".$values[$temp_lang]['reference']."*%' OR sections_ref='*' OR sections_ref LIKE '%*".$values[$temp_lang]['reference']."*%') AND", $rq); }
									
									$blocks_datas = eMain::$sql->sql_to_array($rq);
								
									if ($blocks_datas['nb']>0) {
									?>
									<tr>
										<td width="25%">
											<b>Ajouter un block :</b><br />
										</td>				
										<td>
											<select id="addblock" onchange="javascript:if (this.value!=0) { addStyle('block='+(this.value)+' /block', '<?php echo $wysiwyg_name; ?>', NO_CLOSING); this.value = 0; }">
												<option value="0">Liste des blocks</option>
												<?php												
													for ($m=0;$m<$blocks_datas['nb'];$m++) {
												?>
												<option value="<?php echo $blocks_datas['datas'][$m]['reference']; ?>"><?php echo $blocks_datas['datas'][$m]['reference']; ?></option>
												<?php
													} // END FOR MODULES
												?>
											</select>
										</td>
									</tr>
									<?php } // END IF CONTENT BLOCKS
								
								}
								
								// MODULES //
								$allowed_modules = array();	
								for ($m=0;$m<eCMS::$modules['nb'];$m++) {
									if (strpos(eCMS::$modules['datas'][$m]['places'], $page)!==false) {
										$allowed_modules[eCMS::$modules['datas'][$m]['reference']] = eCMS::$modules['datas'][$m];
									}
								}									
								$keys = array_keys($allowed_modules);
								if (count($keys)>0) {
								?>
								<tr>
									<td width="25%">
										<b>Ajouter un module :</b><br />
									</td>				
									<td>
										<select id="addmodule" onchange="javascript:if (this.value!=0) { addStyle('module='+(this.value)+' /module', '<?php echo $wysiwyg_name; ?>', NO_CLOSING); this.value = 0; }">
											<option value="0">Liste des modules</option>
											<?php												
												for ($m=0;$m<count($allowed_modules);$m++) {
											?>
											<option value="<?php echo $keys[$m]; ?>"><?php echo $allowed_modules[$keys[$m]]['name']; ?></option>
											<?php
												} // END FOR MODULES
											?>
										</select>
									</td>
								</tr>
								<?php } // END IF MODULES ?>
								<?php } //END OF IF NOT SIMPLE ?>
							</table>
														
							<div class="clear"> </div>			
							<!--&nbsp; &gt;&gt; <a href="addmod.php?p=images" target="_blank">Ajouter une image Ã  la liste</a>-->						
						</div>
					</div><!-- END OF WYSIWYG CONTAINER -->