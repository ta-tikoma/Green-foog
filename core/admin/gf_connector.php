<?php
// data base folder
//define('gf_db', str_replace('\\', '/', getcwd()).'/../data_base/');

class GFoogConnector
{

	public function getDBpath()
	{
		return gf_db;
	}

	private function get_new_file_name($path_to_dir, &$name)
	{
		if (!is_dir(gf_db.$path_to_dir))
			return false;

		while (file_exists(gf_db.$path_to_dir.'/'.$name.'.txt')) {
			if (preg_match("/([0-9])+/", $name)) {
				$name = preg_replace_callback("/([0-9])+/", "self::inc", $name);
			} else {
				$name .= 0;
			}
		}

		return true;
	}

	public function new_query($hash, $data)
	{
		$status = "error";
		$message = "folder not found";
		$record = array();

		if ($this->get_new_file_name($hash, $data['name'])) {
			if (file_put_contents(gf_db.$hash.'/'.$data['name'].'.txt', serialize($data)) !== false) {
				if ($data['type'] == 'list') {
					if (!mkdir(gf_db.$hash.'/'.$data['name'])) {
						if (unlink(gf_db.$hash.'/'.$data['name'].'.txt')) {
							$message = 'can\'t create folder';
						} else {
							$message = 'can\'t create folder and info file can\'t remove';
						}
					} else {
						$message = "";
						$status = "success";
					}
				} else { 
					$message = "";
					$status = "success";
				}
				$record = $data;
			} else {
				$message = "can't write file";
			}
		}

		return array(
			'status' 	=> $status,
			'message'	=> $message, 
			'record' 	=> $record
			);
	}

	public function edit_params_query($hash, $name, $arg)
	{
		$item = $this->get_item_query($hash.'/'.$name);
		if ($item['status'] == 'error')
			return $item;
		$message = "";
		$data = $item['record'];

		$data['description'] = $arg['description'];
		$data['position'] = $arg['position'];
		$check = true;
		if ($data['name'] != $arg['name']) {
			if ($this->get_new_file_name($hash, $arg['name'])) {
				if (rename(gf_db.$hash.'/'.$data['name'].'.txt', gf_db.$hash.'/'.$arg['name'].'.txt')) {
					if ($data['type'] == 'list') {
						if (rename(gf_db.$hash.'/'.$data['name'], gf_db.$hash.'/'.$arg['name'])) {

						} else {
							$message = 'folder can\'t rename';
							$check = false;
						}
					}
				} else {
					$message = 'file can\'t rename';
					$check = false;
				}
			} else {
				$message = 'folder not found';
				$check = false;
			}
		}

		if ($check) {
			$data['name'] = $arg['name'];
			if (file_put_contents(gf_db.$hash.'/'.$arg['name'].'.txt', serialize($data)) !== false) {
				$data['hash'] = $hash.'/'.$arg['name'];
				return array(
					'status'  => 'success',
					'message' => '', 
					'record' => $data
					);
			} else {
				$message = 'can\'t write file';
			}
		}

		return array(
			'status'  => 'error',
			'message' => $message, 
			'record' => array()
			);
		
	}

	public function save_value_query($arg)
	{
		$message = "";
		foreach ($arg as $key => $value) {
			$content = file_get_contents(gf_db.$key.'.txt');
			if ($content != false) {
				$record = unserialize($content);
				$record['value'] = $value;
				if (file_put_contents(gf_db.$key.'.txt', serialize($record)) == false) {
					$message .= 'cat\'t write: '.$key;
				}
			} else {
				$message .= 'can\'t read: '.$key;
			}
		}
			
		return array(
			'status'  => ($message == '') ? 'success' : 'error',
			'message' => $message,
			);
		
	}

	public function remove_query($hash)
	{
		$message = "";
		if (unlink(gf_db.$hash.'.txt') == false)
			$message = 'can\'t remove';
		if (is_dir(gf_db.$hash))
			if (rmdir(gf_db.$hash) == false)
				$message .= 'can\'t remove list';

		return array(
			'status'  => ($message == '') ? 'success' : 'error',
			'message' => $message,
			);
	}

	public function inc($st) {
		return (1 + (int) $st[0])."";
	}

	private function recursiveCopy($src, $dest) {
		if (is_dir($src)) 
			$dir = opendir($src);
		while ($file = readdir($dir)) {
			if ($file != '.' && $file != '..') {
				if (!is_dir($src.'/'.$file)) copy($src.'/'.$file, $dest.'/'.$file);
				else {
					@mkdir($dest.'/'.$file);
					$this->recursiveCopy($src.'/'.$file, $dest.'/'.$file);
				} //else
			} //if
		} //while
		closedir($dir);
	} 

	public function clone_query($hash, $name)
	{
		$message = "not found";
		$path = gf_db.$hash.'/'.$name.'.txt';
		if (file_exists($path)) {
			$content = file_get_contents($path);
			if ($content != false) {
				$record = unserialize($content);
				if ($this->get_new_file_name($hash, $record['name'])) {
					// copy origin file
					if (file_put_contents(gf_db.$hash.'/'.$record['name'].'.txt', serialize($record)) != false) {
						$check = true;
						// create folder
						if ($record['type'] == 'list') {
							if (!mkdir(gf_db.$hash.'/'.$record['name'])) {
								if (unlink(gf_db.$hash.'/'.$record['name'].'.txt')) {
									$message = 'can\'t create folder';
									$check = false;
								} else {
									$message = 'can\'t create folder and info file can\'t remove';
									$check = false;
								}
							} else {
								// folder is created
								$this->recursiveCopy(gf_db.$hash.'/'.$name, gf_db.$hash.'/'.$record['name']);
							}
						}

						if ($check) {
							return array(
									'status' 	=> 'success',
									'message'	=> '',
									'record' 	=> $record
								);
						}
					} else {
						$message = "can't copy file";
					}
				} else {
					$message = 'folder not found';
				}
			} else {
				$message = 'can\'t read '.$path;
			}
		}

		return array(
			'status' 	=> 'error',
			'message'	=> $message,
			'record'	=> array()
			);
	}

	public function get_item_query($hash)
	{
		$message = "";
		$path = gf_db.$hash.'.txt';
		
		if (file_exists($path)) {
			$content = file_get_contents($path);
			if ($content != false) {
				$record = unserialize($content);
				
				if ($record['type'] == 'list') {
					$items_list = $this->get_items_list($hash);
					$message .= $items_list['message'];
					$record['value'] = $items_list['record'];
				}

				return array(
					'status' 	=> 'success',
					'message'	=> $message, 
					'record' 	=> $record
					);
			} else {
				$message = 'can\'t read file';
			}
		} else {
			$message = 'not found';
		}
		
		return array(
			'status' 	=> 'error',
			'message'	=> $message, 
			'record' 	=> array()
			);
	}

	function cmp($a, $b) {
		if ($a['position'] == $b['position']) {
			return 0;
		}
		return ($a['position'] < $b['position']) ? -1 : 1;
	}

	private function get_items_list($hash)
	{
		$message = "";
		$path = gf_db.$hash;
		$record = array();
		if ($handle = opendir($path)) {

			while (false !== ($file = readdir($handle))) {
				if  (is_file($path.'/'.$file)) {
					$content = file_get_contents($path.'/'.$file);
					if ($content != false)
						$record[] = unserialize($content);
					else
						$message .= 'can\'t open '.$file;
				}
			}

   			closedir($handle); 
		} else {
			$message = 'can\'t open list';
		}

		uasort($record, 'self::cmp');
		
		return  array(
			'record' 	=> array_values($record),
			'message'	=> $message
			);
	}
}