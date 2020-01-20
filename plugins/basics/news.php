	<script type="text/javascript">
	<!--
		$(document).ready(function() {
			//$('#content *').css({'font-family':'Arial, helvetica, sans'});
		});		
	-->
	</script>
<?php

	global $currentDatas;
	global $table_prefix;
	global $rootURL;

	$ref = $currentDatas[1];
	
	$rq_news = "SELECT global_ref, object, text, date FROM ".$table_prefix."_newsletter_newsletters WHERE selected=1;";
	$result_news = sqlToArray($rq_news);
	
	$block_content='';
	for ($i=0;$i<$result_news['nb'];$i++) {
		$content_news = $result_news['datas'][$i];
		
		if (strToURL($content_news['object'])==$ref) {			
?>
			<h1><?php echo iso_htmlentities($content_news['object']); ?></h1>
			<?php echo styleToHTML($content_news['text']); ?>
			<br />
			<br />
			<div class="more">Publié le <?php echo formatDate($content_news['date'])?></div>
			<a href="<?php echo $rootURL."cms/plugins/newsletter/preview.php?newsletter_ref=".$content_news['global_ref']; ?>" target="_blank" class="more">Voir la newsletter</a>
			<br />
<?php
		}
	}
	
	echo styleToHTML($block_content);
	
?>