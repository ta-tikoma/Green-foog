<?php

class GFoogController {
	
	private $gf_connector;    
	private $gf_get;
	private $gf_view;

	private $user;
	
	function __construct($user)
	{
		$this->gf_connector = new GFoogConnector();
		$this->gf_view = new GFoogView('admin');
		$this->gf_get = new GFoogGet();
		$this->user = $user;
	}
	
	function run()
	{
		// check authorize beging

		if (!isset($_COOKIE['hash'])) {
			$this->login_action();
			return;
		}

		//echo $_COOKIE['hash'];
		if ($this->user['hash'] !== $_COOKIE['hash']) {
        	setcookie("hash", "", time() - 3600*24*30*12, "/");
        	$this->login_action();
			return;
		}
		// check authorize end

		if (!isset($_GET['action']))
		{
			$this->gf_view->show($this->gf_get);
			return;
		}

		switch ($_GET['action']) {
			case 'new_action':
				$this->new_action();
				break;
			case 'edit_params_action':
				$this->edit_params_action();
				break;
			case 'save_value_action':
				$this->save_value_action();
				break;
			case 'remove_action':
				$this->remove_action();
				break;
			case 'clone_action':
				$this->clone_action();
				break;
			case 'get_item_action':
				$this->get_item_action();
				break;
			case 'get_value_action':
				$this->get_value_action();
				break;
			case 'logout_action':
				$this->logout_action();
				break;
			default:
				$this->gf_view->show($this->gf_get, '404');
				break;
		}
	}

	function logout_action() {
		unset($_COOKIE['hash']);
        setcookie('hash', null, -1);
		header("Location: index.php"); exit();
	}

	function generateCode($length=6) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
	    $code = "";
	    $clen = strlen($chars) - 1;  
	    while (strlen($code) < $length) {

	            $code .= $chars[mt_rand(0,$clen)];  
	    }

	    return $code;
	}

	function login_action()
	{

		if (isset($_POST['submit'])) {

			$login = (isset($_POST['login']) && is_string($_POST['login'])) ? $_POST['login'] : null;
			$password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : null;

		    if (($this->user['password'] === $password) && ($this->user['login'] === $login)) {

		        $hash = md5($this->generateCode(10));

		        file_put_contents('../setting.php', '<?php 
		        	$user = array(
						"login" => "'.$this->user['login'].'",
						"password" => "'.$this->user['password'].'",
						"hash" => "'.$hash.'",
					);');

		        setcookie("hash", $hash, time()+60*60*24*30);

		        header("Location: index.php"); exit();

		    } else {
		        print "Вы ввели неправильный логин/пароль";
		    }
		}
		$this->gf_view->show($this->gf_get, 'login');
	}

	function new_action()
	{
		echo json_encode($this->gf_connector->new_query(
			$_POST['record']['hash']
			, array(
				'name' 			=> $_POST['record']['name'],
				'type' 			=> $_POST['record']['type'],
				'description' 	=> $_POST['record']['description'],
				'position' 		=> $_POST['record']['position'],
				'value' 		=> ""
			)));
	}
	
	function edit_params_action()
	{
		echo json_encode($this->gf_connector->edit_params_query(
				$_GET['hash'], 
				$_GET['name'], 
				array(
					'description' => $_POST['record']['description'],
					'name' => $_POST['record']['name'],
					'position' => $_POST['record']['position']
				)
			));
	}

	function save_value_action()
	{
		echo json_encode($this->gf_connector->save_value_query($_POST['value']));
	}

	function remove_action()
	{
		echo json_encode($this->gf_connector->remove_query($_POST['hash']));
	}

	function clone_action()
	{
		echo json_encode($this->gf_connector->clone_query($_POST['hash'], $_POST['name']));
	}

	function get_item_action()
	{
		echo json_encode($this->gf_connector->get_item_query($_GET['hash']));
	}
	
	function get_value_action()
	{
		echo json_encode($this->gf_get->get($_GET['hash']));
	}
}