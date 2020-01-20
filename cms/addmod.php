<?php $separator= "{ itemDelimiter }"; ?>
			<script type="text/javascript">
				<!--								
				function asyncFormSubmitted(tResult) {
					
					console.log(tResult);
										
					// PARSE RESULTS //
					var tLangValues = new Object();					
					tResult = tResult.split('\n');
					for (var i=0;i<tResult.length;i++) {						
						var tValues = tResult[i].split('<?php echo $separator; ?>');
						var tLangValue = new Object();
						var tLang='';
						for (var j=0;j<tValues.length;j++) {
							tValue = tValues[j].split('=');
							tLangValue[tValue[0]] = tValue[1];
							
							if (tValue[0]=='lang') {
								tLang = tValue[1];
							}
							
						}
						
						tLangValues[tLang] = tLangValue;
					}
					
					// APPLY RESULT //
					$('select[id$=_addimage]').each(function() {
						var tName = $(this).attr('id');
						var tLang = tName.substring(0, 2);						
						var tWysiwygName = tName.replace('_addimage', '');
						
						// UPDATE WYSIWYG DATAS //
						tImgList = new Array();
						tImgList['filename'] = tLangValues[tLang]['name']+'.'+tLangValues[tLang]['extension'];
						tImgList['width'] = tLangValues[tLang]['width'];
						tImgList['height'] = tLangValues[tLang]['height'];
						tImgList['description'] = tLangValues[tLang]['description'];
						tImgList['align'] = tLangValues[tLang]['align'];
						
						//pWysiwyg[tWysiwygName].pImgList.push(1, 0, tImgList);
						pWysiwyg[tWysiwygName].pImgList[tLangValues[tLang]['name']] = tImgList;
						
						//$(this).children('option:eq(0)').after('<option value="'+tLangValues[tLang]['name']+'" class="img_option" style="background-image:url(../images/'+tImgList['filename']+');">'+tLangValues[tLang]['name']+'</option>');
					
					});
					
					$('select.img_select').each(function() {
						var tLang = ($(this).attr('id')).split('_')[0];
						//alert(tLang+' '+$(this).attr('id'));
						var tName = tLangValues[tLang]['name'];
						$(this).children('option:eq(0)').after('<option value="'+tName+'" class="img_option" style="background-image:url(../images/'+tName+'.'+tLangValues[tLang]['extension']+');">'+tName+'</option>');
					
					});
					
					// RESET CUSTOM SELECT //
					$('.my_select').remove();
					mySelectsManager = new CustomSelectsManager();
										
					// CLOSE POPUP //
					ePopup.closePopup($('.popup form'));
					
				}
				
				$(document).ready(function() {
					// SLIDERS //
					$('.slider').slider({
						step:1,
						max:10,
						min:0,
									
						slide: function(event, ui) {
							$(this).find('input').val(ui.value);
							$(this).find('.ui-slider-handle').html(ui.value);
						}
					}).each(function() {
						var tValue = $(this).find('input').val();
						$(this).find('.ui-slider-handle').html(tValue);
						
						$(this).slider('value', $(this).find('input').val());
					});
				});
				
				-->
			</script>

