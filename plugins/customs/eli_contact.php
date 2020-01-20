		<div class="block">		
			<form method="post" id="estimate" action="">
				
				<?php
									
					include('plugins/basics/capcha/eCapcha.php');
					$capcha = new eCapcha();
												
					if (isset($_POST['contact_firstname'])) {
										
						if (empty($_POST['email']) && empty($_POST['email2'])) { // ROBOT CHECK //						
						
							// SPAM CHECK //
							$forbidden = array();
							$forbidden[] = '/[\x{0400}-\x{04FF}]/imu'; // Cyrillic
							$forbidden[] = '/[\x{4e00}-\x{9fff}]/imu'; // Chinese
							$forbidden[] = '/[\x{0600}-۾]/imu'; // Arabic
							$forbidden[] = '/\.ru\//imu'; // Russian link
							$forbidden[] = '/penis/imu'; // Spam
							$forbidden[] = '/income/imu'; // Spam
							$forbidden[] = '/ sex /imu'; // Spam
							$forbidden[] = '/bit\.ly/imu'; // Spam
							$forbidden[] = '/\$/imu'; // Spam
							$forbidden[] = '/prostate/imu'; // Spam
							
							$text = $_POST['contact_txt'];
							
							for ($i=0;$i<count($forbidden);$i++) {		
								if (preg_match($forbidden[$i], $text)===1) {
									die('language not supported');
									return 0;
								}
							}
						
							$captcha_success = false;
							if (!isset($_POST['capcha']) || (!empty($_POST['capcha']) && $_POST['soluce_capcha']==$capcha->check_capcha($_POST['capcha']))) {
								$captcha_success = true;
							}
							
							if ($captcha_success==false) {
?>					
				<div class="error">
					<?php eLang::show_translate('sorry, the capcha is incorrect... please try again.', 'ucfirst'); ?>
				</div>						
<?php
							} else {
														
								switch($_POST['type']) {
									case "site": 
										$type = eLang::translate('a website', 'ucfirst');
										break;
									case "app":
										$type = eLang::translate('an app', 'ucfirst');
										break;
									default:
										$type = eLang::translate('other type of project', 'ucfirst');
										break;
								}
								switch($_POST['languages']) {
									case "1": 
										$lang = eLang::translate('unilingual', 'ucfirst');
										break;
									case "2":
										$lang = eLang::translate('from 2 to 5 languages', 'ucfirst');
										break;
									default:
										$lang = eLang::translate('more than 5 languages', 'ucfirst');
										break;
								}
								switch($_POST['talents']) {
									case "all": 
										$talents = eLang::translate('programming only', 'ucfirst');
										break;
									case "prog":
										$talents = eLang::translate('design and programming', 'ucfirst');
										break;
									case "design":
										$talents = eLang::translate('design only', 'ucfirst');
										break;
								}
																
								$selected_tech = "";
								$techs = array('responsive', 'javascript', 'php', 'cms', 'ecom', 'flash', '3D');
								for ($i=0;$i<count($techs);$i++) {
									if (isset($_POST[$techs[$i]])) { 
										if ($selected_tech!="") { $selected_tech.=", "; }
										$selected_tech.=$techs[$i]; 										
									}									
								}
																
								$values = array(
									'type'=>$type,
									'delivery'=>$_POST['delivery'],
									'lang'=>$lang,
									'talents'=>$talents,
									'selected_tech'=>$selected_tech,
									'contact_txt'=>$_POST['contact_txt'],
									'contact_firstname'=>$_POST['contact_firstname'],
									'contact_lastname'=>$_POST['contact_lastname'],
									'contact_mail'=>$_POST['contact_mail'],
									'contact_phone'=>$_POST['contact_phone'],
									'root_url'=>eMain::root_url()
								);
							
								$destinataires = array("contact@e-telier.be", $_POST['contact_mail']);
							
								$OK = eMail::send_auto_mail('estimate', $values, eParams::$sender_email, $destinataires);
								
								if ($OK==true) { 
					?>
					<div class="success"><?php eLang::show_translate('your request has been sent and will be processed as soon as possible!'); ?></div>
					<?php
									
								}
							}
						}
					}
				?>
				
				<script type="text/javascript" src="<?php echo eMain::root_url(); ?>plugins/customs/eli_contact.js?d=201911281721"></script>			
			
				<h1><?php eLang::show_translate('contact and estimate'); ?></h1>
				<br />
				<div class="cols">
					<div class="width50">
						<h2><?php eLang::show_translate('step'); ?> 1 : <br /><?php eLang::show_translate('about yourself'); ?></h2>
						<br />
						<div class="input">
							<label for="contact_firstname"><?php eLang::show_translate('firstname', 'ucfirst'); ?>*</label><!--
							--><input type="text" name="contact_firstname" id="contact_firstname" value="" class="needed" />
						</div><!--
						--><div class="input">
							<label for="contact_lastname"><?php eLang::show_translate('lastname', 'ucfirst'); ?>*</label><!--
							--><input type="text" name="contact_lastname" id="contact_lastname" value="" class="needed" />
						</div><!--
						--><div class="input">
							<label for="contact_mail"><?php eLang::show_translate('email', 'ucfirst'); ?>*</label><!--
							--><input type="text" name="contact_mail" id="contact_mail" value="" class="needed email" />
						</div><!--
						--><div class="input">
							<label for="contact_phone"><?php eLang::show_translate('phone', 'ucfirst'); ?></label><!--
							--><input type="text" name="contact_phone" id="contact_phone" value="" />
						</div><!--
						--><div class="input">
							<label for="contact_txt"><?php eLang::show_translate('message', 'ucfirst'); ?>*</label><!--
							--><textarea name="contact_txt" id="contact_txt" rows="16" cols="60" class="needed"></textarea>
						</div>
						
						<input type="hidden" value="" id="email" name="email" />			
																	
					</div><!-- END OF WIDTH 50 --><!--
						
				--><div class="width50">									
					<h2><?php eLang::show_translate('step'); ?> 2 : (<?php eLang::show_translate('optional'); ?>)<br /><?php eLang::show_translate('info about your project'); ?></h2>
					
					<div id="jauge">
						<img src="<?php echo eMain::root_url(); ?>design/jauge_2.jpg" width="100%" height="auto" alt="jauge" />
						<div id="cursor" style="margin-top:-12px;">
							<img src="<?php echo eMain::root_url(); ?>design/cursor_2.png" width="22" height="37" alt="curseur" />
							<span id="cost"><?php eLang::show_translate('starting at € 0'); ?> *</span>
						</div>
						
					</div>				
					
					<div class="input">
						<label for="contact_firstname"><?php eLang::show_translate('project type', 'ucfirst'); ?></label><!--
							--><select name="type" onchange="updateCost();">
							<option value="site"><?php eLang::show_translate('a website'); ?></option>
							<option value="app"><?php eLang::show_translate('an app'); ?></option>
							<option value="other type"><?php eLang::show_translate('other'); ?></option>										
						</select>
					</div><!--
					--><div class="input">
						<label for="delivery"><?php eLang::show_translate('delivery', 'ucfirst'); ?></label><!--
						--><div class="choices">
							<input type="radio" class="radio_checkbox" name="delivery" value="normal" id="normal" checked="checked" onchange="updateCost();" /><label for="normal" class="checklabel"><?php eLang::show_translate('normal'); ?></label>
							<input type="radio" class="radio_checkbox" name="delivery" value="urgent" id="urgent" onchange="updateCost();" /><label for="urgent" class="checklabel"><?php eLang::show_translate('urgent'); ?></label>
						</div>
					</div><!--
					--><div class="input">
						<label for="contact_mail"><?php eLang::show_translate('languages', 'ucfirst'); ?></label><!--
						--><select name="languages" onchange="updateCost();">
							<option value="1"><?php eLang::show_translate('unilingual', 'ucfirst'); ?></option>
							<option value="2"><?php eLang::show_translate('from 2 to 5 languages', 'ucfirst'); ?></option>	
							<option value="6"><?php eLang::show_translate('more than 5 languages', 'ucfirst'); ?></option>												
						</select>
					</div><!--
					--><div class="input">
						<label for="contact_phone"><?php eLang::show_translate('skills', 'ucfirst'); ?></label><!--
						--><select name="talents" onchange="updateCost();">											
							<option value="prog"><?php eLang::show_translate('programming only', 'ucfirst'); ?></option>
							<option value="all"><?php eLang::show_translate('design and programming', 'ucfirst'); ?></option>
							<option value="design"><?php eLang::show_translate('design only', 'ucfirst'); ?></option>
						</select>
					</div><!--
					--><div class="input">
						<label for="contact_phone">
							<?php eLang::show_translate('technologies', 'ucfirst'); ?>
						</label><!--
						--><div class="choices">
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="responsive" id="responsive" value="responsive" /><label for="responsive" class="checklabel">Responsive Design</label><br />
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="cms" id="cms" value="cms" /><label for="cms" class="checklabel">CMS</label><br />
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="ecom" id="ecom" value="ecom" /><label for="ecom" class="checklabel">E-commerce</label><br />
							
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="javascript" id="javascript" value="javascript" /><label for="javascript" class="checklabel">Javascript / jQuery</label><br />																														
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="php" id="php" value="php" /><label for="php" class="checklabel">PHP</label><br />
															
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="flash" id="flash" value="flash" /><label for="flash" class="checklabel">Flash / Actionscript</label><br />
							<input class="radio_checkbox" onchange="updateCost();" type="checkbox" name="3D" id="3D" value="3D" /><label for="3D" class="checklabel">3D</label><br />
							
						</div>
					</div>
								
					</div><!-- END OF WIDTH50 -->	
				</div>
				<div class="legal_text">
					* <?php echo eText::style_to_html(
						eLang::translate('estimate_warning')
					); ?>
				</div>
				<br />
				<div class="right">		
<?php 
					$capcha->show_capcha($capcha);
?>
					<br />
					<input type="button" name="button" class="button submit" value="<?php eLang::show_translate('send your request', 'ucfirst'); ?>" onclick="submitForm();" />
				</div>	
			</form>
		</div><!-- END OF BLOCK -->