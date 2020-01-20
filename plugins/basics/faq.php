							<div id="help">
<?php	
	global $table_ref;
	global $current_language;	
	global $rootURL;
	global $page_datas;
	global $currentDatas;
	
	global $mysqli;
		
?>
									<script type="text/javascript">
									<!--
										var CMSRootPath = '<?php echo $rootURL; ?>';
										$(document).ready(function() {
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/faq.css?d=201405071218" type="text/css" />');
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'_office/css/help.css?d=201405071218" type="text/css" />');												
										});
										
										var current_subject = null;
										function openSubject(elem) {
								
											if (current_subject!=null) {
																							
												// CLOSE OLD //						
												$(current_subject).next('.messages').animate({'height':'0px', 'overflow':'hidden', 'opacity':'0.0'}, 500, 'swing', function() {  });
												
												$(current_subject).removeClass('opened');
												$(current_subject).addClass('closed');
														
											}
										
											if (elem!=current_subject) {
												
												// OPEN NEW //
												current_subject = elem;
												$(current_subject).removeClass('closed');
												$(current_subject).addClass('opened');
																								
												// Retrieve height //												
												var messages_block = $(current_subject).next('.messages');	
												
												messages_block.css({'display':'block', 'overflow':'hidden', 'height':'auto'});
												var temp_height = messages_block.height();						
												messages_block.css({'height':'1px'});	
												
												messages_block.animate({'height':temp_height+'px', 'opacity':'1.0'}, 750);		
																								
											} else {
												// SET NULL SUBJECT //
												current_subject = null;												
											}
										}										
										
									-->
									</script>
<?php
		$category = '';
		$rq = "SELECT f.*, c.category FROM $table_ref".$current_language."_back_faq AS f, $table_ref".$current_language."_back_faq_categories AS c WHERE f.id_category=c.id AND c.visible=true AND f.visible=1 AND f.title<>'' ORDER BY c.position ASC, c.category ASC, f.position ASC, f.creation_date DESC;";
		if (isset($_GET['s'])) {
			$rq = str_replace('WHERE', "WHERE c.category='".urldecode($_GET['s'])."' AND", $rq);
		}
		if (isset($_POST['search'])) {
			$search = $_POST['search'];
			$search = str_replace("'", " ", $search);
			$search = str_replace("-", " ", $search);
			$search = str_replace("_", " ", $search);
			$search = str_replace("/", " ", $search);
			$keywords = explode(' ', $search);
			$search_str = "f.content LIKE '%".$_POST['search']."%'";
			for($k=0;$k<count($keywords);$k++) {
				if (strlen($keywords[$k])>2) {
					$search_str .= " OR f.content LIKE '%".$keywords[$k]."%'";
					$search_str .= " OR f.keywords LIKE '%".$keywords[$k]."%'";
				}
			}
			$rq = str_replace('WHERE', "WHERE (".$search_str.") AND", $rq);			
		}
		
		$result_datas = sqlToArray($rq);
		
		for($c=0;$c<$result_datas['nb'];$c++) {
			$content = $result_datas['datas'][$c];
			if (!empty($content['image'])) {
				$rq_img = "SELECT extension FROM $table_ref".$current_language."_cms_images WHERE name='".$content['image']."';";
				$result_img = mysqli_query($mysqli, $rq_img);
				$content_img = mysqli_fetch_array($result_img);
			}
			
			if ($category!=$content['category']) {
				$category=$content['category'];
?>
									<h4><?php echo styleToHTML($content['category']); ?></h4>
<?php
			}			
?>
									<div class="title closed" id="id_<?php echo $content['id']; ?>" onclick="openSubject(this);">
										<div class="type">Q</div>
										<div class="subject_txt">
											<?php echo styleToHTML($content['title']); ?>				
										</div>
									</div>
									<div class="messages">
										<div class="message_box">
											<div class="title">
												<div class="type">R</div>
											</div>										
											<div class="message_txt">
												<?php echo styleToHTML($content['content']); ?>	
											</div>											
										</div>
										<div class="clear"> &nbsp;</div>
									</div>
									
<?php		
		} // END WHILE RESULT	
?>
	
						</div><!-- END OF FAQ -->
