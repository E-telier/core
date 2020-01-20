<?php
	if (isset($folder_value)) {
		if (!isset($current_folder)) {
			$current_folder = $result_datas['datas'][0][$folder_value];
		}
		
		if (!empty($current_folder)) {
?>
	<h2><?php echo eText::iso_htmlentities($current_folder); ?></h2>
<?php
		}
	}
?>

	<table cellspacing="0" cellpadding="5" border="0" class="table_result">
		<tr>
			<td class="row_num"> </td> 			
<?php
	
	$nb_cols = count($cols_names);

	for($c=0;$c<$nb_cols;$c++) {
	
		$img_sort = '';
		$this_sort = 'ASC';
		if (isset($_GET['orderby'])) {		
			if ($cols_names[$c]==$_GET['orderby']) {	
			
				if ($sort=='ASC') { 
					$this_sort = 'DESC';
					$img_sort = '&#x25B2;';
				} else { 
					$this_sort = 'ASC'; 
					$img_sort = '&#x25BC;';
				}				
			}
		}
	
		echo '
			<td><a href="'.eMain::cur_page_url(true).'?p='.$page.'&orderby='.$cols_names[$c].'&sort='.$this_sort.'">'.$cols_names[$c].'</a> '.$img_sort.'</td>
		';
	}
?>
			<td class="action_btn">EDIT</td>
<?php
		if (!empty($action_str)) {
?>	
			<td class="action_btn">ACTION</td>
<?php
		} // END IF ACTION
?>
		</tr> 