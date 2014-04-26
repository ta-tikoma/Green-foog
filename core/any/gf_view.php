<?php

class GFoogView
{
	private $themes_name;

	function __construct($themes_name)
	{
		$this->themes_name = $themes_name;
	}

	function show($gf_get, $template_view = null)
	{
		header("Cache-Control: no-cache, must-revalidate");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		
		$path_to_theme = 'themes/'.$this->themes_name.'/';

		if (isset($template_view)) {
			include $path_to_theme.$template_view.'.php';
		} else {
			include $path_to_theme.'index.php';
		}
	}
}
