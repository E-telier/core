<div class="form_contact">

	<?php
	
		include('plugins/basics/capcha/eCapcha.php');
		$capcha = new eCapcha('capcha', 5, 64);
			
		if (isset($_POST['contact_txt'])) {
			
			// SPAM CHECK //
			$forbidden = array();
			$forbidden[] = '/[\x{0400}-\x{04FF}]/imu'; // Cyrillic
			$forbidden[] = '/[\x{4e00}-\x{9fff}]/imu'; // Chinese
			$forbidden[] = '/[\x{0600}-۾]/imu'; // Arabic
			$forbidden[] = '/\.ru\//imu'; // Russian link
			$forbidden[] = '/penis/imu'; // Spam
			
			$text = $_POST['contact_txt'];
			
			for ($i=0;$i<count($forbidden);$i++) {		
				if (preg_match($forbidden[$i], $text)===1) {
					die('language not supported');
					return 0;
				}
			}
			
			$captcha_success = false;
			if (!isset($_POST['capcha']) || (!empty($_POST['capcha']) && $_POST['soluce_capcha']==$capcha->check_capcha($_POST['capcha']))) {
				$captcha_success = true;
			}
			
			if ($captcha_success==false) {
?>					
				<div class="error">
					<?php echo eText::iso_htmlentities(eLang::translate('Sorry, you seem to have made a mistake in copying the letters ... Try again.', 'ucfirst')); ?>
				</div>						
<?php				
			} else {			
			
				$sender = eParams::$sender_email;
				$titre = eLang::translate('contact from the website', 'ucfirst').' : '.$_POST['contact_subject'];			
				$contenu = stripslashes($_POST['contact_subject'] . "\n\n" . $_POST['contact_txt'] . "\n\n" . $_POST['contact_firstname'] .' '. $_POST['contact_lastname'] . "\n" . $_POST['contact_mail']. "\n" . $_POST['contact_phone']);
				$destinataires = array(eParams::$contact_email);
				
				$destinataires[] = "contact@e-telier.be"; // ADD WEBMASTER
				
				if (isset($_POST['contact_cc'])) {
					$destinataires[] = $_POST['contact_mail'];
				}
				
				if (eMail::send_mail($sender, $titre, $contenu, $destinataires, array('replyto'=>$_POST['contact_mail']))>=0) { 
?>
	<div class="success">
<?php
					echo eText::iso_htmlentities(eLang::translate('E-mail successfully sent', 'ucfirst'));
?>
	</div>
<?php
				} else {
?>
	<div class="error">
<?php
					echo eText::iso_htmlentities(eLang::translate('E-mail could not be sent', 'ucfirst'));
?>
	</div>
<?php
				}
			}
		}
			
		$values = array();
		$values['firstname'] = eText::iso_htmlentities(eLang::translate('firstname', 'ucfirst'))." (".eText::iso_htmlentities(eLang::translate('required', 'ucfirst')).")";
		$values['email'] = eText::iso_htmlentities(eLang::translate('email', 'ucfirst'))." (".eText::iso_htmlentities(eLang::translate('required', 'ucfirst')).")";
		$values['message'] = eText::iso_htmlentities(eLang::translate('message', 'ucfirst'))."...";
			
	?>

	<script type="text/javascript" src="<?php echo eMain::root_url(); ?>plugins/basics/form_contact.js"></script>
	<script type="text/javascript">
	<!--
	$(document).ready(function(){
		
		var pageID = $('#form_contact').closest('.page').attr('id');
		
		if (pageID!=undefined) {			
			var url = (window.location.href+'').replace(/#.*/gim, '') + '#' + pageID;
			$('#form_contact').attr('action', url);
		}
	});
	
	
	function getValueByName(cName) {
		var defaultValue = '';		
		switch(cName) {
			case 'contact_name' :
				defaultValue = '<?php echo $values['firstname']; ?>';
				break;
			case 'contact_mail' :
				defaultValue = '<?php echo $values['email']; ?>';
				break;
			case 'contact_txt' :
				defaultValue = '<?php echo $values['message']; ?>';
				break;
		}		
		return defaultValue;
	}
	-->
	</script>
	
	<h2><?php echo eText::iso_htmlentities(eLang::translate('contact us', 'ucfirst')); ?></h2>
	
	<form id="form_contact" action="" method="post">			
		<div>
			<div class="col">
				<div class="contact_subject">
					<label for="contact_subject"><?php echo eText::iso_htmlentities(eLang::translate('subject', 'ucfirst')); ?>*</label>
					<input type="text" name="contact_subject" id="contact_subject" required="true" class="needed" value="" />
				</div>
				<div class="contact_txt">
					<label for="contact_txt"><?php echo eText::iso_htmlentities(eLang::translate('message', 'ucfirst')); ?>*</label>
					<textarea name="contact_txt" id="contact_txt" rows="3" cols="60" required="true" class="needed"></textarea>
				</div>
				<?php $capcha->show_capcha(); ?>				
			</div><!--
			--><div class="col">
				<div class="contact_firstname">
					<label for="contact_firstname"><?php echo eText::iso_htmlentities(eLang::translate('firstname', 'ucfirst')); ?>*</label>
					<input type="text" name="contact_firstname" id="contact_firstname" value="" required="true" class="needed" />
				</div>				
				<div class="contact_lastname">
					<label for="contact_lastname"><?php echo eText::iso_htmlentities(eLang::translate('lastname', 'ucfirst')); ?>*</label>
					<input type="text" name="contact_lastname" id="contact_lastname" value="" required="true" class="needed" />
				</div>				
				<div class="contact_mail">
					<label for="contact_mail"><?php echo eText::iso_htmlentities(eLang::translate('email', 'ucfirst')); ?>*</label>
					<input type="email" name="contact_mail" id="contact_mail" value="" required="true" class="needed email" />
				</div>				
				<div class="contact_phone">
					<label for="contact_phone"><?php echo eText::iso_htmlentities(eLang::translate('phone', 'ucfirst')); ?></label>
					<input type="tel" name="contact_phone" id="contact_phone" value="" />
				</div>
				<div class="contact_send">
					<input type="button" name="button" class="button" value="<?php echo eText::iso_htmlentities(eLang::translate('send', 'ucfirst')); ?>" onclick="sendContactForm(this);" />
				</div>
			</div>
			<input type="hidden" value="" id="email" name="email" />			
			
		</div>		
	</form>
	

</div>