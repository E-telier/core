							<div id="events">
<?php	
	global $table_ref;
	global $current_language;	
	global $rootURL;
	global $page_datas;
	global $currentDatas;
	
	$dictionnary = array();
	$dictionnary['fr'] = array();
	$dictionnary['fr']['posted'] = "Posté le";
	$dictionnary['fr']['back'] = "Retour";
	$dictionnary['fr']['search_title'] = "Recherche d'un événement";
	$dictionnary['fr']['search_date'] = "Recherche par date";
	$dictionnary['fr']['to_date'] = "au";
	$dictionnary['fr']['search_keywords'] = "Recherche par mots-clés";
	$dictionnary['fr']['search_btn'] = "Rechercher";
	$dictionnary['fr']['more'] = "Lire la suite";
	
	$dictionnary['en'] = array();
	$dictionnary['en']['posted'] = "Posted the";
	$dictionnary['en']['back'] = "Back";
	$dictionnary['en']['search_title'] = "Search for an event";
	$dictionnary['en']['search_date'] = "Search by date";
	$dictionnary['en']['to_date'] = "to";
	$dictionnary['en']['search_keywords'] = "Search with keywords";
	$dictionnary['en']['search_btn'] = "Search";
	$dictionnary['en']['more'] = "Read more";
	
	$dictionnary['nl'] = array();
	$dictionnary['nl']['posted'] = "Posted the";
	$dictionnary['nl']['back'] = "Back";
	$dictionnary['nl']['search_title'] = "Event Zoeken";
	$dictionnary['nl']['search_date'] = "Zoeken op datum";
	$dictionnary['nl']['to_date'] = "tot";
	$dictionnary['nl']['search_keywords'] = "Zoeken op keywords";
	$dictionnary['nl']['search_btn'] = "Zoeken";
	$dictionnary['nl']['more'] = "Meer lezen";
	
	$translation = $dictionnary[$current_language];
	
	
?>
									<script type="text/javascript">
									<!--
										var CMSRootPath = '<?php echo $rootURL; ?>';
										$(document).ready(function() {
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/events.css?d=201405071218" type="text/css" />');
										});
									-->
									</script>
