			<!-- START OF SLIDESHOW -->
						
			<?php
								
				// CMS SELECTED IMAGES //
				$rq = "SELECT name, description, extension, width, height FROM ".eParams::$prefix.'_'.$_SESSION['lang']."_cms_images WHERE slideshow_pos>0 ORDER BY slideshow_pos ASC, name ASC;";
				$images_datas = eMain::$sql->sql_to_array($rq);
				$liste_image = array();
				for ($i=0;$i<$images_datas['nb'];$i++) {
					$this_img = $images_datas['datas'][$i];
					$liste_image[] = array('name'=>$this_img['name'].'.'.$this_img['extension'], 'size'=>array($this_img['width'], $this_img['height']), 'description'=>$this_img['description']);
				}
				
				$images = $liste_image;
				$nb_img = count($images);
				
				?>
							
			<div id="slideshow" class="loading">
							
				<div class="menu"><!--
<?php								
						for($i=0;$i<$nb_img;$i++) {						
?>
					--><div class="selector <?php if ($i==0) { echo 'selected'; } ?>"><!--
						--><div class="num"><?php echo $i+1; ?></div><!--
					--></div><!--
<?php
						} // END FOR IMG			
					?>
				--></div>
			
				<div id="slider_content" class="popup_size">
<?php
					for($i=0; $i<$nb_img;$i++) {						
						$image_datas = $images[$i];
						
						$description = $image_datas['description'];
						if (empty($description)) { $description = $image_datas['name']; }
?>
					<div class="slider" id="slider_<?php echo $i; ?>"><div class="img_container"><img src="<?php echo eMain::root_url(); ?>images/<?php echo $image_datas['name']; ?>" width="<?php echo $image_datas['size'][0]; ?>" height="<?php echo $image_datas['size'][1]; ?>" alt="<?php echo $description; ?>" title="<?php echo $description; ?>" /></div></div>
<?php
					}
?>	
				</div>
				
				<div id="arrows">
					<div id="previous"></div>
					<div id="next"></div>
				</div>
				
				<div class="description"><?php if (count($images)>0) { echo $images[0]['description']; } ?></div>
			</div>
							
			<script src="<?php echo eMain::root_url(); ?>js/jquery-ui-1.12.1.min.js"></script>
			<script src="<?php echo eMain::root_url(); ?>plugins/basics/slideshow.js?d=201602031554" type="text/javascript"></script>

			<!-- END OF SLIDESHOW -->