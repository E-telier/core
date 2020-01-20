<?php 
	ini_set('memory_limit', '512M');
	
	include('../_eCore/eMain.php');
	eMain::start_app();
	
	include('eCMS.php');
	eCMS::start_cms();
			
	$title = "CMS E-telier 6.5";
	$keywordsString = "\"CMS E-telier 6.5\"";
	$pageDescription = "\"CMS E-telier 6.5\"";
	$top = 			
	'			
				<div class="header_left">
					<h1>CMS E-telier 6.5</h1>
				</div>
	';
	$bottom = "CMS E-telier 6.5 | [url='http://www.e-telier.be']Created by E-telier[/url][/div][div class='clear']";
	
	$nb_lang = count(eParams::$available_languages);

	
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

	<head>
	
		<title><?php echo $title; ?></title>
				
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="Author" lang="fr" content="Elliot Coene" />
		<meta name="Keywords" lang="fr" content=<?php echo $keywordsString; ?> />
		<meta name="description" content=<?php echo $pageDescription; ?> />
		<meta name="Identifier-URL" content="http://www.e-telier.be" />
		<meta name="Reply-to" content="contact@e-telier.be" />
		<meta name="Publisher" content="ovh" />
		<meta name="robots" content="index, follow, all" />
		<meta name="revisit-after" content="7 days" />
		<meta name="classification" content="programmation" />		
		<meta name="google-site-verification" content="" />
							
		<link href="<?php echo eMain::cur_folder_url(); ?>jquery-ui-1.12.1.custom/jquery-ui.min.css?date=201704111423" rel="stylesheet" type="text/css" />
		<link href="<?php echo eMain::cur_folder_url(); ?>jquery-ui-1.12.1.custom/jquery-ui.structure.min.css?date=201704111423" rel="stylesheet" type="text/css" />
		<link href="<?php echo eMain::cur_folder_url(); ?>jquery-ui-1.12.1.custom/jquery-ui.theme.min.css?date=201704111423" rel="stylesheet" type="text/css" />							
		
		<link href="../css/css.css?date=201810171110" rel="stylesheet" type="text/css" />
		<link href="cms.css?date=201912031643" rel="stylesheet" type="text/css" />
		<link href="custom.css?date=201912031638" rel="stylesheet" type="text/css" />
			
	</head>
			
	<body>
		<script type="text/javascript">
			var CMSRootPath = '<?php echo eMain::cur_folder_url(); ?>';
			CMSRootPath = CMSRootPath.replace('/cms', '');
			//alert(CMSRootPath);
		</script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="<?php echo eMain::cur_folder_url(); ?>jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
		
		<script type="text/javascript" src="wysiwyg.js?date=201810171110"></script>
		<script type="text/javascript" src="custom_select.js?date=201810171110"></script>
		<script type="text/javascript" src="../js/_eCore/eForm.js?date=201810171110"></script>
		<script type="text/javascript" src="../js/_eCore/ePopup.js?date=201810171110"></script>
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>../js/_eCore/eTools.js?date=201804271824"></script>
		
		<div id="site">
			<header id="header">
				<div class="pagewidth">
					<?php echo $top; ?>
					
