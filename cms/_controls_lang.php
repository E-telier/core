	<div id="controls_lang">
		<script type="text/javascript">
		<!--
		
			var current_lang = '';
		
			function showLang(lang) {
												
				$('#controls_lang div').removeClass('selected');
				$('#controls_lang #'+lang+'_btn').addClass('selected');	

				// ADDMOD //
				$('.form_lang').css({'display':'none'});
				$('#form_'+lang).css({'display':'block'});
				$('.addmod').css({'padding-right':'40px', 'background-image':'url(../design/language_'+lang+'.gif)', 'background-repeat':'repeat-y', 'background-position':'top right'});
				
				// RESULT //
				$('.lang_block').css({'display':'none'});
				$('#block_'+lang).css({'display':'block'});
				$('#block_'+lang).css({'padding-right':'30px', 'background-image':'url(../design/language_'+lang+'.gif)', 'background-repeat':'repeat-y', 'background-position':'top right'});
				
				current_lang = lang;
				
			}
			$(document).ready(function() { showLang('<?php if (!isset($_GET['lang'])) { $_GET['lang']=eParams::$available_languages[0]; } echo $_GET['lang']; ?>'); });
		-->
		</script>
<?php
	for ($l=0;$l<$nb_lang;$l++) {
		$temp_lang = eParams::$available_languages[$l];
?>	
		<div id="<?php echo $temp_lang; ?>_btn" onclick="showLang('<?php echo $temp_lang; ?>');"><?php echo strtoupper($temp_lang); ?> <img src="../design/language_<?php echo $temp_lang; ?>.gif" width="30" height="20" /></div>
<?php 
	} // END FOR LANG
?>
	</div>