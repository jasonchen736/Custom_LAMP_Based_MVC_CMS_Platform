<?php

	class defaultController extends bAdminController {
		/**
		 *  Admin home
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$this->assignClean('_TITLE', 'Admin');
			$this->renderAdmin('index.tpl');
		} // function index

		/**
		 *  Login page
		 *  Args: none
		 *  Return: none
		 */
		public function login() {
			if ($this->auth->isLoggedIn()) {
				redirect('/');
			}
			$login = getPost('login');
			$pass = getPost('pass');
			if (getPost('submit')) {
				$sourceURL = $this->auth->getAuthInfo('loginSource');
				if ($this->auth->login($login, $pass)) {
					if (!empty($sourceURL)) {
						redirect($sourceURL);
					} else {
						redirect('/');
					}
				}
			}
			$this->assignClean('_TITLE', 'Admin');
			$this->assignClean('login', $login);
			$this->assignClean('pass', $pass);
			$this->renderAdmin('login.tpl');
		} // function login

		/**
		 *  Log out
		 *  Args: none
		 *  Return: none
		 */
		public function logout() {
			$auth = new adminAuth;
			$auth->logout();
		} // function logout
	} // function defaultController

?>
