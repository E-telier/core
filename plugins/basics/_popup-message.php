<?php 
	
	include('../../_eCore/eMain.php');
	eMain::start_app();
		
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">

	<head>
							
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>POPUP MESSAGE</title>
		
	</head>
	<body>
	<!-- START OF PAGE -->
<?php 	

		if (!isset($_GET['cancel'])) {
			$_GET['cancel'] = 'cancel';
		}
		if (!isset($_GET['action'])) {
			$_GET['action'] = 'validate';
		}
				
		echo stripslashes(eText::style_to_html($_GET['message'])); 
?>
				
		<form id="message" method="post" action="">
			<div class="col unicol">				
				
<?php
		if (isset($_GET['javascript'])) {
			//echo $_GET['javascript'];
?>
					<input type="button" class="button" onclick="<?php echo stripslashes($_GET['javascript']); ?>" name="ok" value="<?php echo stripslashes(eText::iso_htmlentities(eLang::translate($_GET['action'], "ucfirst"))); ?>" />
<?php
		}

	if ($_GET['cancel']!='false') {
?>
					<input type="button" class="button" onclick="ePopup.closePopup($(this));" name="cancel_btn" value="<?php echo eText::iso_htmlentities(eLang::translate($_GET['cancel'], "ucfirst")); ?>" />
<?php
	}
?>
							
			</div>
		</form>
		<!-- END OF PAGE -->
	</body>
</html>
<?php			
	eMain::end_app();
?>
	