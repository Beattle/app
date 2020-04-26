<?php
use RedBeanPHP\R;

class User
{
	public function renderAuth(){
		return [false,'login'];
	}

	public function auth(){
		$login = trim($_POST['login']);
		$pass = trim($_POST['password']);
		$admin = R::findOne('users',' login = ?',[$login]);
		if(!$admin){
			$res['errors'] = 'Пользователь не найден';
			return [$res,'login'];
		}
		if(password_verify($pass,$admin['pass'])){
					$_SESSION['auth'] = true;
					$_SESSION['user'] = $admin['login'];
			header("Location: http://".BASE_URI);
		}

	}

	public function logout(){
		session_destroy();
		header("Location: http://{$this->getBaseUriString()}/");
	}

	private function getBaseUriString():string {
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		return "$host$uri";
	}

}