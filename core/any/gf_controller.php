<?php

class GFoogController {
	
	private $gf_get;
	private $gf_view;

	function __construct()
	{
		$this->gf_get = new GFoogGet();
		$this->gf_view = new GFoogView($this->gf_get->get("configuration/theme-name"));
	}
	
	function run()
	{
		$this->gf_view->show($this->gf_get);
	}
}