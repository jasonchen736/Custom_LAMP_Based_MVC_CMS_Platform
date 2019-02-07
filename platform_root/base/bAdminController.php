<?php

	class bAdminController extends bController {
		// admin auth object
		protected $auth;

		/**
		 *  Perform authentication
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			parent::__construct();
			$this->auth = adminAuth::getInstance();
			$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			if (!$this->auth->validate() && $url != $this->auth->get('loginURL')) {
				redirect($this->auth->get('loginURL'));
			}
		} // function __construct

		/**
		 *  Export search
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: none
		 */
		public function export() {
			$class = preg_replace('/Controller$/', '', get_called_class());
			headers::sendExportHeaders($class.'Export.csv');
			$dataViewClass = 'dataView'.ucfirst($class);
			$view = new $dataViewClass;
			$records = $view->performSearch(true);
			if (!empty($records)) {
				$headers = array_keys(current($records));
				$headerLine = '';
				$last = '';
				foreach ($headers as $header) {
					$headerLine .= $header.',';
					$last = $header;
				}
				echo rtrim($headerLine, ',');
				foreach ($records as $record) {
					echo "\n";
					foreach ($record as $field => $value) {
						echo '"', str_replace('"', '""', $value), '"';
						if ($field != $last) {
							echo ',';
						}
					}
	
				}
			} else {
				echo 'No records found';
			}
			exit;
		} // function export
	} // function bAdminController

?>
