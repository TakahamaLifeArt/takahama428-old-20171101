<?php
require_once dirname(__FILE__)."/UploadHandler.php";
class MyUploadHandler extends UploadHandler
{
	public function __construct($options = null, $initialize = true, $error_messages = null) {
		parent::__construct($options, $initialize, $error_messages);
	}
	
	
	protected function get_file_name($file_path, $name, $size, $type, $error,
									 $index, $content_range) {
		$name = $this->trim_file_name($file_path, $name, $size, $type, $error,
									  $index, $content_range);
		return $this->fix_file_extension($file_path, $name, $size, $type, $error,
										 $index, $content_range);
	}
}
?>
