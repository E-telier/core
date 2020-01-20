<?php
class eParams {
	
	// MAIN //
	public static $prefix = 'e';	
	
	// MAIL //
	public static $admin_sender = "contact@e-telier.be";
	public static $admin_smtp = array(
		'host'=>"ssl0.ovh.net",
		'port'=>"465",
		'username'=>"",
		'password'=>"",
		'encryption'=>'ssl'
	);
	
	// LANG //
	public static $default_lang = 'fr';
	public static $available_languages = array('fr', 'en');
	
	// SQL //	
	public static $sql_user="e";
	public static $sql_host="e.mysql.db";
	public static $sql_password="e";
	public static $sql_database="e";
	public static $sql_port=null;

	// CMS //
	public static $site_name = "E-telier multimédia";
				
	public static $contact_email = "contact@";
	public static $sender_email = "contact@";
				
	public static $ga_id = "UA-123456789-1";
	
	// CUSTOM //
	//public static $custom_api = 'plugins/customs/orc_API.php';
	public static $stats = true;
	
}
?>