<?php 
	
	include('_eCore/eMain.php');
	eMain::start_app();
	
	include('_eCore/addons/eCMS.php');
	eCMS::start_cms();
		
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">

	<head>
							
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title><?php echo eCMS::$page_datas['title']; ?></title>		
		
		<meta name="Keywords" lang="<?php echo $_SESSION['lang']; ?>" content="<?php echo eText::iso_htmlentities(eCMS::$page_datas['keywords']); ?>" />
		<meta name="description" content="<?php echo eText::iso_htmlentities(eCMS::$page_datas['description']); ?>" />
		
		<meta name="generator" content="CMS E-telier 6.5" />
		<meta name="author" content="E-telier.be" />
		
		<meta name="robots" content="index, follow, all" />
		<meta name="revisit-after" content="7 days" />	
		
		<meta name="viewport" id="testViewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Exo+2:400,800,900,400italic,700,700italic' rel='stylesheet' type='text/css'>
		
		<link href="<?php echo eMain::root_url(); ?>css/css.css?date=201912031602" rel="stylesheet" type="text/css" />
		<link href="<?php echo eMain::root_url(); ?>css/pages_rules.css?date=201911281654" rel="stylesheet" type="text/css" />
				
		<link href="<?php echo eMain::root_url(); ?>css/responsive.css?date=201912041630" rel="stylesheet" type="text/css" />
		
		
		
		<link rel="icon" type="image/icon" href="<?php echo eMain::root_url(); ?>favicon.ico?date=201911271024" />
			
		<!-- USER STYLE -->
		<style type="text/css">
<?php
	$rq = "SELECT * FROM ".eParams::$prefix."_cms_styles WHERE activated=1;";	
	$result_datas = eMain::$sql->sql_to_array($rq);
	$nb_styles = $result_datas['nb'];;
	for($s=0;$s<$nb_styles;$s++) {
		$contenu = $result_datas['datas'][0];
		echo "		
		".$contenu['name']." {
			font-family:".$contenu['text_font'].";
			font-size:".$contenu['text_size']."px;
			color:".$contenu['text_color'].";
			
			background-color:".$contenu['background_color'].";			
			border:".$contenu['border_size']."px solid ".$contenu['border_color'].";
			
			".$contenu['other']."						
		}		
		";
	
	}
?>
		</style>
		<!-- END OF USER STYLE -->
		
	</head>
	
	<body>
	
<?php
		if (!empty(eParams::$ga_id)) {
?>
		<!-- GOOGLE ANALYTICS -->		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo eParams::$ga_id; ?>', 'auto');
			ga('send', 'pageview');

		</script>
		<!-- END OF GOOGLE ANALYTICS -->
<?php
		} // END OF ISSET GA
?>
		
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/jquery-3.4.1.min.js"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/jquery-ui-1.12.1.min.js?d=201912031734"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/jquery.ui.touch-punch-0.2.3.min.js"></script>
		<script type="text/javascript">
			<!-- 
			const CMSRootPath = '<?php echo eMain::root_url(); ?>';			
			-->
		</script>
		
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/_eCore/eCMS.js?date=201810191353"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/_eCore/eTools.js?date=201810191353"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/_eCore/eForm.js?date=201810191353"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/_eCore/ePopup.js?date=201810191353"></script>
		
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/checkbox.js?date=201810191353"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/radio.js?date=201810191353"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>js/style.js?date=201911291558"></script>
					
		<!-- START OF SITE -->
		<div id="site" class="<?php echo eCMS::$page_datas['reference']; ?><?php echo str_replace('*', ' access_', substr(eCMS::$page_datas['access'], 0, strlen(eCMS::$page_datas['access'])-1)); ?>">
		
			<!-- START OF HEADER -->
			<header id="header">
				<div class="pagewidth">
				
					<!-- HEADER CONTENT -->
					<?php eCMS::includeModulesInHTML(eText::style_to_html(eCMS::$page_datas['banner_content']), 'header'); ?>	
					
