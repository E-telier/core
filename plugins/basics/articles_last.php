
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
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/articles_last.css?d=201405071218" type="text/css" />');
										});
									-->
									</script>
<?php

	$rq = "SELECT reference FROM $table_ref".$current_language."_cms_pages WHERE content LIKE '%plugins/articles.php%' LIMIT 1;";
	$result_datas = sqlToArray($rq);
	$content = $result_datas['datas'][0];
	$dest_page = $content['reference'];

	$rq = "SELECT title, content FROM $table_ref".$current_language."_back_articles WHERE visible=1 ORDER BY creation_date DESC LIMIT 3;";
	$result_datas = sqlToArray($rq);
	while($content = mysqli_fetch_array($result)) {
	
		$this_url = $rootURL.$current_language.'/'.$dest_page.'/'.urlencode($content['title']);
		$this_title = iso_htmlentities($content['title']);
		
		$short_text = substr_nextword(noStyle($content['content']), 128, '');
											
?>
									<div class="article">
										<h4><a href="<?php echo $this_url ?>" title="<?php echo $this_title ?>"><?php echo styleToHTML($content['title']); ?></a></h4>
										<?php echo $short_text; ?> <a href="<?php echo $this_url ?>" title="<?php echo $this_title ?>">[...]</a>
										<br /><br />
										<a href="<?php echo $this_url ?>" title="<?php echo $this_title ?>"><?php echo iso_htmlentities($translation['more']); ?></a>
									</div>		
<?php		
	} // END WHILE RESULT
?>

