<?php
	global $crea_datas;	
	
	if (count(eCMS::$url_datas)>1 && eCMS::$datas_url[0]=='creations') {
		
		$folder = eText::str_to_url($crea_datas['title']);
		$folder_path = "images/creations/".$folder.'/';
				
		$rq_img = "SELECT * FROM ".eCMS::$table_prefix."_cms_images WHERE folder='creations/".$folder."' AND gallery_pos>1 ORDER BY gallery_pos ASC;";
		$images_datas = eMain::$sql->sql_to_array($rq_img);
		$nb_img = $images_datas['nb'];
		$images = $images_datas['datas'];
?>

							<div class="block gallery_img">								
								<div class="creation">
									<h1><?php echo $crea_datas['title']; ?></h1>
								
									<div class="tech">
										<b><?php echo $crea_datas['cat']."</b> &nbsp; Ann&eacute;e ".$crea_datas['annee']; ?>
										<br />
										<?php 
										if (!empty($crea_datas['role'])) { echo '<b>'.eText::iso_htmlentities($crea_datas['role']).'</b> '; }
										if (!empty($crea_datas['languages'])) { echo eText::iso_htmlentities($crea_datas['languages']); }
										if (!empty($crea_datas['tech'])) { echo '<br /><b>'.eText::iso_htmlentities($crea_datas['tech']).'</b>'; }				
										?>
										
									</div>
									<br /><br />
									<div id="images" class="center">
										<div class="img">
											<a href="<?php echo eMain::root_url().$folder_path.eFile::add_suffix($crea_datas['image']); ?>" title="<?php echo $crea_datas['title']; ?> - Image" onclick="window.open(this.href); return false;">
												<img src="<?php echo eMain::root_url().$folder_path.$crea_datas['image']; ?>?d=201911271730" width="160" height="120" alt="<?php echo $crea_datas['title']; ?> - Image" title="<?php echo $crea_datas['title']; ?> - Image" />
											</a>
										</div><!--
<?php
												
				$pos_line=0;
				for ($i=0;$i<$nb_img;$i++) {
													
					$this_img = $images[$i];
					$filePath = eMain::root_url().$folder_path.$this_img['name'].'.'.$this_img['extension'];
									
?>
										--><div class="img">
											<a href="<?php echo eFile::add_suffix($filePath); ?>?d=201911271738" title="<?php echo eText::style_to_html($this_img['description']); ?>" onclick="window.open(this.href); return false;">
												<img src="<?php echo $filePath; ?>?d=201911271738" width="160" height="120" alt="<?php echo eText::style_to_html($this_img['description']); ?>" title="<?php echo eText::style_to_html($this_img['description']); ?>" />
											</a>
										</div><!--
<?php										
				}				
?>
										-->
									</div>
									<br />
									<?php echo eText::indentHTML(eText::style_to_html($crea_datas['description']), 9); ?>
									<br />
					
<?php
				if (!empty($crea_datas['target'])) {
?>
									<div class="more_info right">
										<a href="<?php echo $crea_datas['target']; ?>" onclick="window.open(this.href); return false;">
											Plus d'infos
										</a>
									</div>
<?php
				}
								
				// Compte //			
				$views = $crea_datas['views'] + 1;
				$rqInsert = "UPDATE ".eCMS::$table_prefix."_back_creations SET views = $views WHERE id=".$crea_datas['id'];
				eMain::$sql->sql_query($rqInsert);
			
?>
								</div><!-- END OF CREATION -->
							</div><!-- END OF BLOCK -->
							<script type="text/javascript">var noAnim = true;</script>
<?php 
		
	} // END IF CREATION DETAILS
	
	$hidden_condition = "AND hidden=0";
	if (isset($_GET['showall'])) {
		$hidden_condition = "";
	}

	$rq = "SELECT * FROM ".eCMS::$table_prefix."_back_creations WHERE visible=1 ".$hidden_condition." ORDER BY annee DESC, id DESC";
	$creas_datas = eMain::$sql->sql_to_array($rq);

?>			
							<script type="text/javascript" src="<?php echo eMain::root_url(); ?>plugins/customs/eli_creations.js?d=201912041006"></script>
							<div class="block">
										
								<h1><?php eLang::show_translate('mutlimedia creations'); ?></h1>
								<br />
								<div class="creations"><!--
									--><div class="load"></div><script type="text/javascript">$('.creations .load').css({'position':'absolute', 'top':'-20px', 'left':'-20px', 'width':'calc(100% + 40px)', 'height':'calc(100% + 40px)', 'z-index':1000, 'background-color':'#fff'});</script><!--
<?php
			
								$url = eMain::root_url().$_SESSION['lang'].'/'.eCMS::$url_datas[0].'/';
																													
								for ($c=0;$c<$creas_datas['nb'];$c++) {
								
									$crea_datas = $creas_datas['datas'][$c];															
									$title = eText::iso_htmlentities($crea_datas['title']);								
									$crea_url = $url.urlencode(str_replace('/', '|slash|', $crea_datas['title']));
									
									$folder = eText::str_to_url($crea_datas['title']);
									$folder_path = "images/creations/".$folder.'/';
					
?>
									--><div class="element">
										<div class="imgblock">
											<a href="<?php echo $crea_url; ?>">
												<img src="<?php echo eMain::root_url().$folder_path.$crea_datas['image']; ?>?d=201911271735" width="80" height="60" style="border:0px;" alt="<?php echo $crea_datas ['annee']." : ".$title; ?>" title="<?php echo $crea_datas ['annee']." : ".$title; ?>"/>
											</a>
										</div>
										<a href="<?php echo $crea_url; ?>" class="title"><?php echo $title; ?></a>								
									</div><!--
<?php						
								} // END OF FOR IMG								
?>	
								--></div>	
							</div> <!-- END OF block -->
