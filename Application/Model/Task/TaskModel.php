<?php

use RedBeanPHP\R;
use DawPhpPagination\Pagination;

class TaskModel
{

	public static function getList($sort_type,$order_by,$nav_count): array
	{
		$res = [];


		$pagination = new Pagination(['pp' => $nav_count, 'options_select' => [1, 3, 5], 'number_links' => 1]);
		$pagination->paginate(R::count('app_tasks'));


		$limit = $pagination->getLimit();
		$offset = $pagination->getOffset();
		$query = "SELECT * FROM app_tasks ";
		if (!empty($order_by)) {
			$query .= " ORDER BY $order_by COLLATE NOCASE $sort_type ";
		}
		$query .= " LIMIT $limit OFFSET $offset";

		$res['items'] = R::getAll($query);
		$res['page_nav'] = $pagination;

		return $res;
	}

	public function updateTask($post_data)
	{

		$task = R::load('app_tasks', $post_data['id']);
		if(isset($post_data['status'])){
			$task['status'] = $post_data['status'];
		}

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

	public static function getTask($post_data)
	{
		$task =  R::getRow( 'SELECT * FROM app_tasks WHERE id = ? LIMIT 1',
			[ $post_data['id'] ]
		);
		return $task;
	}
}