<?php 
					// LANGUAGES //						
					$nb_lang = count(eParams::$available_languages);
					if ($nb_lang>1) {
?>
					<!-- START OF LANGUAGES -->
					<div id="languages">
<?php 							
							for ($i=0;$i<$nb_lang;$i++) {
								$lang = eParams::$available_languages[$i];
								$lang_str = strtoupper($lang);
								
								$rq_lang = "SELECT reference FROM ".eParams::$prefix.'_'.$lang."_cms_pages WHERE global_ref='".eCMS::$page_datas['global_ref']."' LIMIT 1;";
								$result_lang = eMain::$sql->sql_to_array($rq_lang);
								
								$lang_page_ref = '';
								if ($result_lang['nb']>0) {
									$content_lang = $result_lang['datas'][0];
									$lang_page_ref = $content_lang['reference'];
								}
								$lang_url = eMain::root_url().$lang.'/'.$lang_page_ref;
								
								// ADD PLUGIN URL //
								if (isset($plugin_table)) {
									if (isset($currentDatas[1])) {
									
										// Get global_ref //
										$rq_plugin = "SELECT global_ref FROM $table_ref".$_SESSION['lang']."_back_".$plugin_table." WHERE title='".addslashes(urldecode($currentDatas[1]))."';";
										$result_plugin = mysqli_query($mysqli, $rq_plugin);
										$content_plugin = mysqli_fetch_array($result_plugin);
																				
										// Get title in language //
										$rq_plugin = "SELECT title FROM $table_ref".$lang."_back_".$plugin_table." WHERE global_ref='".$content_plugin['global_ref']."';";	
										//echo $rq_plugin;								
										$result_plugin = mysqli_query($mysqli, $rq_plugin);
										$content_plugin = mysqli_fetch_array($result_plugin);
										
										$lang_url .= '/'.urlencode($content_plugin['title']);
										
									}
								}
								
								
?>
						<a href="<?php echo $lang_url; ?>" <?php if ($lang==$_SESSION['lang']) { ?>class="selected"<?php } ?> id="<?php echo $lang; ?>"><?php echo eText::iso_htmlentities(ucfirst($lang_str)); ?></a>
<?php
							} // END OF FOR LANGUAGES
?>
					</div><!-- END OF LANGUAGES -->
<?php
						} // END OF IF LANGUAGES > 1						
?>

					<!-- MENU -->
					<nav class="menu">					
						<ul><!--
<?php
							$rq = "SELECT reference, menu_name, id, combined, childof FROM ".eCMS::$table_prefix."_cms_pages WHERE (reference=childof OR combined=1) AND menu_position>0 ORDER BY menu_position ASC, reference ASC;";
							
							$rq = eCMS::get_access_page($rq);
							
							$result_datas = eMain::$sql->sql_to_array($rq);
							$nb_menu = $result_datas['nb'];;
							for($m=0;$m<$nb_menu;$m++) {
								$menu_datas = $result_datas['datas'][$m];	
								$menu_name = eText::style_to_html(ucfirst($menu_datas['menu_name']));
								
								$current_section = false;
								if (eCMS::$page_datas['childof']==$menu_datas['reference']) {
									$current_section = true;
									//echo 1;
								} else {
									$rq_grandchild = "SELECT id FROM ".eCMS::$table_prefix."_cms_pages WHERE reference='".eCMS::$page_datas['childof']."' AND childof='".$menu_datas['reference']."';";
									//echo 2;
									if (eMain::$sql->sql_to_num($rq_grandchild)>0) {
										//echo 3;
										$current_section = true;
									}
								}
								
								$combined = false;
								if ($menu_datas['combined']==1 && $menu_datas['childof']==eCMS::$page_datas['reference']) {
									$combined = true;
								} else if ($menu_datas['reference']==eCMS::$page_datas['reference']) {
									$rq_combinator = "SELECT id FROM ".eCMS::$table_prefix."_cms_pages WHERE combined='1' AND childof='".$menu_datas['reference']."';";
									if (eMain::$sql->sql_to_num($rq_combinator)>0) {
										//echo 3;
										$combined = true;
									}
								}
								
								$url = eMain::root_url().$_SESSION['lang'].'/'.$menu_datas['reference'];
								$onclick = '';								
								if ($combined) {
									$url = eMain::root_url().$_SESSION['lang'].'/'.$menu_datas['childof'].'/#'.$menu_datas['reference'];
									$onclick = "eCMS.manageAnchorScroll('".$url."'); return false;";
								}
								
?>
							--><li class="<?php echo $menu_datas['reference']; ?><?php if ($current_section == true) { ?> selected<?php } ?>"><a href="<?php echo $url; ?>" onclick="<?php echo $onclick; ?>"><?php echo $menu_name; ?></a></li><!--
<?php
							} // END OF FOR MENU
