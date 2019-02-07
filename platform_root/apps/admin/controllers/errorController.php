<?php

	class errorController extends bAdminController {
		public function error($message) {
			header('HTTP/1.0 404 Not Found');
			$this->assignClean('_TITLE', '404');
			$this->renderView('404.tpl');
		}
	} // function errorController

?>
