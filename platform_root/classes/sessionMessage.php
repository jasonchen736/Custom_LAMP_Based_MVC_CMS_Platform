<?php

	/**
	 *  This class provides an interface to access system messages and errored fields
	 *  This class is essentially a wrapper for the following session arrays:
	 *    main messages array
	 *      $_SESSION['_sessionMessage']
	 *    messages generated at run time
	 *      $_SESSION['_sessionMessage']['errorMessages']
	 *      $_SESSION['_sessionMessage']['successMessages']
	 *      $_SESSION['_sessionMessage']['generalMessages']
	 *    errored field names
	 *      $_SESSION['_sessionMessage']['errorFields']
	 */
	class sessionMessage {
		/**
		 *  Initialize message arrays
		 *  Args: none
		 *  Return: none
		 */
		public static function initialize() {
			$_SESSION['_sessionMessage'] = array();
			$_SESSION['_sessionMessage']['errorMessages'] = array();
			$_SESSION['_sessionMessage']['successMessages'] = array();
			$_SESSION['_sessionMessage']['generalMessages'] = array();
			$_SESSION['_sessionMessage']['errorFields'] = array();
		} // function initialize

		/**
		 *  Returns true if requested message array is not empty
		 *  Args: (str) type of message
		 *  Return: (boolean) error array is not empty
		 */
		public static function haveMessages($type) {
			if (isset($_SESSION['_sessionMessage'])) {
				switch ($type) {
					case 'error':
						if (!empty($_SESSION['_sessionMessage']['errorMessages'])) {
							return true;
						}
						break;
					case 'success':
						if (!empty($_SESSION['_sessionMessage']['successMessages'])) {
							return true;
						}
						break;
					case 'general':
						if (!empty($_SESSION['_sessionMessage']['generalMessages'])) {
							return true;
						}
						break;
					default:
						break;
				}
			}
			return false;
		} // function haveErrors

		/**
		 *  Retrieve messages of requested type
		 *  Args: (string) type of message
		 *  Return: (array) requested messages array
		 */
		public static function getMessages($type) {
			$messages = array();
			if (isset($_SESSION['_sessionMessage'])) {
				switch ($type) {
					case 'error':
						$messages = $_SESSION['_sessionMessage']['errorMessages'];
						break;
					case 'success':
						$messages = $_SESSION['_sessionMessage']['successMessages'];
						break;
					case 'general':
						$messages = $_SESSION['_sessionMessage']['generalMessages'];
						break;
					default:
						break;
				}
			}
			return $messages;
		} // function getMessages

		/**
		 *  Clear the requested messages array
		 *  Args: (string) type of message
		 *  Return: none
		 */
		public static function clearMessages($type) {
			if (isset($_SESSION['_sessionMessage'])) {
				switch ($type) {
					case 'error':
						$_SESSION['_sessionMessage']['errorMessages'] = array();
						break;
					case 'success':
						$_SESSION['_sessionMessage']['successMessages'] = array();
						break;
					case 'general':
						$_SESSION['_sessionMessage']['generalMessages'] = array();
						break;
					default:
						break;
				}
				if (empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['errorFields'])) {
					unset($_SESSION['_sessionMessage']);
				}
			}
		} // function clearMessages

		/**
		 *  Add a message to the requested messages array
		 *  Args: (string) type of message, (string) message
		 *  Return: none
		 */
		public static function addMessage($type, $message) {
			if (!isset($_SESSION['_sessionMessage'])) {
				self::initialize();
			}
			$message = (string) $message;
			$key = md5($message);
			switch($type) {
				case 'error':
					$_SESSION['_sessionMessage']['errorMessages'][$key] = $message;
					break;
				case 'success':
					$_SESSION['_sessionMessage']['successMessages'][$key] = $message;
					break;
				case 'general':
					$_SESSION['_sessionMessage']['generalMessages'][$key] = $message;
					break;
				default:
					break;
			}
		} // function addMessage

		/**
		 *  Remove a message from the requested messages array
		 *  Args: (string) type of message, (string) message
		 *  Return: none
		 */
		public static function removeMessage($type, $message) {
			if (isset($_SESSION['_sessionMessage'])) {
				$message = (string) $message;
				$key = md5($message);
				switch($type) {
					case 'error':
						if (isset($_SESSION['_sessionMessage']['errorMessages'][$key])) {
							unset($_SESSION['_sessionMessage']['errorMessages'][$key]);
						}
						break;
					case 'success':
						if (isset($_SESSION['_sessionMessage']['successMessages'][$key])) {
							unset($_SESSION['_sessionMessage']['successMessages'][$key]);
						}
						break;
					case 'general':
						if (isset($_SESSION['_sessionMessage']['generalMessages'][$key])) {
							unset($_SESSION['_sessionMessage']['generalMessages'][$key]);
						}
						break;
					default:
						break;
				}
				if (empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['errorFields'])) {
					unset($_SESSION['_sessionMessage']);
				}
			}
		} // function removeMessage

		/**
		 *  Returns whether the error field var is empty
		 *  Args: none
		 *  Return: (boolean) true if !empty
		 */
		public static function haveErrorFields() {
			return isset($_SESSION['_sessionMessage']) && !empty($_SESSION['_sessionMessage']['errorFields']);
		} // function haveErrorFields

		/**
		 *  Return the error field array
		 *  Args: none
		 *  Return: (array) error fields
		 */
		public static function getErrorFields() {
			$fields = array();
			if (isset($_SESSION['_sessionMessage'])) {
				$fields = array_unique($_SESSION['_sessionMessage']['errorFields']);
			}
			return $fields;
		} // function getErrorFields

		/**
		 *  Clear the error fields array
		 *  Args: none
		 *  Return: none
		 */
		public static function clearErrorFields() {
			if (isset($_SESSION['_sessionMessage'])) {
				$_SESSION['_sessionMessage']['errorFields'] = array();
				if (empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['successMessages']) && 
					empty($_SESSION['_sessionMessage']['errorFields'])) {
					unset($_SESSION['_sessionMessage']);
				}
			}
		} // function clearErrorFields

		/**
		 *  Add an error field
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function addErrorField($field) {
			if (!isset($_SESSION['_sessionMessage'])) {
				self::initialize();
			}
			$field = (string) $field;
			$_SESSION['_sessionMessage']['errorFields'][$field] = $field;
		} // function addErrorField

		/**
		 *  Remove an error field
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function removeErrorField($field) {
			$field = (string) $field;
			if (isset($_SESSION['_sessionMessage']) && isset($_SESSION['_sessionMessage']['errorFields'][$field])) {
				unset($_SESSION['_sessionMessage']['errorFields'][$field]);
			}
		} // function removeErrorField
	} // class sessionMessage

	/**
	 *  Wrapper for sessionMessage::haveMessages('error')
	 *  Args: none
	 *  Return: (boolean) have error messages
	 */
	function haveErrors() {
		return sessionMessage::haveMessages('error');
	} // function haveErrors

	/**
	 *  Wrapper for sessionMessage::getMessages('error')
	 *  Args: none
	 *  Return: (array) error messages array
	 */
	function getErrors() {
		return sessionMessage::getMessages('error');
	} // function getErrors

	/**
	 *  Wrapper for sessionMessage::clearMessages('error')
	 *  Args: none
	 *  Return: none
	 */
	function clearErrors() {
		sessionMessage::clearMessages('error');
	} // function clearErrors

	/**
	 *  Wrapper for sessionMessage:addMessage('error', $message)
	 *  Args: (string) error message
	 *  Return: none
	 */
	function addError($message) {
		sessionMessage::addMessage('error', $message);
	} // function addError

	/**
	 *  Wrapper for sessionMessage::removeMessage('error', $message)
	 *  Args: (string) error message
	 *  Return: none
	 */
	function removeError($message) {
		sessionMessage::removeMessage('error', $message);
	} // function removeError

	/**
	 *  Wrapper for sessionMessage::haveMessages('success')
	 *  Args: none
	 *  Return: (boolean) have success messages
	 */
	function haveSuccess() {
		return sessionMessage::haveMessages('success');
	} // function haveSuccess

	/**
	 *  Wrapper for sessionMessage::getMessages('success')
	 *  Args: none
	 *  Return: (array) success messages array
	 */
	function getSuccess() {
		return sessionMessage::getMessages('success');
	} // function getSuccess

	/**
	 *  Wrapper for sessionMessage::clearMessages('success')
	 *  Args: none
	 *  Return: none
	 */
	function clearSuccess() {
		sessionMessage::clearMessages('success');
	} // function clearSuccess

	/**
	 *  Wrapper for sessionMessage:addMessage('success', $message)
	 *  Args: (string) success message
	 *  Return: none
	 */
	function addSuccess($message) {
		sessionMessage::addMessage('success', $message);
	} // function addSuccess

	/**
	 *  Wrapper for sessionMessage::removeMessage('success', $message)
	 *  Args: (string) success message
	 *  Return: none
	 */
	function removeSuccess($message) {
		sessionMessage::removeMessage('success', $message);
	} // function removeSucces

	/**
	 *  Wrapper for sessionMessage::haveMessages('general')
	 *  Args: none
	 *  Return: (boolean) have genteral messages
	 */
	function haveMessages() {
		return sessionMessage::haveMessages('general');
	} // function haveMessages

	/**
	 *  Wrapper for sessionMessage::getMessages('general')
	 *  Args: none
	 *  Return: (array) general messages array
	 */
	function getMessages() {
		return sessionMessage::getMessages('general');
	} // function getMessages

	/**
	 *  Wrapper for sessionMessage::clearMessages('general')
	 *  Args: none
	 *  Return: none
	 */
	function clearMessages() {
		sessionMessage::clearMessages('general');
	} // function clearMessages

	/**
	 *  Wrapper for sessionMessage:addMessage('general', $message)
	 *  Args: (string) general message
	 *  Return: none
	 */
	function addMessage($message) {
		sessionMessage::addMessage('general', $message);
	} // function addMessage

	/**
	 *  Wrapper for sessionMessage::removeMessage('general', $message)
	 *  Args: (string) general message
	 *  Return: none
	 */
	function removeMessage($message) {
		sessionMessage::removeMessage('general', $message);
	} // function removeMessage

	/**
	 *  Wrapper for sessionMessage::haveErrorFields()
	 *  Args: none
	 *  Return: (boolean) true if !empty
	 */
	function haveErrorFields() {
		return sessionMessage::haveErrorFields();
	} // function haveErrorFields

	/**
	 *  Wrapper for sessionMessage::getErrorFields()
	 *  Args: none
	 *  Return: (array) error fields
	 */
	function getErrorFields() {
		return sessionMessage::getErrorFields();
	} // function getErrorFields

	/**
	 *  Wrapper for sessionMessage::clearErrorFields()
	 *  Args: none
	 *  Return: none
	 */
	function clearErrorFields() {
		sessionMessage::clearErrorFields();
	} // function clearErrorFields

	/**
	 *  Wrapper for sessionMessage::addErrorField($field)
	 *  Args: (str) field name
	 *  Return: none
	 */
	function addErrorField($field) {
		sessionMessage::addErrorField($field);
	} // function addErrorField

	/**
	 *  Wrapper for sessionMessage::removeErrorField($field)
	 *  Args: (str) field name
	 *  Return: none
	 */
	function removeErrorField($field) {
		sessionMessage::removeErrorField($field);
	} // function removeErrorField

	/**
	 *  Clears all error, success, general messages as well as error fields
	 *  Args: none
	 *  Return: none
	 */
	function clearAllMessages() {
		clearErrors();
		clearSuccess();
		clearMessages();
		clearErrorFields();
	} // function clearAllMessages

?>
