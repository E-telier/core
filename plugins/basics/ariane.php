<?php

	global $page_datas;
	global $table_prefix;
	
	// GET SECTION //
	if ($page_datas['childof']!=$page_datas['reference']) {
		$rq = "SELECT menu_name, childof FROM ".$table_prefix."_cms_pages WHERE reference='".$page_datas['childof']."' LIMIT 1;";		
		$result_datas = sqlToArray($rq);					
		$content = $result_datas['datas'][0];
		
		$parent_name = $content['menu_name'];
		$parent_ref = $page_datas['childof'];
		
		if ($content['childof']!=$page_datas['childof']) {
			$rq = "SELECT reference, menu_name FROM ".$table_prefix."_cms_pages WHERE reference='".$content['childof']."' AND reference=childof LIMIT 1;";			
			$result_datas = sqlToArray($rq);
			$content = $result_datas['datas'][0];
			$great_parent_name = $content['menu_name'];
			$great_parent_ref = $content['reference'];
		}
		
	}
	
?>
	<div id="ariane">
		<?php if (isset($great_parent_ref)) { ?><a href="<?php echo curFolderURL().$great_parent_ref; ?>"><?php echo iso_strtoupper_fr($great_parent_name); ?></a> &gt; <?php } // END IF GREAT PARENT ?>
		<?php if (isset($parent_ref)) { ?><a href="<?php echo curFolderURL().$parent_ref; ?>"><?php echo iso_strtoupper_fr($parent_name); ?></a> &gt; <?php } // END IF PARENT ?>
		<span class="myself"><?php echo iso_strtoupper_fr($page_datas['menu_name']); ?></span>
	</div>