<?php

use RedBeanPHP\R;

class Task
{
	const sort_params = ['user_name','email','status'];


	public function renderTaskList()
	{
		$sort_type = $_GET['user_name'] ?? $_GET['email'] ?? $_GET['status'] ?? 0;
		$order_by = '';
		if ($sort_type) {
			$order_by = array_search($sort_type, $_GET);
		}
		$res = TaskModel::getList($sort_type,$order_by,$nav_count = 3);
		$res['sort'] = $this->getSortingQuery();
		$action = 'index';
		if (isset($_SESSION['success'])) {
			$res['success'] = $_SESSION['success'];
			unset($_SESSION['success']);

		};
		if(isset($_SESSION['update'])){
			$res['update'] = $_SESSION['update'];
			unset($_SESSION['update']);
		}
		return [$res, $action];
	}

	/**
	 * @return array
	 * @throws \RedBeanPHP\RedException\SQL
	 * @throws \RedBeanPHP\RedException
	 */
	public function createTask()
	{
		if (!empty($_POST)) {
			$post_data['user_name'] = $_POST['user_name'];
			$post_data['email'] = $_POST['email'];
			$post_data['text'] = htmlspecialchars($_POST['text']);

			$id = (new TaskModel())->createTask($post_data);
			if ($id) {
				$_SESSION['success'] = 'Вы успешно добавили задачу';
			}
			header('Location: http://' . BASE_URI);
		}

		return ['', 'index'];
	}

	public static function addToURL($key, $value, $url)
	{
		$info = parse_url($url);
		parse_str($info['query'], $query);
		return $info['scheme'] . '://' . $info['host'] . $info['path'] . '?' .
			http_build_query($query ? array_merge($query,
				array($key => $value)) : array($key => $value));
	}

	private static function getCurrentUrl()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	private function getSortingQuery(){
		$sort = [];
		foreach (self::sort_params as $param){
			$sort[$param]['type'] = 'asc';
			if(isset($_GET[$param]) && $_GET[$param] === 'asc'){
				$sort[$param]['type'] = 'desc';
			}
		}
		return $sort;

	}

	/**
	 */
	public function updateTask()  {
		if(!isset($_SESSION['user'])){
			$res['errors'] = 'Нет доступа к редактированию задачи';
			return [$res,'403'];
		}

		$post_data['id'] = (int)$_POST['id'];
		if(isset($_POST['status'])){
			$post_data['status'] = $_POST['status'] === 'true'? 1:0;
		}

		if(isset($_POST['user_name'])){
			$post_data['user_name'] = $_POST['user_name'];
		}
		if(isset($_POST['email'])){
			$post_data['email'] = $_POST['email'];
		}
		if($_POST['text']){
			$post_data['text'] = $_POST['text'];
		}

		$id  = (new TaskModel())->updateTask($post_data);
		if($id){
			$_SESSION['update'] = 'Вы успешно изменили задачу';
		}
		if(!isset($_POST['ajax'])){
		 	header('Location:http://' . BASE_URI);
		}
	}

	public function editTask(){
		$post_data['id'] = (int)$_GET['id'];
		$res = TaskModel::getTask($post_data);
		return [$res,'form'];
	}


}