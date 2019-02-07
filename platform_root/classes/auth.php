<?php

	class auth {
		// session index
		protected $sessionIndex;
		// auth session
		protected $session;
		// model name
		protected $model;
		// login url
		protected $loginURL;

		/**
		 *  Generate a random password
		 *  Args: (int) length of password, (int) password strength
		 *  Returns: (str) password
		 */
		public static function generatePassword($length = 9, $strength = 4) {
			$requireCaps = false;
			$requireNums = false;
			$requireOther = false;
			$availableChars = array();
			$chars = 'abcdefghijklmnopqrstuvwxyz';
			$availableChars[1] = 'chars';
			if ($strength >= 2) {
				$caps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$requireCaps = true;
				$availableChars[2] = 'caps';
			}
			if ($strength >= 3) {
				$nums = '0123456789';
				$requireNums = true;
				$availableChars[3] = 'nums';
			}
			if ($strength >= 4) {
				$others = '@#$%!&-_;';
				$requireOther = true;
				$availableChars[4] = 'others';
			}
			$passwordIndex = array();
			$passwordIndex[] = 'chars';
			if ($requireCaps) {
				$passwordIndex[] = 'caps';
			}
			if ($requireNums) {
				$passwordIndex[] = 'nums';
			}
			if ($requireOther) {
				$passwordIndex[] = 'others';
			}
			$indexLength = $length - count($passwordIndex);
			$numAvail = count($availableChars);
			for ($i = 0; $i < $indexLength; $i++) {
				$passwordIndex[] = $availableChars[rand(1, $numAvail)];
			}
			shuffle($passwordIndex);
			$password = '';
			foreach ($passwordIndex as $charset) {
				$password .= ${$charset}[(rand() % strlen($$charset))];
			}
			return $password;
		} // function generatePassword
	
		/**
		 *  Return random hex string
		 *  Args: (int) salt length
		 *  Returns: (str) salt
		 */
		public static function getSalt($saltLength) {
			$chars = '0123456789abcdef';
			$length = strlen($chars) - 1;
			$salt = '';
			for ($i = 0; $i < $saltLength; $i++) {
				$salt .= $chars[rand(0, $length)];
			}
			return $salt;
		} // function getSalt
	
		/**
		 *  Return password hash
		 *  Args: (str) password, (str) salt, (int) loops
		 *  Returns: (str) hash
		 */
		public static function generatePasswordHash($password, $salt = false, $loops = false) {
			if (empty($salt)) {
				$salt = self::getSalt(6);
			}
			if (!$loops) {
				$loops = rand(1, 100);
			}
			$hash = $password;
			for ($i = 0; $i < $loops; $i++) {
				$hash = hash('sha256', $hash.$salt);
			}
			$hash = $hash.str_pad($loops, 3, 0, STR_PAD_LEFT).$salt;
			return $hash;
		} // function generatePasswordHash

		/**
		 *  Link local auth session
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			if (!isset($_SESSION[$this->sessionIndex])) {
				$_SESSION[$this->sessionIndex] = array();
			}
			$this->session = &$_SESSION[$this->sessionIndex];
		} // function __construct

		/**
		 *  Retrieve an auth variable
		 *  Args: (str) variable name
		 *  Return: (mixed) variable value
		 */
		public function get($variable) {
			if (isset($this->$variable)) {
				return $this->$variable;
			}
			return NULL;
		} // function get

		/**
		 *  Retrieve a authenticated user variable
		 *  Args: (str) variable name
		 *  Return: (mixed) variable value
		 */
		public function getUserInfo($field) {
			if (!empty($this->session)) {
				if (isset($this->session['user'][$field])) {
					return $this->session['user'][$field];
				}
			}
			return NULL;
		} // function getUserInfo

		/**
		 *  Return sessioned auth info
		 *  Args: (str) session index
		 *  Return: (mixed) array auth info or NULL
		 */
		public function getAuthInfo($field = false) {
			if (!empty($this->session)) {
				if (!$field) {
					return $this->session;
				} elseif (isset($this->session[$field])) {
					return $this->session[$field];
				}
			}
			return NULL;
		} // function getAuthInfo

		/**
		 *  Validate and auth info
		 *  Args: (str) login, (str) password, (boolean) bypass authentication
		 *  Return: (boolean) successful login
		 */
		public function login($login, $pass, $bypassAuth = false) {
			if ($login && $pass) {
				$model = call_user_func($this->model.'::getObject', array('login' => $login));
				if ($model && $model->exists()) {
					if ($model->status == 'active') {
						$session = $model->auth($pass, $bypassAuth);
						if (!empty($session)) {
							if (isset($this->session['loginSource'])) {
								$session['loginSource'] = $this->session['loginSource'];
							}
							$this->session = $session;
							$this->session['validated'] = true;
							return true;
						} else {
							addError('Your login / password does not match');
						}
					} else {
					  addError('Your account is no longer active');
					}
				} else {
					addError('Your login could not be found');
				}
			} else {
				addError('Invalid login or password provided');
			}
			return false;
		} // function login

		/**
		 *  Update existing auth info
		 *  Args: (array) auth info
		 *  Return: none
		 */
		public function updateAuth($auth) {
			$this->session = $auth;
			$this->session['validated'] = true;
		} // function updateAuth

		/**
		 *  Logout and redirect to login url
		 *  Args: (boolean) redirect
		 *  Return: none
		 */
		public function logout($redirect = true) {
			$this->session = NULL;
			if ($redirect) {
				redirect($this->loginURL);
			}
		} // function logout

		/**
		 *  Verify auth info
		 *  Args: none
		 *  Return: (boolean) valid admin user
		 */
		public function validate() {
			if (isset($this->session['validated']) && !empty($this->session['validated'])) {
				return true;
			} else {
				$this->session = array();
				if (PAGE_URL != $this->loginURL) {
					if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
						$this->session['loginSource'] = $_SERVER['REQUEST_URI'];
					}
				}
				return false;
			}
		} // function validate

		/**
		 *  Verify auth access
		 *  Args: (str) access section, (str) redirect url
		 *  Return: none
		 */
		public function checkAccess($section = false, $redirect = false) {
			if (!$this->validate()) {
				redirect($this->loginURL);
			} elseif (!empty($section)) {
				$auth = $this->getAuthInfo();
				if (!isset($auth['access'][$section]) || !$auth['access'][$section]) {
					addError('You do not have access to that section');
					redirect($redirect ? $redirect : $this->loginURL);
				}
			}
		} // function checkAccess

		/**
		 *  Check if user is logged in
		 *  Args: none
		 *  Return: (boolean) user logged in
		 */
		public function isLoggedIn() {
			if (isset($this->session['validated']) && !empty($this->session['validated'])) {
				return true;
			} else {
				return false;
			}
		} // function isLoggedIn
	} // class auth

?>
