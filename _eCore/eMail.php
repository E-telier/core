<?php
include(realpath(dirname(__FILE__)).'/../libs/PHPMailer-master/PHPMailerAutoload.php');
Class eMail {
		
	public static function inline_css($html) {
			
		$style = preg_replace('/.*<style type="text\/css">(.*)<\/style>.*/ sim', '$1', $html);
		
		$classes = array();
		
		while(strpos($style, '}')!==false) {
			
			$class_name = trim(substr($style, 0, strpos($style, '{')));
			$class_style = substr($style, strpos($style, '{')+1);
			$class_style = substr($class_style, 0, strpos($class_style, '}'));
			$class_style = str_replace("\n", "", $class_style);
			$class_style = str_replace("\r", "", $class_style);
			$class_style = str_replace("	", "", $class_style);
			
			$classes[$class_name] = $class_style;
			
			$style = substr($style, strpos($style, '}')+1);
		}
				
		$class_names = array_keys($classes);
				
		$nb_class = count($class_names);
				
		for ($i=0;$i<$nb_class;$i++) {
			
			$class_name = $class_names[$i];
			$class_style = $classes[$class_name];
		
			if (strpos($class_name, '.')!==false) {
				$search = 'class="'.str_replace('.', '', $class_name).'"';
			} else if (strpos($class_name, '#')!==false) {
				$search = 'id="'.str_replace('#', '', $class_name).'"';
			} else {
				$search = '<'.$class_name;
			}
			
			$new_html = "";
			$old_html = $html;
			
			// add class style //
			while(strpos($old_html, $search)!==false) {
				
				$search_pos = strpos($old_html, $search);
				
				$new_html = $new_html.substr($old_html, 0, $search_pos);
				$old_html = substr($old_html, $search_pos+strlen($search));
				
				// add existing style to class style //
				$old_style = "";
				if (strpos($old_html, "style='")!==false) {
					if (strpos($old_html, "style='")<strpos($old_html, '>')) {						
						$old_style_start = strpos($old_html, "style='") + strlen("style='");
						$old_style_length   = strpos($old_html, "'", $old_style_start) - $old_style_start;
						$old_style = substr($old_html, $old_style_start, $old_style_length);
					}
				}
				
				$new_html = $new_html.$search.' style="'.$class_style.$old_style.'"';	
								
			}
			$html = $new_html.$old_html;
						
		}
		
		return $html;
		
	}
	
	public static function getSSLPage($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSLVERSION,3); 
		
		//echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		$result = curl_exec($ch);
		
		//print curl_error($ch);
		
		curl_close($ch);
		return $result;
	}
	
	public static function populate_email($type, $content) {
		
		$lang = $_SESSION['lang'];
		//$lang = 'fr'; // TEMP before translations
		
		$subject_format = eMain::$sql->sql_to_array("SELECT ".$lang." FROM ".eParams::$prefix."_dictionnary WHERE reference='mail_".$type."_subject' LIMIT 1;");
		$message_format = eMain::$sql->sql_to_array("SELECT ".$lang." FROM ".eParams::$prefix."_dictionnary WHERE reference='mail_".$type."_message' LIMIT 1;");
		
		$mail_string = $message_format['datas'][0][$lang];		
		while(strpos($mail_string, '{')!==false) {
			
			$before_pos = strpos($mail_string, '{');
			$after_pos = strpos($mail_string, '}');
			
			$request = trim(substr($mail_string, $before_pos+1, $after_pos-$before_pos-1));			
			$result = $content[$request];
			
			$mail_string = substr($mail_string, 0, $before_pos).$result.substr($mail_string, $after_pos+1);
			
		}
		
		$mail_title = $subject_format['datas'][0][$lang];		
		while(strpos($mail_title, '{')!==false) {
			
			$before_pos = strpos($mail_title, '{');
			$after_pos = strpos($mail_title, '}');
			
			$request = trim(substr($mail_title, $before_pos+1, $after_pos-$before_pos-1));			
			$result = $content[$request];
			
			$mail_title = substr($mail_title, 0, $before_pos).$result.substr($mail_title, $after_pos+1);
			
		}
		
		return array('subject'=>$mail_title, 'content'=>$mail_string);
	}
	
	public static function send_auto_mail($type, $content, $sender = '', $destinataires = array()) {
		
		$populated = self::populate_email($type, $content);
		
		if ($sender=='') {
			$sender = eParams::$admin_sender;
		}
		
		if (count($destinataires)==0) {
			$destinataires[] = $sender;
			$destinataires[] = "contact@e-telier.be"; // ADD WEBMASTER
		}
				
		if (self::send_mail($sender, $populated['subject'], $populated['content'], $destinataires)==0) {
			return true;
		}
		
		return false;
		
	}
	
	public static function send_mail($sender, $titre, $content, $destinataires, $params=array() /*$replyto='', $html_template=false, $show_result = false*/) {
		
		if ($sender==eParams::$admin_sender) {
			$sender_smtp = array(
				'email'=> $sender,
				'username'=> $sender,
				'password'=> eParams::$admin_smtp['password'],
				'host'=> eParams::$admin_smtp['host'],
				'port'=> eParams::$admin_smtp['port'],
				'encryption'=> eParams::$admin_smtp['encryption']
			);
		}
		if (is_array($sender)) {
			$sender_smtp = $sender;
			$sender = $sender_smtp['email'];
		}
				
		//$Mail_server = 'smtp.e-telier.be';
		//ini_set('SMTP',$Mail_server);
										
		//create a boundary for the email. This 
		//$boundary = uniqid('np');
						
		//headers - specify your from email address and name here
		//and specify the boundary for the email
		//$headers = "MIME-Version: 1.0\r\n";
		//$headers .= "From: ".$sender."\r\n";
		if (isset($params['replyto'])) {
			//$headers .= "Reply-To: ".$replyto."\r\n";
			$replyto = $params['replyto'];
		} else {
			$replyto = $sender;
		}
		//$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

		$content = stripslashes($content);
		$plain_text = eText::no_html(eText::no_style($content), false);
		
		//here is the content body
		//$message = $plain_text;
		//$message .= "\r\n\r\n--" . $boundary . "\r\n";
		//$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

		//Plain text body
		//$message .= $plain_text;
		//$message .= "\r\n\r\n--" . $boundary . "\r\n";
		//$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

		//Html body
		
		if (strpos($content, '<html')===false) {
		
			if (isset($params['html_template']) && $params['html_template']==true) {
				$url = eMain::root_url()."cms/plugins/newsletter/preview.php";
				$url = str_replace(':443', '', $url); // FORCED NO HTTPS LOCAL
				$url = str_replace('https://', 'http://', $url); // FORCED NO HTTPS LOCAL
				
				$html = file($url);	
								
				$html = implode('', $html);	
				$html = preg_replace('/.*<body>(.*)<\/body>.*/ sim', '$1', $html);
				
				$html = inline_css($html);
									
				$html = str_replace('<!-- START OF CONTENT -->', '<!-- START OF CONTENT -->'.eText::style_to_html($content), $html);
			} else {
				$html = eText::style_to_html($content);
			}
			
			$html = "
			 <html>
				<body>
					".$html."
				</body>
			</html>";
			
		} else {
			$html = $content;
		}	

		//$message .= $html;
		
		//$message .= "\r\n\r\n--" . $boundary . "--";
						
		$nb = count($destinataires);	
		$OK = 0;
		for($i=0;$i<$nb;$i++) {
			$destinataire = $destinataires[$i];	
			if (!empty($destinataire)) {
			
				//$dest_headers = $headers;
				$from = $sender;
				if ($sender=="self") {
					//$dest_headers = str_replace('From: '.$sender, 'From: '.$destinataire, $dest_headers);
					$from = $destinataire;
				}
				//$dest_headers = str_replace('To: TO', 'To: '.$destinataire, $dest_headers);
				//die($dest_headers);
				
				//$html = str_replace('{USER_EMAIL}', $destinataire, $html);
				$dest_message = str_replace('{USER_EMAIL}', $destinataire, $html);	
				
				//Create a new PHPMailer instance
				$mail = new PHPMailer;
				
				if (isset($sender_smtp)) {				
					//Tell PHPMailer to use SMTP
					//$mail->isSMTP();
					//Enable SMTP debugging
					// 0 = off (for production use)
					// 1 = client messages
					// 2 = client and server messages
					$mail->SMTPDebug = 0;
					//Ask for HTML-friendly debug output
					$mail->Debugoutput = 'html';
					//Set the hostname of the mail server
					$mail->Host = $sender_smtp['host'];
					//Set the SMTP port number - likely to be 25, 465 or 587
					$mail->Port = $sender_smtp['port'];
					//Set the encryption system to use - ssl (deprecated) or tls
					$mail->SMTPSecure = $sender_smtp['encryption'];
					//Whether to use SMTP authentication
					$mail->SMTPAuth = true;
					//Username to use for SMTP authentication
					$mail->Username = $sender_smtp['username'];
					//Password to use for SMTP authentication
					$mail->Password = $sender_smtp['password'];
				} else {
					// Set PHPMailer to use the sendmail transport /* Warning : comment this to avoid error */
					//$mail->isSendmail();
				}
				//Set who the message is to be sent from
				$mail->setFrom($from);
				//Set an alternative reply-to address
				$mail->addReplyTo($replyto);
				//Set who the message is to be sent to
				$mail->addAddress($destinataire);
				//Char encode
				$mail->CharSet = 'UTF-8';
				//Set the subject line
				$mail->Subject = $titre;
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($dest_message);
				//Replace the plain text body with one created manually
				$mail->AltBody = $plain_text;
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');

				//send the message, check for errors
				
				if (!$mail->send()) {
					echo "Mailer Error: " . $mail->ErrorInfo;
					
					// FALLBACK PHP MAIL //			

					//create a boundary for the email. This 
					$boundary = uniqid('np');
					
					// header
					$headers = "MIME-Version: 1.0\r\n";
					$headers .= "From: ".$sender."\r\n";
					if (isset($params['replyto'])) {
						$headers .= "Reply-To: ".$params['replyto']."\r\n";
					}
					$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
					
					$dest_headers = $headers;
					if ($sender=="self") {
						$dest_headers = str_replace('From: '.$sender, 'From: '.$destinataire, $dest_headers);
					}
					$dest_headers = str_replace('To: TO', 'To: '.$destinataire, $dest_headers);	

					//here is the content body
					$message = $plain_text;
					$message .= "\r\n\r\n--" . $boundary . "\r\n";
					$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
							
					//Plain text body
					$message .= $plain_text;
					$message .= "\r\n\r\n--" . $boundary . "\r\n";
					$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
					
					// HTML //
					$message .= $html;
					$message .= "\r\n\r\n--" . $boundary . "--";
					
					$dest_message = str_replace('{USER_EMAIL}', $destinataire, $message);	
					
					if (!mail($destinataire, '=?utf-8?B?'.base64_encode($titre).'?=', $dest_message, $dest_headers)) { $OK--; } 
					else { $OK++; /*echo "\nOK".$destinataire;*/ }
					
					$OK--;
				} else {
					$OK++;
					if (isset($params['showresult']) && $params['showresult']==true) {
						echo "Envoyé à -".$destinataire."-<br />"; 
					}
				}
				
				//if (!mail($destinataire, '=?utf-8?B?'.base64_encode($titre).'?=', $dest_message, $dest_headers)) { $OK--; } 
				//else { $OK++; /*echo "\nOK".$destinataire;*/ }
			} else {
				$OK++;
			}
		}
						
		return ($OK-$nb);
		
	}
}
?>