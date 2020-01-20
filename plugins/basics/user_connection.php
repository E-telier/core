<!-- AJAX RESULT --><?php
	if (isset($_POST['connect'])) {
		if (eUser::getInstance()->checked) {
			echo 1;
		} else {
			echo "[ERROR] : ";
			eMain::show_errors(true, false);
		}
		//print_r($_POST);
	}
?><!-- END OF AJAX RESULT -->
<!-- START OF PAGE -->
<script type="text/javascript">
	<!--
	$(document).ready(function() {
		console.log(typeof ePopup);
		console.log(CMSRootPath);
		if (typeof ePopup === 'undefined') {
			$.getScript(CMSRootPath+"js/ePopup.js", function() {
				console.log("ePopup loaded but not necessarily executed.");
			});
		}
		if (typeof eForm === 'undefined') {
			$.getScript(CMSRootPath+"js/eForm.js", function() {
				console.log("eForm loaded but not necessarily executed.");
			});
		}
		
		$('.page #user_connection, .popup #user_connection').css({ 'display': 'block' });
	});

	if (typeof asyncFormSubmitted ==='undefined') {
		function asyncFormSubmitted() {
			window.location.href = '<?php echo eMain::root_url(); ?>';
		}
	}
	-->
</script>
<div id="user_connection" style="display:none;">
	<h2><?php eLang::show_translate('log in'); ?></h2>
	<form id="connection" name="connection" method="post" action="">
		<input type="text" id="connect_login" name="connect_login" value="" placeholder="<?php eLang::show_translate('login'); ?>" />
		<br />
		<input type="password" id="connect_password" name="connect_password" value="" placeholder="<?php eLang::show_translate('password'); ?>" />
		<br />
		<br />
		<div class="">
			<!-- <input type="button" class="submit button" value="<?php eLang::show_translate('validate'); ?>" /> -->
			<div class="submit button"><?php eLang::show_translate('validate'); ?></div>
			<input type="hidden" id="connect" name="connect" value="1" />
		</div>
	</form>
</div>
<!-- END OF PAGE -->
<?php
	if (eUser::getInstance()->checked) {
?>
<a href="?disconnect=1" class="user_connection"><?php eLang::show_translate('logout_btn'); ?></a>
<?php
	} else {
?>
<a href="connection" class="user_connection" onclick="$('#connection').remove(); ePopup.createPopup('connection', {'url':CMSRootPath+'connection'}); return false;"><?php eLang::show_translate('login_btn'); ?></a>
<?php
	}
?>