<?php

	
			
	if ($page=="pages") {	
		include('_form_pages.php');
	} else if ($page=="blocks") {
		include('_form_blocks.php');		
	
	} else if ($page=="users") {
		// USERS //
		$table = eParams::$prefix."_users";
			
		// Traitement form //
		
		if (isset($_POST['login'])) {
			
			$error = false;
			
			if (!isset($_POST['domain'])) { $_POST['domain'] = '*admin*'; }
							
			$_POST['access_level'] = intval($_POST['access_level']);
			$access_level = eUser::getInstance()->get_datas('access_level');
			if ($access_level['access_level']>$_POST['access_level']) { $error = eLang::translate('incorrect access level'); }
			
			$password = eMain::encrypt($_POST['password']);
			if ($addmod!=0 && $_POST['password']==$_POST['old_password']) {
				$password['encrypted'] = $_POST['old_password'];
				$password['coef'] = $_POST['password_coef'];
			} else if ($_POST['password']!=$_POST['password_conf']) { 
				$error = eLang::translate("incorrect Password Confirmation"); 
			}
			
			if (!$error) {
				if ($addmod==0) {
					$rq = "INSERT INTO $table (firstname, lastname, email, login, password, password_coef, access_level, domain) 
					VALUES ('".eMain::$sql->protect_sql($_POST['firstname'])."', '".eMain::$sql->protect_sql($_POST['lastname'])."', '".eMain::$sql->protect_sql($_POST['email'])."', '".eMain::$sql->protect_sql($_POST['login'])."', '".eMain::$sql->protect_sql($password['encrypted'])."', '".eMain::$sql->protect_sql($password['coef'])."', '".eMain::$sql->protect_sql($_POST['access_level'])."', '".eMain::$sql->protect_sql($_POST['domain'])."')";
				
				} else {
					$rq = "UPDATE $table SET 
					firstname='".eMain::$sql->protect_sql($_POST['firstname'])."', lastname='".eMain::$sql->protect_sql($_POST['lastname'])."', email='".eMain::$sql->protect_sql($_POST['email'])."', login='".eMain::$sql->protect_sql($_POST['login'])."', 
					password='".eMain::$sql->protect_sql($password['encrypted'])."', password_coef='".eMain::$sql->protect_sql($password['coef'])."', access_level='".eMain::$sql->protect_sql($_POST['access_level'])."', domain='".eMain::$sql->protect_sql($_POST['domain'])."'
					WHERE id=".$addmod.";";
				}
							
				if (!eMain::$sql->sql_query($rq)) {
					$error = eLang::translate("unable to process the query")."<br />$rq<br />".eMain::$sql->get_error();
				} else {
					echo '<div class="success">Données correctement enregistrées !</div>';
				}
			}
			
			if ($error!==false) {
				echo "<div class=\"error\">".$error."</div>";
			}
		}
		
		// Affichage datas //
		if ($addmod==0) {
		
			$content = array();
			$content['firstname']="";
			$content['lastname']="";
			$content['email']="";
			$content['login']="";
			$content['password']="";
			$content['password_coef']="";
			$content['domain']="*admin*";
			$content['access_level']=0;
			
			$title = "Ajout d'un nouvel utilisateur";
		
		} else {
		
			$rq = "SELECT * FROM $table WHERE id=$addmod;";
			$result_datas = eMain::$sql->sql_to_array($rq);
			$content = $result_datas['datas'][0];
			
			$title = "Modification de l'utilisateur \"".$content['login']."\"";
					
		}
				
?>
	<h1><?php echo eText::style_to_html($title); ?></h1>
	<div class="addmod">
	<form method="post" name="add" id="add">
		<table cellspacing="0">
			<tr>
				<td>login : </td>
				<td>
<?php
				if ($content['login']=="admin") {
?>
					admin<input type="hidden" value="admin" name="login" />
<?php
				} else {
?>
					<input type="text" value=<?php echo "\"".$content['login']."\""; ?> name="login" size="64" />
<?php
				} // END IF ADMIN
?>
				</td>
			</tr>
			<tr>
				<td>Mot de passe : </b></td>
				<td><input type="password" value="<?php echo $content['password']; ?>" name="password" size="64" /></td>
			</tr>
			<tr>
				<td>Confirmation mot de passe : </b></td>
				<td><input type="password" value="<?php echo $content['password']; ?>" name="password_conf" size="64" /></td>
			</tr>
			<!--
			<tr>
				<td>Type d'accès : </b></td>
				<td>
<?php
					if ($content['login']=="admin") {
?>
						0 : Complet (Administrateur)<input type="hidden" value="0" name="access_level" />
<?php
					} else {
?>
					<div class="slider">
						<div class="left"><?php echo eLang::translate('high', 'ucfirst'); ?></div>
						<div class="right"><?php echo eLang::translate('low', 'ucfirst'); ?></div>
						<input type="text" name="access_level" value="<?php echo $content['access_level']; ?>" placeholder="0=highest" />
					</div>
<?php
					} // END IF USER
?>
				</td>
			</tr>
			-->
			<input type="hidden" name="access_level" value="<?php echo $content['access_level']; ?>" placeholder="0=highest" />
			<tr>
				<td>Nom : </b></td>
				<td><input type="text" value="<?php echo $content['lastname']; ?>" name="lastname" size="64" /></td>
			</tr>
			<tr>
				<td>Prénom : </b></td>
				<td><input type="text" value="<?php echo $content['firstname']; ?>" name="firstname" size="64" /></td>
			</tr>
			<tr>
				<td>E-mail : </b></td>
				<td><input type="text" value="<?php echo $content['email']; ?>" name="email" size="64" /></td>
			</tr>
			
			<tr>
				<td>Type d'accès : </b></td>
				<td>
<?php
					if ($content['login']=="admin") {
?>
					<input type="hidden" name="domain" value="*" />Super Admin
<?php
					} else {

						for ($a=0;$a<count(eUser::$access_types);$a++) {
?>
					<input type="radio" <?php if ($content['domain']=="*".eUser::$access_types[$a]."*") { ?>checked="checked"<?php } ?> value="*<?php echo eUser::$access_types[$a]; ?>*" name="domain" id="user_<?php echo eUser::$access_types[$a]; ?>" /> 
					<label for="user_<?php echo eUser::$access_types[$a]; ?>"><?php eLang::show_translate('usertype '.eUser::$access_types[$a]); ?></label>
					<br />
<?php 
						}
					} // END IF USER
?>				
				</td>
			</tr>	
			
			<!-- <input type="hidden" value="<?php echo $content['domain']; ?>" name="domain" size="64" /> -->
		</table>
		<input type="hidden" name="old_password" value="<?php echo $content['password']; ?>" />
		<input type="hidden" name="password_coef" value="<?php echo $content['password_coef']; ?>" />
		<div class="float-right">
			<div class="submit">ENREGISTRER</div>
		</div>
		<div class="clear"> </div>
		
	</form>
	</div> <!--END OF ADDMOD -->
	
	<?php

	// STYLES //

	} else if ($page=="styles") {
	
		$table = eParams::$prefix."_cms_styles";
	
		// Traitement form //
		if (isset($_POST['name'])) {
		
			if (!get_magic_quotes_gpc()) {				
				foreach ($_POST as $post_key => $post_value) {
					$_POST[$post_key] = addslashes($post_value);
				}				
			}
		
			if ($addmod==0) {
				$rq = "INSERT INTO $table (name, text_font, text_color, text_size, background_color, border_color, border_size, other, activated) VALUES (\"".$_POST['name']."\", \"".$_POST['text_font']."\", \"".$_POST['text_color']."\", \"".$_POST['text_size']."\", \"".$_POST['background_color']."\", \"".$_POST['border_color']."\", \"".$_POST['border_size']."\", \"".$_POST['other']."\", \"".$_POST['activated']."\");";
			} else {
				$rq = "UPDATE $table SET name=\"".$_POST['name']."\", text_font=\"".$_POST['text_font']."\", text_color=\"".$_POST['text_color']."\", text_size=\"".$_POST['text_size']."\", background_color=\"".$_POST['background_color']."\", border_color=\"".$_POST['border_color']."\", border_size=\"".$_POST['border_size']."\", other=\"".$_POST['other']."\", activated=".$_POST['activated']." WHERE id=".$addmod.";";
			}
			if (!eMain::$sql->sql_query($rq)) {
				echo "<div class=\"error\">ERROR CMS002 : Unable to process the query $rq</div>";
			} else {
				echo '<div class="success">Données correctement enregistrées !</div>';
			}
		}
		
		// Affichage datas //
		if ($addmod==0) {
		
			$content = array();
			$content['name']="";
			$content['text_font']="Arial, Verdana, Geneva";
			$content['text_color']="#000000";
			$content['text_size']="12";
			$content['background_color']="#cccccc";
			$content['border_color']="#ffffff";
			$content['border_size']="1";
			$content['other']="";
			$content['activated']=1;
		
			$title = "Ajout d'un nouveau style";
		
		} else {
		
			$rq = "SELECT * FROM $table WHERE id=$addmod;";
			$result_datas = eMain::$sql->sql_to_array($rq);
			$content = $result_datas['datas'][0];
			
			$title = "Modification du style \"".$content['name']."\"";
					
		}
				
?>
	<h1><?php echo eText::style_to_html($title); ?></h1>	
	<div class="addmod">
	<form method="post" name="add" id="add">
		<b>Nom de l'élément : </b><br />
		<input type="text" value=<?php echo "\"".$content['name']."\""; ?> name="name" size="64" />
		<br /><br />		
		
		<b>Police de texte : </b><br />
		<input type="radio" name="text_font" value="Arial, Verdana, Geneva" <?php if ($content['text_font']=="Arial, Verdana, Geneva") { echo "checked"; } ?> /><span style="font-family:Arial, Verdana, Geneva;">Arial, Verdana, Geneva </span>
		<input type="radio" name="text_font" value="Times New Roman, Times" <?php if ($content['text_font']=="Times New Roman, Times") { echo "checked"; } ?> /><span style="font-family:Times New Roman, Times;">Times New Roman, Times </span>
		<input type="radio" name="text_font" value="Courrier New, Courrier" <?php if ($content['text_font']=="Courrier New, Courrier") { echo "checked"; } ?> /><span style="font-family:Courier New, Courier;">Courrier New, Courrier </span>
		<input type="radio" name="text_font" value="Garamond, Helvetica" <?php if ($content['text_font']=="Garamond, Helvetica") { echo "checked"; } ?> /><span style="font-family:Garamond, Helvetica;">Garamond, Helvetica</span>
		<input type="radio" name="text_font" value="inherit" <?php if ($content['text_font']=="inherit") { echo "checked"; } ?> /><span style="font-family:inherit;">Normale (héritée)</span>
		<br /><br />
		
		<b>Couleur du texte : </b><br />
		<input type="radio" name="text_color" value="#336699" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="#336699") { echo "checked"; } ?> /><span style="color:#336699;">Bleu </span>
		<input type="radio" name="text_color" value="#333333" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="#333333") { echo "checked"; } ?> /><span style="color:#333333;">Gris foncé </span>
		<input type="radio" name="text_color" value="#cccccc" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="#cccccc") { echo "checked"; } ?> /><span style="color:#cccccc;">Gris clair </span>
		<input type="radio" name="text_color" value="#000000" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="#000000") { echo "checked"; } ?> /><span style="color:#000000;">Noir</span> 
		<input type="radio" name="text_color" value="#ffffff" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="#ffffff") { echo "checked"; } ?> /><span style="color:#ffffff;">Blanc</span> 
		<input type="radio" name="text_color" value="inherit" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_color']=="inherit") { echo "checked"; } ?> /><span style="color:inherit;">Normale (héritée)</span> 
		
		<blockquote>
		<b>Valeur : </b>
		<input type="text" id="text_color" value=<?php echo "\"".$content['text_color']."\""; ?> name="text_color" size="8" /> (exemples : #336699 ou rgb(255,160,140))
		</blockquote><br />
		
		<b>Taille du texte : </b><br />
		<input type="radio" name="text_size" value="10" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_size']=="10") { echo "checked"; } ?> /><span style="font-size:10px;">10 - Dix </span>
		<input type="radio" name="text_size" value="12" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_size']=="12") { echo "checked"; } ?> /><span style="font-size:12px;">12 - Douze </span>
		<input type="radio" name="text_size" value="14" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_size']=="14") { echo "checked"; } ?> /><span style="font-size:14px;">14 - Quatorze </span>
		<input type="radio" name="text_size" value="16" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_size']=="16") { echo "checked"; } ?> /><span style="font-size:16px;">16 - Seize</span> 
		<input type="radio" name="text_size" value="inherit" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['text_size']=="inherit") { echo "checked"; } ?> /><span style="font-size:inherit;">Normale (héritée)</span> 
		
		<blockquote>
		<b>Valeur : </b>
		<input type="text" id="text_size" value=<?php echo "\"".$content['text_size']."\""; ?> name="text_size" size="8" />
		</blockquote><br />
		
		<b>Couleur du fond : </b><br />
		<input type="radio" name="background_color" value="#336699" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#336699") { echo "checked"; } ?> /><span style="background-color:#336699;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="background_color" value="#333333" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#333333") { echo "checked"; } ?> /><span style="background-color:#333333;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="background_color" value="#cccccc" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#cccccc") { echo "checked"; } ?> /><span style="background-color:#cccccc;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="background_color" value="#000000" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#000000") { echo "checked"; } ?> /><span style="background-color:#000000;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="background_color" value="#339966" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#339966") { echo "checked"; } ?> /><span style="background-color:#339966;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="background_color" value="#993366" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#993366") { echo "checked"; } ?> /><span style="background-color:#993366;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="background_color" value="#ffffff" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="#ffffff") { echo "checked"; } ?> /><span style="background-color:#ffffff;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="background_color" value="transparent" onClick="document.getElementById('background_color').value=this.value;" <?php if ($content['background_color']=="transparent") { echo "checked"; } ?> /><span style="background-color:transparent;">Aucun fond</span> 
		<blockquote>
		<b>Valeur : </b>
		<input type="text" id="background_color" value=<?php echo "\"".$content['background_color']."\""; ?> name="background_color" size="8" /> (exemples : #336699 ou rgb(255,160,140))
		</blockquote><br />
		
		<b>Taille de la bordure (0 = aucune) : </b><br />
		<input type="radio" name="border_size" value="1" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_size']=="1") { echo "checked"; } ?> /><span style="border:1px solid #000000;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="border_size" value="2" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_size']=="2") { echo "checked"; } ?> /><span style="border:2px solid #000000;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="border_size" value="3" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_size']=="3") { echo "checked"; } ?> /><span style="border:3px solid #000000;">&nbsp; &nbsp; &nbsp; </span>
		<input type="radio" name="border_size" value="0" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_size']=="0") { echo "checked"; } ?> /><span style="border:0px solid #000000;">Aucune</span> 
		<blockquote>
		<b>Valeur : </b>
		<input type="text" id="border_size" value=<?php echo "\"".$content['border_size']."\""; ?> name="border_size" size="8" />
		</blockquote><br />
		
		<b>Couleur de la bordure : </b><br />
		<input type="radio" name="border_color" value="#336699" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_color']=="#336699") { echo "checked"; } ?> /><span style="border:1px solid #336699;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="border_color" value="#333333" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_color']=="#333333") { echo "checked"; } ?> /><span style="border:1px solid #333333;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="border_color" value="#cccccc" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_color']=="#cccccc") { echo "checked"; } ?> /><span style="border:1px solid #cccccc;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="border_color" value="#000000" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_color']=="#000000") { echo "checked"; } ?> /><span style="border:1px solid #000000;">&nbsp; &nbsp; &nbsp; </span> 
		<input type="radio" name="border_color" value="#ffffff" onClick="document.getElementById(this.name).value=this.value;" <?php if ($content['border_color']=="#ffffff") { echo "checked"; } ?> /><span style="border:1px solid #ffffff;">&nbsp; &nbsp; &nbsp; </span> 
		<blockquote>
		<b>Valeur : </b>
		<input type="text" id="border_color" value=<?php echo "\"".$content['border_color']."\""; ?> name="border_color" size="8" /> (exemples : #336699 ou rgb(255,160,140))
		</blockquote><br />
		
		<b>Activation : </b><br />
		<select name="activated">
			<option value="0" <?php if ($content['activated']==0) { echo "selected"; } ?> >0 : Non</option>
			<option value="1" <?php if ($content['activated']==1) { echo "selected"; } ?> >1 : Oui</option>
		</select>
		<br /><br />
				
		<b>Autres valeurs : (langage CSS, réservé aux professionnels)</b><br />
		<div class="group">
			<textarea name="other" cols="96" rows="10" id="form-content"><?php echo $content['other']; ?></textarea>			
		</div>
		<br /><br />
		
		<div class="align_right">
			<div class="submit">ENREGISTRER</div>
		</div>
		
	</form>
	</div> <!--END OF ADDMOD -->
	
<?php

	// PARAMS //

	} else if ($page=="params") {
		include('_form_params.php'); 
	} else if ($page=="images") {
		include('_form_images.php'); 
	}
	
	// PLUGINS //
	for ($m=0;$m<eCMS::$modules['nb'];$m++) {
		if (eCMS::$modules['datas'][$m]['backoffice']!='') {
			include('plugins/'.eCMS::$modules['datas'][$m]['backoffice'].'/addmod.php');
		}
	}
	
?>
