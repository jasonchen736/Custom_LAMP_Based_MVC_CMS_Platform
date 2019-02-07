<?php

	class apiController {
		public $response = array();

		/**
		 *  Constructor
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			$this->response['errors'] = array();
			$this->response['data'] = array();
			$this->response['success'] = 1;
			$this->response['msg'] = array();
		} // function __construct
	
		/**
		 *  Process form
		 *  Args: none
		 *  Return: none
		 */
		public function processForm() {
			if (getPost('formSubmit')) {
				if (is_null(getPost('captcha')) || captcha::validateCaptcha(getPost('captcha'), false)) {
					$request = $_POST;
					if (isset($request['responseEmail'])) {
						$emails = $request['responseEmail'];
					} else {
						$emails = false;
					}
					$fp = new formProcessor;
					if (isset($request['_survey'])) {
						$type = 'survey/'.($request['_survey'] ? $request['_survey'] : 'unknown');
						unset($request['_survey']);
					} else {
						if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
							$type = 'popForm'.parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
						} else {
							$type = 'popForm/unknown';
						}
					}
					if ($fp->processRequest($request, $emails, $type)) {
						$this->response['msg'][] = 'Your form has been submitted.  Thank you for your interest.';
					} else {
						$this->response['errors'] = $fp->getErrors();
					}
				} else {
					$this->response['errors'][] = 'The image text that you entered is incorrect';
				}
			}
			$this->_render();
		} // function processForm	

		/**
		 *  Render resonpse
		 *  Args: none
		 *  Return: none
		 */
		private function _render(){
			header('Content-Type: application/json');
			echo json_encode($this->response);
		} // function _render
	} // class apiController

?>