?>
						--></ul>
						<div class="clear"> </div>
					</nav><!-- END OF MENU -->


					<div class="clear"> </div>							
				</div><!-- END OF PAGEWIDTH -->
			</header><!-- END OF HEADER -->	

			<!-- START OF PAGES -->
			<div id="pages">
<?php

			$rq = "SELECT * FROM ".eCMS::$table_prefix."_cms_pages WHERE combined=1 AND childof='".eCMS::$page_datas['reference']."' ORDER BY menu_position ASC;";
			$pages_datas = eMain::$sql->sql_to_array($rq);	
			array_unshift($pages_datas['datas'], eCMS::$page_datas);
			$pages_datas['nb']++;
			
			$parent_page_datas = eCMS::$page_datas;
			
			for ($p=0;$p<$pages_datas['nb'];$p++) {
				eCMS::$page_datas = $pages_datas['datas'][$p];
?>							
				<section class="page" id="<?php echo eCMS::$page_datas['reference']; ?>">	
					<!-- START OF MIDDLE -->
					<div class="middle">
						<div class="pagewidth">		

							<!-- TEXT BLOCKS -->
<?php 					
						$rq = "SELECT * FROM ".eCMS::$table_prefix."_cms_blocks WHERE content_block=0 AND ((pages_ref='*' OR sections_ref='*') OR (pages_ref LIKE '%*".eCMS::$page_datas['reference']."*%' OR sections_ref LIKE '%*".eCMS::$page_datas['childof']."*%')) ORDER BY position ASC;";
						//echo $rq;
						$result_datas = eMain::$sql->sql_to_array($rq);
						$nb_block = $result_datas['nb'];;				
						for($b=0;$b<$nb_block;$b++) {
							$block_datas = $result_datas['datas'][$b];
							
							if ($b==0) {
?>						
							<aside class="blocks"><!--
<?php
							} // END IF b==0					
						
							$block_title = eText::style_to_html($block_datas['title']);
							$block_content = eText::style_to_html($block_datas['content']);
							$style = '';
							$class=$block_datas['reference'];
							if (!empty($block_datas['bgcolor'])) { 
								$style.='background-color:#'.$block_datas['bgcolor'].';';
								$rgb = hex2rgb($block_datas['bgcolor']);
								if ($rgb[0]+$rgb[1]+$rgb[2]>=600) { $class.=' darktext'; } else { $class.=' lighttext'; }
							}
							if ($block_datas['textalign']!='initial') { 
								$class.=' align_'.$block_datas['textalign'];
							}
							if (!empty($style)) { $style = 'style="'.$style.'"'; }
?>
								--><div id="block_<?php echo $block_datas['id']; ?>" class="block <?php echo $class; ?>" <?php echo $style; ?>>
									<?php if (!empty($block_title)) { echo "<h3>".$block_title."</h3>"; } ?> 
									<?php eCMS::includeModulesInHTML($block_content, 'block'); ?> 
								</div><!-- END OF BLOCK --><!--
<?php
							
							if ($b==$nb_block-1) {
?>
							--></aside><!-- END OF BLOCKS -->
<?php
							} // END IF LAST b						
							
						} // END FOR BLOCKS
?>				
							<!-- END OF TEXT BLOCKS -->
						
							<!-- CONTENT -->
<?php							
						if (!empty(eCMS::$page_datas['content'])) {
?>					
							<div class="content">
								<?php eCMS::includeModulesInHTML(eText::style_to_html(eCMS::$page_datas['content']), 'content'); ?>						
							</div>
							<!-- END OF CONTENT -->																						
<?php		
						} // END OF IF CONTENT
?>									
							<div class="clear"> </div>
						</div><!-- END OF PAGEWIDTH -->	
					</div><!-- END OF MIDDLE -->
				</section><!-- END OF PAGE -->			
<?php
			} // END FOR COMBINED PAGES			
			eCMS::$page_datas = $parent_page_datas;
?>
			</div><!-- END OF PAGES -->
			
			<!-- START OF FOOTER -->
			<footer id="footer">
				<div class="pagewidth">
					<?php eCMS::includeModulesInHTML(eText::style_to_html(eCMS::$page_datas['footer_content']), 'footer'); ?>
					<div class="clear"> </div>
				</div><!-- END OF PAGEWIDTH -->	
			</footer>
			<!-- END OF FOOTER -->
						
		</div>	
		<!-- END OF SITE -->
		
	</body>	
</html>
<?php			
	eMain::end_app();
?>