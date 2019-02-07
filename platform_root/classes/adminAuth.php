<?php

	class adminAuth extends auth {
		// session index
		protected $sessionIndex = 'admin';
		// auth session
		protected $session;
		// model name
		protected $model = 'adminUser';
		// login url
		protected $loginURL = '/login';
		// instance
		public static $auth;

		/**
		 *  Get instance of admin auth
		 *  Args: none
		 *  Return: none
		 */
		public static function getInstance() {
			if (empty(self::$auth)) {
				self::$auth = new adminAuth;
			}
			return self::$auth;
		} // function getInstance
	} // class adminAuth

?>
