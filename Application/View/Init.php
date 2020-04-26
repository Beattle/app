<?php

class View
{

	private $result;
	private $content; // content of page output

	public function __construct( $View,$result,$action = 'index')
	{
		$this->result = $result;
		if(!isset($_POST['ajax'])){
			$this->renderView(strtolower($View),$action);
		}
	}



	public function renderView($View,$action)
	{
		ob_start();
		/** @noinspection PhpIncludeInspection */
		include APP_PATH."View/$View/$action.phtml";
		$this->content = ob_get_clean();


		$this->renderLayout();

		// require APP_PATH."View/$View/$action.phtml";
	}

	private function renderLayout()
	{
		include APP_PATH."View/Layout/template.phtml";
	}


}
