
<?php	
	global $table_ref;
	global $current_language;	
	global $rootURL;
	
	$dictionnary = array();
	$dictionnary['fr'] = array();	
	$dictionnary['fr']['more'] = "Lire la suite";
	
	$dictionnary['en'] = array();	
	$dictionnary['en']['more'] = "Read more";
	
	$dictionnary['nl'] = array();	
	$dictionnary['nl']['more'] = "Meer lezen";
	
	$translation = $dictionnary[$current_language];
	
?>
									<script type="text/javascript">
									<!--										
										$(document).ready(function() {
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/events_last.css?d=201405071218" type="text/css" />');
										});
									-->
									</script>
<?php

	$rq = "SELECT reference FROM $table_ref".$current_language."_cms_pages WHERE content LIKE '%plugins/events.php%' LIMIT 1;";
	$result_datas = sqlToArray($rq);
	$content = $result_datas['datas'][0];
	$dest_page = $content['reference'];

	$rq = "SELECT title, content, image, date_from, date_to FROM $table_ref".$current_language."_back_events WHERE visible=1 AND (date_to='' AND date_from>='".date('Y-m-d')."') OR date_to>='".date('Y-m-d')."' ORDER BY date_from ASC, date_to DESC LIMIT 3;";
	$result_datas = sqlToArray($rq);
	while($content = mysqli_fetch_array($result)) {
	
		if (!empty($content['image'])) {
			$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
			$result_img = mysqli_query($mysqli, $rq_img);
			$content_img = mysqli_fetch_array($result_img);
		}
		$this_url = $rootURL.$current_language.'/'.$dest_page.'/'.urlencode($content['title']);
		$this_title = iso_htmlentities($content['title']);
		
		$short_text = substr_nextword(noStyle($content['content']), 128, '');
		
?>
									<div class="event">
										<h4><a href="<?php echo $this_url; ?>" title="<?php echo $this_title; ?>"><?php echo formatDate($content['date_from']); if (!empty($content['date_to'])) { echo " - ".formatDate($content['date_to']); } ?></a></h4>
										<?php if (!empty($content['image'])) { ?><a href="<?php echo $this_url; ?>" title="<?php echo $this_title; ?>"><span class="img center img_center"><img src="<?php echo $rootURL.'images/'.$content['image'].'.'.$content_img['extension']; ?>" width="100%" height="auto" alt="<?php echo $content['title']; ?>" /></span></a><?php } // END IF IMAGE ?>
										<h4><a href="<?php echo $this_url; ?>" title="<?php echo $this_title; ?>"><?php echo styleToHTML($content['title']); ?></a></h4>
										<?php echo $short_text; ?> <a href="<?php echo $this_url; ?>" title="<?php echo $this_title; ?>">[...]</a>
										<br /><br />
										<a href="<?php echo $this_url; ?>" title="<?php echo $this_title; ?>"><?php echo iso_htmlentities($translation['more']); ?></a>
									</div>		
<?php		
	} // END WHILE RESULT
?>