<?php
	// CONNECTION //	
	$access = false;	
	if (eUser::getInstance()->checked) {
		$user_datas = eUser::getInstance()->get_datas('domain', true);
		
		if ($user_datas['domain']== '*' || strpos($user_datas['domain'], '*admin*')!==false) {				
			if (isset($_GET['back'])) {					
				unset($_SESSION[eCMS::$table_prefix.'_page']);
			} else {
				unset($_SESSION[eCMS::$table_prefix.'_table']);
			}
		
			$page = "";
			if (isset($_GET['p'])) {
				$page = $_GET['p'];;
				$_SESSION[eCMS::$table_prefix.'_page'] = $page;
			} else if (isset($_SESSION[eCMS::$table_prefix.'_page'])) { $page = $_SESSION[eCMS::$table_prefix.'_page']; }
			
			$table = "";
			if (isset($_GET['table'])) {
				$table = $_GET['table'];;
				$_SESSION[eCMS::$table_prefix.'_table'] = $table;
			} else if (isset($_SESSION[eCMS::$table_prefix.'_table'])) { $table = $_SESSION[eCMS::$table_prefix.'_table']; }
			
			$access = true;
		} else {
			eMain::add_error("this user is not allowed to access this page");
		}
	}
	if ($access) {
?>					

					<div class="header_right">
						<nav class="menu">
							<ul>							
															
								<li <?php if ($page=="pages") { echo "class=\"selected\"";} ?>><a href="index.php?p=pages">Pages</a></li>
								<li <?php if ($page=="blocks") { echo "class=\"selected\"";} ?>><a href="index.php?p=blocks">Text-blocks</a></li>
								<li <?php if ($page=="images") { echo "class=\"selected\"";} ?>><a href="index.php?p=images">Images</a></li>
								<li <?php if ($page=="params") { echo "class=\"selected\"";} ?>><a href="index.php?p=params">Params du site</a></li>
								<li <?php if ($page=="fichiers") { echo "class=\"selected\"";} ?>><a href="index.php?p=fichiers">Fichiers</a></li>
								<li <?php if ($page=="users") { echo "class=\"selected\"";} ?>><a href="index.php?p=users">Utilisateurs</a></li>
							
								<li <?php if ($page=="styles") { echo "class=\"selected\"";} ?>><a href="index.php?p=styles">Style / CSS</a></li>
								<li id="quit_btn" style=""><a href="index.php?saveandquit=1">Save & QUIT</a></li>
							</ul>
							
<?php
						if (isset($_GET['saveandquit'])) {
							eMain::$sql->backup('prefix');
							session_unset();
							header('Location: '.eMain::cur_page_url(true).'?disconnect=1');
							die('BACKUP SUCCESSFUL');
						}

						// PLUGINS BACK OFFICE //
						$modules_back = array();
						for ($m=0;$m<eCMS::$modules['nb'];$m++) {
							if (eCMS::$modules['datas'][$m]['backoffice']!='') {
								$modules_back[eCMS::$modules['datas'][$m]['reference']] = eCMS::$modules['datas'][$m];
							}
						}
										
						if (count($modules_back)>0) {
							$modules_keys = array_keys($modules_back);
?>
							Back Office :<br />
							<ul>
<?php
							for ($m=0;$m<count($modules_back);$m++) {
								if ($modules_back[$modules_keys[$m]]['backoffice']!='newsletter') {
?>
								<li <?php if ($page==$modules_back[$modules_keys[$m]]['backoffice']) { echo "class=\"selected\"";} ?>><a href="index.php?p=<?php echo $modules_back[$modules_keys[$m]]['backoffice']; ?>"><?php echo eText::iso_htmlentities($modules_back[$modules_keys[$m]]['name']); ?></a></li>
<?php
								}
							} // END FOR PLUGINS BACK
?>
							</ul>
<?php						
							if (array_search('newsletter', $modules_keys)!==false) {
?>
						<br />Newsletter :<br />
							<ul>								
								<li <?php if ($page=="newsletter_clients") { echo "class=\"selected\"";} ?>><a href="index.php?p=newsletter_clients">Contacts</a></li>
								<li <?php if ($page=="newsletter_attachments") { echo "class=\"selected\"";} ?>><a href="index.php?p=newsletter_attachments">Pièces jointes</a></li>
								<li <?php if ($page=="newsletter_newsletters") { echo "class=\"selected\"";} ?>><a href="index.php?p=newsletter_newsletters">Newsletters</a></li>
								<li <?php if ($page=="newsletter_sendings") { echo "class=\"selected\"";} ?>><a href="index.php?p=newsletter_sendings">Envois</a></li>
							</ul>
<?php
							}
						} // END IF PLUGINS BACK						

?>							
						</nav><!-- END OF MENU -->
					</div>
<?php					
	} // END OF IF ACCESS
?>
					<div class="clear" style="clear:both;"> </div>
				</div>				
			</header>		
					
			<div class="middle">
				<div class="pagewidth">	
				
			<?php
			if ($access) {	

				if (isset($_GET['back'])) {
									
					// BACK OFFICE //
					if (isset($_GET['addmod'])) {
						include("back/addmod.php");
					} else {
						include("back/table.php");
					}
				} else {
									
					// CMS //
					if (isset($_GET['addmod'])) {
						$page = $_GET['p'];
						$addmod = $_GET['addmod'];
						include("addmod.php");
					} else if (isset($_GET['p'])) {
						$page = $_GET['p'];
						include("results.php");
					} else {
					
					echo "
				<div class=\"content\">
					<h1>Connexion réussie</h1>
					<div class=\"paragraph\">
						Veuillez choisir une action dans le menu...
					</div>
				</div>
				<div class=\"clear\"> </div>
					";
					
					}						
				}
									
			} // END OF IF ACCESS
			
			if (eMain::get_errors_nb()>0) {
?>
						<div class="error">
							<?php eMain::show_errors(); ?>
						</div>
<?php
			}
			
			if ($access == false) {				
			?>				
					<div class="content">
						<h1>Connexion au système</h1>
						<div class="paragraph">
							Connectez-vous avec votre nom d'utilisateur et votre mot de passe :<br /><br />
							<form method="post" action="index.php" id="connect">
								Username : <input name="connect_login" value="" type="text" />
								<br /><br />
								Password : <input name="connect_password" value="" type="password" />
								<br /><br /><br />
								<input type="submit" name="connect" value="Entrer" />
							</form>
						</div>
					</div>
					<div class="clear"> </div>
			<?php								
			} // END IF NO ACCESS			
			?>
				</div><!-- END OF PAGEWIDTH -->	
			</div><!-- END OF MIDDLE -->
			<div style="clear:both;"> </div>
			
			<div id="footer">
				<div class="pagewidth">
					<?php echo eText::style_to_html($bottom); ?>
				</div>
			</div>	
			
		</div>
		
	</body>
	
</html>
<?php
	eMain::end_app();
?>