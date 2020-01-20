<?php
class eFile {
		
	public static function upload_file($fileDatas, $params=array()) {

		$error = false;
				
		if (!isset($params['filename']) || empty($params['filename'])) {
			$params['filename'] = $fileDatas['name'];
		}
	
		if (!isset($params['max_size'])) {
			$params['max_size']=16*1024*1024;
		}
		if (!isset($params['forbidden_types'])) {
			$params['forbidden_types'] = array('exe', 'bat');
		}
		if (!isset($params['folder'])) {
			$params['folder'] = 'files/';
			
			if (!isset($params['id'])) {
				$params['id'] = '';
			} else {
				$params['id'] .= '/';
			}
			
			$params['folder'] .= $params['id'];
		}
		
		if (!isset($params['replace'])) {
			$params['replace'] = false;
		}
				
		if ($error==false) {
			if ($fileDatas['size'] > $params['max_size']) {
				$error = 'size';
			}
		}
		
		if ($error==false) {
			$last_dot = strrpos($fileDatas['name'], '.');
			$ext = substr($fileDatas['name'], $last_dot);
			for ($i=0;$i<count($params['forbidden_types']);$i++) {
				if (strpos($fileDatas['type'], $params['forbidden_types'][$i])!==false) {
					$error = 'type';
					break;
				}
				if (strpos($ext, $params['forbidden_types'][$i])!==false) {
					$error = 'type';
					break;
				}
			}
		}
		
		if ($error==false) {	
			$filename = $params['filename'];			
			if (strpos($params['filename'], $ext)===false) {
				$filename = eText::str_to_url($params['filename']).$ext;
			}
						
			$dirname = dirname($params['folder'].$filename);
			if (!is_dir($dirname)) {
				mkdir($dirname, 0755, true);
			}
						
			if ($params['replace']===false) {
				$i=0;
				$name = basename($filename, $ext);
				while (file_exists($params['folder'].$filename)) {
					$i++;
					$filename = $name.'_'.$i.$ext;
				}
			}
			
			if(!move_uploaded_file($fileDatas['tmp_name'], $params['folder'].$filename) ) {
				$error = "upload";
			}
		}
				
		return array('filename'=>$filename, 'error'=>$error, 'filesize'=>$fileDatas['size'], 'filetype'=>$fileDatas['type']);
		
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////
	// FOLDER //
	//////////////////////////////////////////////////////////
	
	public static function create_all_subfolders($path) {
		
		if (substr($path, -1)!='/') { $path.='/'; }
		
		$start_pos = 0;
		
		while(strpos($path, '/', $start_pos)!==false) {
			
			$start_pos = strpos($path, '/', $start_pos);			
			$sub_path = substr($path, 0, $start_pos);	

			//echo $sub_path."<br />\n";			
			
			if (!is_dir($sub_path)) {
				mkdir($sub_path);
			}
			
			$start_pos++;
			
		}	
			
	}
	
	public static function explore_folder($folderPath, $result_type='*') {
	
		$foldersList=array();
		//echo $folderPath;
		$directory = opendir($folderPath);
		while ($this_folder = readdir($directory)) {
			//echo $this_folder;
			if ($this_folder!="." && $this_folder!="..") {
				$ext = strtolower(substr($this_folder, strlen($this_folder)-5, 5));
				
				if (substr_count($ext, ".")<1) {
					$type = 'folder';
				} else {
					$type = 'file';
				}
				//echo $this_folder.' '.$type.' '.$ext;
				if ($result_type =='*' || $result_type==$type) {				
					$foldersList[] = $this_folder;
				}
			}
		}
		
		return $foldersList;
	
	}
	
	public static function is_dir_empty($dir) {
		if (!is_readable($dir)) return NULL;
		$handle = opendir($dir);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	public static function rrmdir($dir) { 
		// DELETE FOLDER AND ALL ITS FILES //
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (is_dir($dir."/".$object))
					rrmdir($dir."/".$object);
					else
					unlink($dir."/".$object); 
				} 
			}
			rmdir($dir); 
		} 
	}
	public static function recurse_copy($src,$dst) { 
		// CLONE FOLDER //
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}
	
	/**
	* public static function zipFile.  Creates a zip file from source to destination
	*
	* @param  string $source Source path for zip
	* @param  string $destination Destination path for zip
	* @param  string|boolean $flag OPTIONAL If true includes the folder also
	* @return boolean
	*/
	public static function zip_file($source, $destination, $flag = '')
	{
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		$source = str_replace('\\', '/', realpath($source));
		if($flag)
		{
			$flag = basename($source) . '/';
			//$zip->addEmptyDir(basename($source) . '/');
		}

		if (is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);
			foreach ($files as $file)
			{
				//echo substr($file, -3, 3).'<br />';
				if (substr($file, -3, 3)!='/..') {
					
					//echo $file.'<br />';
				
					$file = str_replace('\\', '/', realpath($file));
					
					if (is_dir($file) === true)
					{
						$zip->addEmptyDir(str_replace($source . '/', '', $flag.$file . '/'));
					}
					else if (is_file($file) === true)
					{
						$zip->addFromString(str_replace($source . '/', '', $flag.$file), file_get_contents($file));
					}
				}
			}
		}
		else if (is_file($source) === true)
		{
			$zip->addFromString($flag.basename($source), file_get_contents($source));
		}
		
		//die('lala');
		
		return $zip->close();
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////////////////////////////////////
	// CSV //
	/////////////////////////////////////////////////////////////////////////////
	
	
	public static function save_csv($csv_datas, $filepath = "files/export.csv", $delimiter=";") {
	
		$dirname = dirname(substr($filepath, 0, strrpos($filepath, '/')+2));
		if (!is_dir($dirname)) {
			mkdir($dirname, 0755, true);
		}
	
		$f = fopen($filepath, 'w');
		//echo $filepath;
		fputs($f, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		
		if (is_array($csv_datas)) {
			// loop over the input array
			foreach ($csv_datas as $line) { 
				// generate csv lines from the inner arrays			
				fputcsv($f, $line, $delimiter); 
			}
		} else {
			fwrite($f, $csv_datas);
		}
		
		fclose($f);
	}
	
	public static function download_csv($csv_datas, $filename = "export.csv", $delimiter=";") {
		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w'); 
		fputs($f, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		
		if (is_array($csv_datas)) {
			// loop over the input array
			foreach ($csv_datas as $line) { 
				// generate csv lines from the inner arrays			
				fputcsv($f, $line, $delimiter); 
			}
		} else {
			fwrite($f, $csv_datas);
		}
		
		// rewrind the "file" with the csv lines
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachement; filename="'.$filename.'"');
		// make php send the generated csv lines to the browser
		fpassthru($f);
		
	}	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////////////////////////////////////
	// IMG //
	/////////////////////////////////////////////////////////////////////////////
		
	public static function add_suffix($filename, $suffix='_full') {
		return substr_replace($filename, $suffix, strrpos($filename, '.'), 0);
	}
	
	public static function add_image($file_datas, $folder, $name, $extension, $width, $height, $rotation, $resize) {
		
		$root_folder='../images/';
		
		$weight = $file_datas['size'];
		$max_img_weight = 16;
		if ($weight > $max_img_weight*1024*1024) {
			return "Image trop grande (".$max_img_weight." Mo max)";
		}
		
		$type_file = $file_datas['type'];	
		if ( !strstr($type_file, "jpg") && !strstr($type_file, "jpeg") && !strstr($type_file, "bmp") && !strstr($type_file, "gif") && !strstr($type_file, "png") )	{
			return "Le fichier n'est pas une image ".$type_file."<br>";						
		} 				
				
		$tmp_file = $file_datas['tmp_name'];
		
		$tmp_folder = "tmp/";	
		$tmp_filename = $tmp_file;
		$tmp_filename = substr($tmp_filename, strrpos($tmp_filename, '\\'));
		$tmp_filename = substr($tmp_filename, strrpos($tmp_filename, '/'));
					
		// SAVE TO TEMP FOLDER //
		if (file_exists($tmp_file)) {
			if( !move_uploaded_file($tmp_file, $tmp_folder . $tmp_filename ) ) {									
				return "Impossible de copier le fichier dans ".$tmp_folder."<br>";							
			}														
		} 
					
		$size = getimagesize($tmp_folder . $tmp_filename);
		$resample = false;
		$force_height = $size[1];
		$force_width = $size[0];
		
		eFile::create_all_subfolders($root_folder.$folder);
		$full_path = $root_folder.$folder .'/'. $name.'_full'.'.'.$extension;
							
		$dest_size = eFile::fill_empty_size_within_ratio($size, $width, $height);
		$width = $dest_size[0];
		$height = $dest_size[1];
													
		// Fix Orientation										
		if ($rotation == 'auto' && function_exists('exif_read_data') && $extension == 'jpg') {
			$exif = exif_read_data($tmp_folder . $tmp_filename);	
			if (isset($exif['Orientation'])) {
				$orientation = $exif['Orientation'];
				switch($orientation) {
					case 3:
						$rotation = 180;
						break;
					case 6:
						$rotation = -90;
						break;
					case 8:
						$rotation = 90;
						break;
					default :
						$rotation = 0;
				}
			}
		}
		
		if ($rotation!=0) {
			// FORCE RESAMPLE ON ROTATED //
			$resample = true;									
			if (abs($rotation)==90) {
				$former_width = $width;
				$width = $height;
				$height = $former_width;
			}
		}
		
		if ($weight >= $max_img_weight*0.25*1024*1024) {
			// FORCE RESAMPLE ON BIG FILES //
			$resample = true;
			$force_height = $force_height*0.5;
			$force_width = $force_width*0.5;
			
			$small_ratio = $width/$height;
			if ($width>$force_width) {
				$width = $force_width;
				$height = $force_width / $small_ratio;
			} else if ($height>$force_height) {
				$height = $force_height;
				$width = $force_height * $small_ratio;
			}
		}
		
		if ($resample) {
			// RESAMPLE FULL IMAGE //
			$src_path = $tmp_folder . $tmp_filename;
			$dest_path = $full_path;
			$http_file = $file_datas;
			eFile::resize_image($src_path, $dest_path, $force_width, $force_height, 'deform', $size, $http_file, $rotation);
			
		} else {
			copy($tmp_folder . $tmp_filename, $full_path);
		}
				
		// CREATE VISIBLE IMAGE //								
		$size = getimagesize($full_path);
																									
		if ($height == $size[1] && $width == $size[0]) {
			// MOVE IMAGE //
			copy($full_path, $root_folder.$folder.'/'.$name.".".$extension);									
		} else {
			// RESAMPLE IMAGE //
			$src_path = $full_path;
			$dest_path = $root_folder.$folder.'/'.$name.".".$extension;
			$http_file = $file_datas;
			eFile::resize_image($src_path, $dest_path, $width, $height, $resize, $size, $http_file);																		
		}
		
		return array('width'=>$width, 'height'=>$height);
		
	} // END OF ADD_IMAGE
	
	public static function resize_image($src_path, $dest_path, $width, $height, $resize = 'crop', $src_size = array(), $http_file = array(), $rotation=0) {
		
		if (!isset($src_size[0])) {
			$file_datas = getimagesize($src_path);
			$src_size = array($file_datas[0], $file_datas[1]);
		}
		$size = $src_size;
		if (!isset($http_file['type'])) {
			if (!isset($file_datas)) { $file_datas = getimagesize($src_path); }
			$http_file['type'] = $file_datas['mime'];
		}
		$type_file = $http_file['type'];
			
		if ( strstr($type_file, "jpg") || strstr($type_file, "jpeg")) {
			$src_img = @imagecreatefromjpeg($src_path);	
			if (!$src_img) { $src_img = @imagecreatefromstring(file_get_contents($src_path)); }				
		} else if (strstr($type_file, "gif")) {
			$src_img = imagecreatefromgif($src_path);								
		} else if (strstr($type_file, "png")) {
			$src_img = imagecreatefrompng($src_path);								
		} else {
			$src_img = imagecreatefromwbmp($src_path);								
		}
												
		$dst_img = imagecreatetruecolor($width,$height);
		if (strstr($type_file, "png")) {
			imagealphablending( $dst_img, false );
			imagesavealpha( $dst_img, true );
		}
		
		$dst_x = 0;
		$dst_y = 0;
		$src_x = 0;
		$src_y = 0;
		$dst_w = $width;
		$dst_h = $height;
		$src_w = $size[0];
		$src_h = $size[1];
		
		$dst_ratio = $width/$height;
		$src_ratio = $size[0]/$size[1];
		
		if ($dst_ratio!=$src_ratio) {
			switch($resize) {
				case 'crop':
					
					if ($dst_ratio>$src_ratio) {
						// DEST WIDTH BASED //
																				
						$src_h = $src_w/$dst_ratio;
						$src_y = ($size[1]-$src_h)/2;
													
					} else {
						// DEST HEIGHT BASED //
						$src_w = $src_h*$dst_ratio;
						$src_x = ($size[0]-$src_w)/2;
					}
					
					//echo $src_x.' '.$src_y.' '.$src_h.' '.$src_w;
					
					break;
				
				case 'full':
					if ($dst_ratio>$src_ratio) {
						// DEST HEIGHT BASED //
						
						$dst_w = $dst_h*$src_ratio;
						$dst_x = ($width-$dst_w)/2;
						
					} else {
						// DEST WIDTH BASED //
						$dst_h = $dst_w/$src_ratio;
						$dst_y = ($height-$dst_h)/2;
					}
					break;
					
				case 'deform':
				default:
					// KEEP DEFAULT //
					break;
			}
		}
		imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h); 
					
		if ($rotation!=0) {
			$dst_img = imagerotate($dst_img, $rotation, 0);
		}
		
		// Output
		if ( strstr($type_file, "jpg") || strstr($type_file, "jpeg")) {
			imagejpeg($dst_img, $dest_path, 100);								
		} else if (strstr($type_file, "gif")) {
			imagegif($dst_img, $dest_path);
		} else if (strstr($type_file, "png")) {								
			imagepng($dst_img, $dest_path);
		} else {
			imagewbmp($dst_img, $dest_path);
		}
	}
	public static function fill_empty_size_within_ratio($src_size, $dest_width, $dest_height) {
		$ratio = $src_size[0]/$src_size[1];
		
		if ($dest_width=='' && $dest_height=='') {
			$dest_width = $src_size[0];
			$dest_height = $src_size[1];
		} else {
			if ($dest_width=='') {
				$dest_width = $dest_height * $ratio;
			}
			if ($dest_height=='') {
				$dest_height = $dest_width / $ratio;
			}
		}		
		
		$dest_size = array(round($dest_width), round($dest_height));
		
		return $dest_size;
	}
		
	public static function get_size_within_destination($src_size, $dest_width, $dest_height) {
		
		$ratio = $src_size[0]/$src_size[1];			
		$dest_ratio = $dest_width / $dest_height;
			
		if ($dest_ratio>$ratio) {
			// dest width is larger > base width on height //
			$dest_width = $dest_height * $ratio;
		} else {
			// dest height is greater > base height on width //
			$dest_height = $dest_width / $ratio;
		}
					
		
		$dest_size = array(round($dest_width), round($dest_height));
		
		return $dest_size;
		
	}
	
	public static function get_extension($type_file) {
		
		$extension = '';
		
		if ( strstr($type_file, "jpg") || strstr($type_file, "jpeg")) {				
			$extension = "jpg";
		} else if (strstr($type_file, "gif")) {				
			$extension = "gif";
		} else if (strstr($type_file, "png")) {				
			$extension = "png";
		} else if (strstr($type_file, "bmp")) {				
			$extension = "bmp";
		} else {
			$extension = substr($image, strrpos($image, '.')+1);
		}
		
		return $extension;
	}
	
}
?>