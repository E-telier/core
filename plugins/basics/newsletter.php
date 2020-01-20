<div class="form_inscription">

	<?php
	
		global $current_language;
		global $table_prefix;
		global $sender_email;
		global $contact_email;
		global $mysqli;
				
		if (isset($_GET['unsubscribe'])) {
			$rq = "UPDATE ".$table_prefix."_newsletter_clients SET activated=0 WHERE email='".$_GET['unsubscribe']."';";
			if (!mysqli_query($mysqli, $rq)) { echo mysqli_error(); } else { echo styleToHTML(getTranslation('successfully unsubscribed', 'ucfirst')); }
		}
	
		if (isset($_POST['newsletter_mail'])) {
			$sender = $sender_email;//"contact@e-telier.be";
			$titre = $site_name." : inscription";
			$contenu = getTranslation('subscription on the website', 'ucfirst')." : ".$_POST['newsletter_mail'];
			$destinataires = array("contact@e-telier.be", $_POST['newsletter_mail'], $contact_email);//, "info@marieklein.be");
			if (sendMail($sender, $titre, $contenu, $destinataires)>=0) { 
				//echo styleToHTML(getTranslation('success']);

				$rq = "SELECT activated FROM ".$table_prefix."_newsletter_clients WHERE email=\"".$_POST['newsletter_mail']."\" LIMIT 1;";
				$result_datas = sqlToArray($rq);
				if ($result_datas['nb']>0) {
					$contact_datas = $result_datas['datas'][0];
					if ($contact_datas['activated']==1) {				
						echo styleToHTML(getTranslation('email already subscribed', 'ucfirst'));
					} else {
						$rq_subscribe = "UPDATE ".$table_prefix."_newsletter_clients SET activated=1 WHERE email='".$_POST['newsletter_mail']."';";						
					}
				} else {
					$rq_subscribe = "INSERT INTO ".$table_prefix."_newsletter_clients (email, languages, ceo, tax) VALUES (\"".$_POST['newsletter_mail']."\", \"fr\", 1, \"oui\");";					
				} 
				
				if (isset($rq_subscribe)) {
					if (mysqli_query($mysqli, $rq_subscribe)) {
						echo styleToHTML(getTranslation('successful subscription', 'ucfirst'));
					} else {
						echo mysqli_error();
					}
				}
				
			} else {
				echo "...";
			}
				
		}
			
		$values = array();
		$values['email'] = getTranslation('your email', 'ucfirst');
			
	?>

	<script type="text/javascript">
		function subscribe() {
			if (document.forms["form_inscription"].elements["email"].value=='') {
				
				var error = false;
												
				if (!error) {
					// email //
					if ($('#newsletter_mail').val().match('[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+')==null) {					
						$('#newsletter_mail').css({'border':'1px solid #ff0000'});
						alert("<?php echo getTranslation('incorrect email format', 'ucfirst'); ?>");
						error = true;
					}
				}
				
				if (!error) {
					// SEND //
					document.forms["form_inscription"].submit();
				}
			}
		}		
	</script>
	<form id="form_inscription" action="" method="post">
		<div>
			<h3><?php echo iso_htmlentities(getTranslation('subscribe to our newsletter', 'ucfirst')); ?></h3>
			<input type="text" name="newsletter_mail" id="newsletter_mail" value="<?php echo $values['email']; ?>" onfocus="if (this.value=='<?php echo $values['email']; ?>') { this.value=''; }" onblur="if (this.value=='') { this.value='<?php echo $values['email']; ?>'; }" />
			<input type="button" name="send" value="<?php echo getTranslation('send', 'ucfirst'); ?>" onclick="subscribe();" />
			<input type="hidden" value="" name="email" />
		</div>
	</form>
	<div class="clear"> </div>

</div>