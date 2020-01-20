<?php

	global $table_prefix;
	global $page_datas;
	global $folderURL;
	
	$parent_ref = $page_datas['childof'];//$menu_datas['reference'];//
	
			
	// If parent is a child, start submenu at parent level //
	$rq_parent = "SELECT childof FROM ".$table_prefix."_cms_pages WHERE childof<>reference AND reference='".$page_datas['childof']."';";
	$result_parent = mysqli_query($mysqli, $rq_parent);
	if (mysqli_num_rows($result_parent)>0) {
		$content_parent = mysqli_fetch_array($result_parent);
		$subsection = $content_parent['childof'];
	} else {
		$subsection = $parent_ref;
	}
	
	// Parent Page //
	$rq_subsection = "SELECT reference FROM ".$table_prefix."_cms_pages WHERE reference='".$subsection."' LIMIT 1;";
	$result_subsection = mysqli_query($mysqli, $rq_subsection);
	$content_subsection = mysqli_fetch_array($result_subsection);	
	/*
?>
										<!-- <li><a href="<?php echo $folderURL.$content_subsection['reference']; ?>"><?php echo htmlentities('Présentation'); ?></a></li>-->
<?php
	*/
	
	
	// Children //
	$rq_children = "SELECT reference, menu_name FROM ".$table_prefix."_cms_pages WHERE childof<>reference AND childof='".$subsection."' AND menu_position>0 ORDER BY menu_position ASC, menu_name ASC;";
	//echo $rq_children;
	$result_children = mysqli_query($mysqli, $rq_children);
	$nb = mysqli_num_rows($result_children);
	//echo $nb;
	if ($nb>0) {		
?>
							<!--<li>-->
								<div class="submenu">
									<ul>	
<?php
	}
	for ($s=0;$s<$nb;$s++) {
		$content_children = mysqli_fetch_array($result_children);
		$class='';
		if (strlen($content_children['menu_name'])>25) { $class.='small'; }
		if ($content_children['reference']==$page_datas['childof'] || $content_children['reference']==$page_datas['reference']) { $class.=' selected'; }		
		
?>
										<li class="<?php echo  $content_children['reference']; ?>"><a href="<?php echo $folderURL.$content_children['reference']; ?>" class="<?php echo $class; ?>"><?php echo iso_htmlentities($content_children['menu_name']); ?></a></li>
<?php

		$rq_sub = "SELECT reference, menu_name FROM ".$table_prefix."_cms_pages WHERE childof<>reference AND childof='".$content_children['reference']."' AND menu_position>0 ORDER BY menu_position ASC, menu_name ASC;";
		$result_sub = mysqli_query($mysqli, $rq_sub);
		$nb_sub = mysqli_num_rows($result_sub);
		if ($nb_sub>0) {
?>
										<li>
											<ul>
<?php
			for ($ss=0;$ss<$nb_sub;$ss++) {
				$content_sub = mysqli_fetch_array($result_sub);		
?>
												<li><a href="<?php echo $folderURL.$content_sub['reference']; ?>" <?php if (strlen($content_children['menu_name'])>25) { ?>class="small"<?php } ?>><?php echo htmlentities($content_sub['menu_name']); ?></a></li>
<?php
			}// END OF FOR LEVEL 2
?>
											</ul>
										</li>
<?php	
		} // END OF IF NB_SUB >0
	} // END OF FOR LEVEL 1

	if ($nb>0) {
?>
									</ul>
								</div><!-- END OF SUBMENU -->								
							<!--</li>-->
<?php
	}
	
