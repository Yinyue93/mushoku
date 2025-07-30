<?php
/**
 * JSON file handler
 *
 * PHP Version 5.3.0 or Upper version
 *
 * @package    Dura
 * @author     schnabear
 * @copyright  2014 schnabear
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3
 *
 */

class Dura_Class_JsonHandler
{
	protected $className = 'Dura_Class_Json';
	protected $fileName  = 'json';

	public function __construct($className = null)
	{
		if ( $className )
		{
			$this->className = $className;
		}
	}

	public function create()
	{
		return new $this->className(array());
	}

	public function load($id)
	{
		$file = $this->getFilePath($id);

		$contents = file_get_contents($file);
		$contents = json_decode($contents, true);

		if ( !$contents )
		{
			return false;
		}

		$json = new $this->className($contents);

		return $json;
	}

	public function save($id, $json)
	{
		$json->update = time();
		$file = $this->getFilePath($id);
		return file_put_contents($file, (string) $json, LOCK_EX);
	}

	public function delete($id)
	{
		$file = $this->getFilePath($id);
		$result = @unlink($file);
		
		// If this is a room deletion, also remove the associated upload directory
		if ($result && $this->fileName === 'room') {
			$uploadDir = dirname(dirname(__DIR__)) . '/uploads/room_' . $id;
			if (is_dir($uploadDir)) {
				$this->_removeDirectory($uploadDir);
			}
		}
		
		return $result;
	}
	
	/**
	 * Recursively remove a directory and all its contents
	 */
	private function _removeDirectory($dir)
	{
		if (!is_dir($dir)) {
			return false;
		}
		
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			$path = $dir . '/' . $file;
			if (is_dir($path)) {
				$this->_removeDirectory($path);
			} else {
				@unlink($path);
			}
		}
		
		return @rmdir($dir);
	}

	public function getFilePath($id)
	{
		return DURA_STORAGE_PATH.'/'.$this->getFileName($id);
	}

	public function getFileName($id)
	{
		return $this->fileName.'_'.$id.'.json';
	}
}
