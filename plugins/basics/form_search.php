<?php 

	global $folderURL;
	global $current_language;
			
	$dictionnary['fr']['recherche'] = "Recherche";		
	$dictionnary['en']['recherche'] = "Search";
	$dictionnary['nl']['recherche'] = "Zoek";
					
	$traductions = $dictionnary[$current_language];
	
	
?>

<script type="text/javascript">
	
	function checkKey(e) {
			
		if (e.which == 13 || e.keyCode == 13) {
		
			var val = $('#search_input').val();
			val = val.split(' ').join('+');
			val = val.split('"').join('&quot;');
            
			window.location.href = '<?php echo $folderURL.strToURL($traductions['recherche']); ?>/'+val;
			
			e.preventDefault();
			
            return false;
        }
        return true;
	}
</script>

<form onsubmit="return false;">
	<input type="text" name="search" id="search_input" value="<?php echo $traductions['recherche']; ?>" onfocus="if (this.value=='<?php echo $traductions['recherche']; ?>') { this.value=''; }" onkeypress="return checkKey(event)" />	
</form>