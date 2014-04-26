<?php

define('gf_db', str_replace('\\', '/', getcwd()).'/data_base/');

class GFoogGet
{

	public function get($hash)
	{
		$path = gf_db.'root/'.$hash.'.txt';

		if (file_exists($path)) {
			return $this->get_value($path)['record'];
		} else {
			return array();
		}
	}

	function cmp($a, $b) {
		if ($a['position'] == $b['position']) {
			return 0;
		}
		return ($a['position'] < $b['position']) ? -1 : 1;
	}

	private function get_values_list($path)
	{
		$result_sort = array();
		$result = array();
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if  (is_file($path.'/'.$file)) {
					$record = $this->get_value($path.'/'.$file);
					$result[$record['name']] = array('value' => $record['record'], 'position' => $record['position']);
				}
			}
   			closedir($handle); 
		}
		
		uasort($result, 'self::cmp');
		foreach ($result as $key => $value)
			$result_sort[$key] = $value['value'];
		
		return $result_sort;
	}

	private function get_value($file) 
	{
		$record = unserialize(file_get_contents($file));
		if ($record['type'] == 'list') {
			$path_parts = pathinfo($file);
			$record['value'] = $this->get_values_list($path_parts['dirname'].'/'.$path_parts['filename']);
		}
		return array(
			'name'	=> $record['name'],
			'record' => $record['value'],
			'position' => $record['position']
			);		
	}
}
