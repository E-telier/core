<?php
	// PARAMS //
	$maxImgWidth=1024;
	$maxImgHeight=720;	
	
	$rq_img = "SELECT * FROM ".eCMS::$table_prefix."_cms_images WHERE gallery_pos>0 ORDER BY gallery_pos ASC, name DESC;";
	$images_datas = eMain::$sql->sql_to_array($rq_img);
	$nb_img = $images_datas['nb'];
	$images = $images_datas['datas'];
?>
		
		<script type="text/javascript" src="<?php echo eMain::root_url(); ?>plugins/basics/gallery.js?d=201803131501"></script>
		<script type="text/javascript">
			<!--
			var myGallery;
			$(document).ready(function() {
				myGallery = new Gallery(<?php echo $maxImgWidth; ?>, <?php echo $maxImgHeight; ?>);				
				myGallery.prepare();
			});						
			-->
		</script>
		
		<div id="gallery" style="position:relative;">
			<?php						
			for ($i=0;$i<$nb_img;$i++) {
				$this_img = $images[$i];
				echo eText::style_to_html('[img='.$this_img['name'].' /img]');
			}
			?>
			<div class="clear"> </div>
			<div class="loading"></div>
		</div>
		
		<div id="gallery_bg" style="display:none;">			
			<div id="gallery_closebtn" onclick="myGallery.closeGallery()">X | FERMER</div>
			<div id="gallery_slideshow">
			<?php
						
			for ($i=0;$i<$nb_img;$i++) {
									
				$this_img = $images[$i];
				
				$relative_path = "images/".$this_img['name'].'.'.$this_img['extension'];
				$filePath = eMain::root_url().$relative_path;
				
				$sizes = getimagesize("images/".$this_img['name'].'_full.'.$this_img['extension']);
				$width=$sizes[0];
				$height=$sizes[1];
				
				echo "
					<div class=\"img\">
						<img src=\"".$filePath."\" width=\"".$width."\" height=\"".$height."\" alt=\"".eText::style_to_html($this_img['description'])."\" />
						<div class=\"gallery_legend\"> </div>
					</div>
				";								
								
			}
			
			?>			
			</div>	
			<div class="left_arrow" onclick="myGallery.stepGallery(-1);"><img src="<?php echo eMain::root_url(); ?>design/slideshow_arrow-left-off.gif" width="30" height="25" alt="left" class="switch" /></div>
			<div class="right_arrow" onclick="myGallery.stepGallery(1);"><img src="<?php echo eMain::root_url(); ?>design/slideshow_arrow-right-off.gif" width="30" height="25" alt="right" class="switch" /></div>
			<div id="gallery_closebg"> </div>
		</div>