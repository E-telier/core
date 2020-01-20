<?php
class eUser {
	
	private static $eUser = null;
	public static $access_types = array('admin', 'consult');
	
	public $checked = false;
	public $timezone = false;
	private $user_datas = array();
	
	public static function getInstance() {
		// SINGLETON //
		
		if (isset($_GET['disconnect']) || isset($_POST['disconnect'])) {
			self::$eUser=null;
			unset($_SESSION['e_user']);
			header('Location: '.eMain::root_url());
			die('');
		}
		
		if (self::$eUser==null) {
			if (isset($_SESSION['e_user'])) {
				self::$eUser = unserialize($_SESSION['e_user']);
			} else {
				self::$eUser = new eUser();		
			}
		}
		
		return self::$eUser;
	}
		
	public function init() {		
		$this->connect();		
	}
		
	public function connect() {	
					
		$datas = array();
		if (isset($_POST['connect'])) {
			$datas = $_POST;			
		}
		if (isset($_GET['connect'])) {
			$datas = $_GET;			
		}
							
		if (isset($datas['connect_login']) && isset($datas['connect_password'])) {
						
			// GET / POST //
			$this->user_datas['login'] = $datas['connect_login'];
			 
			// User insde mariadb
			$rq = "SELECT id, password_coef FROM ".eParams::$prefix."_users WHERE login='".eMain::$sql->protect_sql($this->user_datas['login'])."' LIMIT 1;";
			$user_datas = eMain::$sql->sql_to_array($rq);
						
			if ($user_datas['nb']>0) {
				// check matching encrypted
				$encrypted = eMain::encrypt($datas['connect_password'], $user_datas['datas'][0]['password_coef']);
				$this->user_datas['password'] = $encrypted['encrypted'];
				$this->checked = $this->check_user();				
			} else {
				eMain::add_error("login doesn't exist");
				$this->checked = false;
			}
		} 
		
		if ($this->checked && isset($user_datas)) {
			// CONNEXION SUCCEED //
			$rq = "UPDATE ".eParams::$prefix."_users SET last_connection='".gmdate('Y-m-d H:i:s')."' WHERE id=".$user_datas['datas'][0]['id'];
			eMain::$sql->sql_query($rq);	
		} else if (!isset($datas['connect_login']) && isset($this->user_datas['login']) && isset($this->user_datas['password'])) {
			// SESSION //
			$this->checked = $this->check_user();
		}
		
		//header('Location: '.eMain::root_url());
		//die('');
		
	}
		
	private function save_user() {
		$_SESSION['e_user'] = serialize($this);
	}
		
	private function check_user() {
		$rq = "SELECT id FROM ".eParams::$prefix."_users WHERE login='".eMain::$sql->protect_sql($this->user_datas['login'])."' AND password='".eMain::$sql->protect_sql($this->user_datas['password'])."' LIMIT 1;";
		$id_user = eMain::$sql->sql_to_array($rq);
				
		if ($id_user['nb']==0) {
			eMain::add_error('wrong login or password');
			return false;
		} else {
			$this->user_datas['id'] = $id_user['datas'][0]['id'];
		}
		$this->save_user();
		
		return true;
	}
	
	public function get_datas($datas_str = '*', $force_update = false) {
		
		if (!$this->checked) { return false; }
					
		$fields = explode(',', $datas_str);
		for ($i=0;$i<count($fields);$i++) {
			if (!isset($this->user_datas[$fields[$i]])) {
				$force_update = true;
			}
		}
		
		if ($datas_str=='*' || $force_update==true) {
			$rq = "SELECT ".eMain::$sql->protect_sql($datas_str)." FROM ".eParams::$prefix."_users WHERE id=".eMain::$sql->protect_sql($this->user_datas['id'])." LIMIT 1;";
			$new_datas = eMain::$sql->sql_to_array($rq);
			$new_datas = $new_datas['datas'][0];

			$this->user_datas = array_merge($this->user_datas, $new_datas);
		} else {
			$new_datas = array();
			for ($i=0;$i<count($fields);$i++) {
				$new_datas[$fields[$i]] = $this->user_datas[$fields[$i]];
			}
		}
		
		$this->save_user();
		
		return $new_datas;			
		
	}
		
}
?>