<?php
	if (isset($currentDatas[1])) {
		// SHOW CURRENT EVENTS //
		$rq = "SELECT * FROM $table_ref".$current_language."_back_events WHERE title='".addslashes(urldecode($currentDatas[1]))."';";		
		$result_datas = sqlToArray($rq);
		$content = $result_datas['datas'][0];
		
		$rq = "UPDATE $table_ref".$current_language."_back_events SET views=views+1 WHERE id=".$content['id'].";";
		//echo $rq;
		mysqli_query($mysqli, $rq);
		
		if (!empty($content['image'])) {
			$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
			$result_img = mysqli_query($mysqli, $rq_img);
			$content_img = mysqli_fetch_array($result_img);
		}
		
?>
									<div class="event">
										<h3><?php echo styleToHTML($content['title']); ?></h3>
										<div class="date"><?php echo formatDate($content['date_from']); if (!empty($content['date_to'])) { echo " - ".formatDate($content['date_to']); } ?></div>
										<?php if (!empty($content['image'])) { ?><div class="img center img_center"><img src="<?php echo $rootURL.'images/'.$content['image'].'.'.$content_img['extension']; ?>" width="100%" height="auto" alt="<?php echo $content['title']; ?>" /></div><?php } // END IF IMAGE ?>
										<br />
										<?php echo styleToHTML($content['content']); ?> 
										<br /><br />
										<a href="<?php echo $rootURL.$current_language.'/'.$page_datas['reference']; ?>"><?php echo iso_htmlentities($translation['back']); ?></a>
										<br /><br />
										<div class="fb-share-button" data-href="<?php echo curPageURL(); ?>" data-type="button_count"></div>
									</div>
<?php
		
	} else {
	
		$sql_clauses = "WHERE title<>'' AND visible=1";
		if (isset($_POST['date_from'])) {
			// SEARCH EVENTS //
			
			$sql_clauses .= " AND ((date_from>='".formatDate($_POST['date_from'], "Y-m-d")."' AND date_from<='".formatDate($_POST['date_to'], "Y-m-d")."')";
			$sql_clauses .= " OR (date_to>='".formatDate($_POST['date_from'], "Y-m-d")."' AND date_to<='".formatDate($_POST['date_to'], "Y-m-d")."'))";
			
			if (!empty($_POST['keywords'])) {
				$sql_clauses.= " AND (false";
				$keywords = explode(' ', $_POST['keywords']);
				for ($k=0;$k<count($keywords);$k++) {
					if (!empty($keywords[$k])) {
						$sql_clauses.= " OR (keywords LIKE '%".$keywords[$k]."%' OR title LIKE '%".$keywords[$k]."%' OR content LIKE '%".$keywords[$k]."%')";
					}
				}
				$sql_clauses.= ")";
			}
			$values = $_POST;
			$limit="20";
		} else {
		
			// SHOW LAST //
			$sql_clauses .= " AND ((date_to<>'' AND date_to>='".date('Y-m-d')."') OR (date_to='' AND date_from>='".date('Y-m-d')."'))";
		
			$values = array();
			
			$rq = "SELECT date_from FROM $table_ref".$current_language."_back_events ORDER BY date_from ASC LIMIT 1;";
			$result_datas = sqlToArray($rq);
			$content = $result_datas['datas'][0];
			
			$values['date_from'] = formatDate(date('Y-m-d'));
			$time = strtotime(date('Y-m-d'));
			$values['date_to'] = date('d/m/Y', strtotime("+3 month", $time));
			$values['keywords'] = '';
			$limit="3";
		}	
	
?>
																		
									<h1><?php echo iso_htmlentities($translation['search_title']); ?></h1>
									<form id="form_articles" method="post" action="">
										<div>
											<label for="date"><?php echo iso_htmlentities($translation['search_date']); ?></label><div class="inputs dates"><input type="text" value="<?php echo $values['date_from']; ?>" name="date_from" /> <?php echo iso_htmlentities($translation['to_date']); ?> <input type="text" value="<?php echo $values['date_to']; ?>" name="date_to" /></div>
											<br />
											<label for="keywords"><?php echo iso_htmlentities($translation['search_keywords']); ?></label><div class="inputs"><input type="text" name="keywords" id="keywords" value="<?php echo $values['keywords']; ?>" /></div>
											<br />
											<input type="submit" name="submit" value="<?php echo iso_htmlentities($translation['search_btn']); ?>" />
											<div class="clear"> </div>
										</div>
									</form>	
<?php	

		$rq = "SELECT * FROM $table_ref".$current_language."_back_events $sql_clauses ORDER BY date_from ASC, date_to DESC LIMIT ".$limit.";";
		//echo $rq;
		$result_datas = sqlToArray($rq);
		while($content = mysqli_fetch_array($result)) {
		
			if (!empty($content['image'])) {
				$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
				$result_img = mysqli_query($mysqli, $rq_img);
				$content_img = mysqli_fetch_array($result_img);
			}
?>
									<div class="event">
										<h3><?php echo styleToHTML($content['title']); ?></h3>
										<div class="date"><?php echo formatDate($content['date_from']); if (!empty($content['date_to'])) { echo " - ".formatDate($content['date_to']); } ?></div>
										<?php if (!empty($content['image'])) { ?><div class="img center img_center"><img src="<?php echo $rootURL.'images/'.$content['image'].'.'.$content_img['extension']; ?>" width="100%" height="auto" alt="<?php echo $content['title']; ?>" /></div><?php } // END IF IMAGE ?>
										<br />
										<?php 
										
											$text_only = noStyle($content['content']);
											$short_text = substr_nextword($text_only, 255);
											
											echo $short_text;
											 
										?>  
										<br /><br />
										<a href="<?php echo $rootURL.$current_language.'/'.$page_datas['reference'].'/'.urlencode($content['title']); ?>"><?php echo iso_htmlentities($translation['more']); ?></a>
									</div>		
<?php		
		} // END WHILE RESULT
	} // END IF NO EVENT
?>
	
						</div><!-- END OF EVENTS -->
