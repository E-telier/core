<?php
	global $table_prefix;
	global $rootURL;
	global $current_language;

	$rq_news = "SELECT id, object, date FROM ".$table_prefix."_newsletter_newsletters WHERE selected=1 ORDER BY date DESC;";
	$result_news = sqlToArray($rq_news);
		
	for ($i=0;$i<$result_news['nb'];$i++) {
		$content_news = $result_news['datas'][$i];
		
		$block_content = "[div class='more']News du ".formatDate($content_news['date']).'[/div]';
		$block_title = '[HTML]<a href="'.$rootURL.$current_language."/news/".strToURL($content_news['object']).'" title="'.iso_htmlentities($content_news['object']).'">[/HTML]'.$content_news['object']."[/a]";
?>
								
								<?php 
									if (!empty($block_title)) { echo "<h4>".styleToHTML($block_title)."</h4>"; } 
									echo styleToHTML($block_content); 
								?>
<?php
	}
?>