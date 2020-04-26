<?php

use RedBeanPHP\R;
use DawPhpPagination\Pagination;

class TaskModel
{
	const sort_params = ['name', 'email', 'status'];

	public static function getList(): array
	{
		$res = [];


		$pagination = new Pagination(['pp' => 3, 'options_select' => [1, 3, 5], 'number_links' => 1]);
		$pagination->paginate(R::count('app_tasks'));


		$sort_type = $_GET['user_name'] ?? $_GET['email'] ?? $_GET['status'] ?? 0;
		$order_by = '';
		if ($sort_type) {
			$order_by = array_search($sort_type, $_GET);
		}

		$limit = $pagination->getLimit();
		$offset = $pagination->getOffset();
		$query = "SELECT * FROM app_tasks ";
		if (!empty($order_by)) {
			$query .= " ORDER BY $order_by $sort_type";
		}
		$query .= " LIMIT $limit OFFSET $offset";

		$res['items'] = R::getAll($query);
		$res['page_nav'] = $pagination;

		return $res;
	}

	public function updateTask($post_data)
	{

		$task = R::load('app_tasks', $post_data['id']); //reloads our book
		$task['status'] = $post_data['status'];
		if(isset($post_data['text'])){
			if($post_data['text'] !== $task['text']){
				$task['text'] = $post_data['text'];
			}
			$task['admin_edit'] = 1;

		}
		if(isset($post_data['user_name'])) $task['user_name'] = $post_data['user_name'];
		if(isset($post_data['email'])) $task['email'] = $post_data['email'];

		return R::store($task);
	}

	public function createTask($post_data)
	{

		R::ext('xdispense', function ($type) {
			return R::getRedBean()->dispense($type);
		});

		$task = R::xdispense('app_tasks');
		$task['user_name'] = $post_data['user_name'];
		$task['email'] = $post_data['email'];
		$task['text'] = $post_data['text'];
		return R::store($task);
	}

	public function getTask($post_data)
	{
		$task =  R::getRow( 'SELECT * FROM app_tasks WHERE id = ? LIMIT 1',
			[ $post_data['id'] ]
		);
		return $task;
	}
}