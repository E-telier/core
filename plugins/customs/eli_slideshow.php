<?php

	$rq = "SELECT id, image, cat, title FROM ".eCMS::$table_prefix."_back_creations WHERE visible=1 AND hidden=0 ORDER BY annee DESC, id DESC;";
	$rea_datas = eMain::$sql->sql_to_array($rq);
?>

				<div id="creations" style="height:150px; overflow:hidden; opacity:0.0;">

					<div id="slideshow"><!--
<?php		
	for ($i=0;$i<$rea_datas['nb'];$i++) {
		$content = $rea_datas['datas'][$i];
?>
						--><a href="<?php echo eMain::cur_folder_url()."creations/".urlencode($content['title']); ?>"><!--
							--><img src="<?php echo eMain::root_url(); ?>images/creations/<?php echo eText::str_to_url($content['title']).'/'.$content['image']; ?>" width="160" height="120" alt="<?php echo $content['cat']; ?> : <?php echo $content['title']; ?>" /><!--
							--><div class="title"><?php echo eText::iso_htmlentities($content['title']); ?></div><!--
						--></a><!--
<?php
	} // END OF FOR //
?>			
						
					--></div>
					
					<div id="controls">
						<div id="position"> </div>
					</div>
					
					<div id="maskleft"> </div>
					<div id="maskright"> </div>
					
				</div>
										
				<script type="text/javascript" src="<?php echo eMain::root_url(); ?>plugins/customs/eli_slideshow.js?d=201912041615"></script>
					
				