							<div id="articles">
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
	$dictionnary['fr']['search_title'] = "Recherche d'un article";
	$dictionnary['fr']['search_date'] = "Recherche par date";
	$dictionnary['fr']['to_date'] = "au";
	$dictionnary['fr']['search_keywords'] = "Recherche par mots-clés";
	$dictionnary['fr']['search_btn'] = "Rechercher";
	$dictionnary['fr']['more'] = "Lire la suite";
	
	$dictionnary['en'] = array();
	$dictionnary['en']['posted'] = "Posted the";
	$dictionnary['en']['back'] = "Back";
	$dictionnary['en']['search_title'] = "Search for an article";
	$dictionnary['en']['search_date'] = "Search by date";
	$dictionnary['en']['to_date'] = "to";
	$dictionnary['en']['search_keywords'] = "Search with keywords";
	$dictionnary['en']['search_btn'] = "Search";
	$dictionnary['en']['more'] = "Read more";
	
	$dictionnary['nl'] = array();
	$dictionnary['nl']['posted'] = "Posted the";
	$dictionnary['nl']['back'] = "Back";
	$dictionnary['nl']['search_title'] = "Artikel Zoeken";
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
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/articles.css?d=201405071218" type="text/css" />');
										});
									-->
									</script>
<?php
	if (isset($currentDatas[1])) {
		// SHOW CURRENT ARTICLE //
		$rq = "SELECT * FROM $table_ref".$current_language."_back_articles WHERE title='".addslashes(urldecode($currentDatas[1]))."';";		
		$result_datas = sqlToArray($rq);
		$content = $result_datas['datas'][0];
		
		$rq = "UPDATE $table_ref".$current_language."_back_articles SET views=views+1 WHERE id=".$content['id'].";";
		mysqli_query($mysqli, $rq);
		
		if (!empty($content['image'])) {
			$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
			$result_img = mysqli_query($mysqli, $rq_img);
			$content_img = mysqli_fetch_array($result_img);
		}
		
?>
									<div class="article">
										<h3><?php echo styleToHTML($content['title']); ?></h3>
										<div class="date"><?php echo iso_htmlentities($translation['posted']); ?> <?php echo formatDate($content['creation_date']); ?></div>
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
			// SEARCH ARTICLE //
			$sql_clauses .= " AND creation_date>='".formatDate($_POST['date_from'], "Y-m-d")."' AND creation_date<='".formatDate($_POST['date_to'], "Y-m-d")."'";
			
			if (!empty($_POST['keywords'])) {
				$sql_clauses.= "AND (false";
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
			// SHOW LAST ARTICLES //
			$values = array();
			
			$rq = "SELECT creation_date FROM $table_ref".$current_language."_back_articles ORDER BY creation_date ASC LIMIT 1;";
			$result_datas = sqlToArray($rq);
			$content = $result_datas['datas'][0];
			
			$values['date_from'] = formatDate($content['creation_date']);
			$values['date_to'] = date('d/m/Y');
			$values['keywords'] = '';;
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

		$rq = "SELECT * FROM $table_ref".$current_language."_back_articles $sql_clauses ORDER BY creation_date DESC LIMIT ".$limit.";";
		$result_datas = sqlToArray($rq);
		while($content = mysqli_fetch_array($result)) {
			if (!empty($content['image'])) {
				$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
				$result_img = mysqli_query($mysqli, $rq_img);
				$content_img = mysqli_fetch_array($result_img);
			}
?>
									<div class="article">
										<h3><?php echo styleToHTML($content['title']); ?></h3>
										<div class="date"><?php echo iso_htmlentities($translation['posted']); ?> <?php echo formatDate($content['creation_date']); ?></div>
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
	} // END IF NO ARTICLE
?>
	
						</div><!-- END OF ARTICLES -->
