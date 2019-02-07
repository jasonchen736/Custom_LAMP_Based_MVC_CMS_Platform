<?php

	class fileHandler {
		// file directory
		private $fileDir = false;
		// file data
		private $fileData = false;
		// errors array
		protected static $errors = array();
		protected static $errorFields = array();

		/**
		 *  Add an item to the error array
		 *  Args: (str) error message, (str) error index
		 *  Return: none
		 */
		public static function addError($error, $index = false) {
			if ($index !== false) {
				self::$errors[$index] = $error;
			} else {
				self::$errors[] = $error;
			}
		} // function addError

		/**
		 *  Add an item from the error array by a known index
		 *  Args: (str) error index
		 *  Return: none
		 */
		public static function removeError($index) {
			if (isset(self::$errors[$index])) {
				unset(self::$errors[$index]);
			}
		} // function removeError

		/**
		 *  Clear the error array
		 *  Args: none
		 *  Return: none
		 */
		public static function clearErrors() {
			self::$errors = array();
		} // function clearErrors

		/**
		 *  Retrieve error array
		 *  Args: none
		 *  Return: (array) error array
		 */
		public static function getErrors() {
			return self::$errors;
		} // function getErrors

		/**
		 *  Add a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function addErrorField($fieldName) {
			self::$errorFields[$fieldName] = true;
		} // function addErrorField

		/**
		 *  Remove a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function removeErrorField($fieldName) {
			if (isset(self::$errorFields[$fieldName])) {
				unset(self::$errorFields[$fieldName]);
			}
		} // function removeErrorField

		/**
		 *  Clear the error fields array
		 *  Args: none
		 *  Return: none
		 */
		public static function clearErrorFields() {
			self::$errorFields = array();
		} // function clearErrorFields

		/**
		 *  Retrieve error fields array
		 *  Args: none
		 *  Return: (array) error fields array
		 */
		public static function getErrorFields() {
			return self::$errorFields;
		} // function getErrorFields

		/**
		 *  Push internal error or error fields array to system messages
		 *  Args: (str) array to push
		 *  Return: none
		 */
		public static function updateSystemMessages($type = false) {
			if (!$type || $type == 'errors') {
				$errors = self::$getErrors();
				foreach ($errors as $error) {
					addError($error);
				}
			}
			if (!$type || $type == 'errorFields') {
				$errorFields = self::$getErrorFields();
				foreach ($errorFields as $errorField => $val) {
					addErrorField($errorField);
				}
			}
		} // function updateSystemMessages

		/**
		 *  Populate file information either from file upload or local source
		 *  Args: (str) file name or index of $_FILES upload, (str) file source
		 *  Args: (str) local source directory
		 *  Return: none
		 */
		public function __construct($file, $source = 'upload', $dir = false) {
			if (!$dir) {
				$dir = '';
			}
			switch ($source) {
				case 'param':
					$this->fileData = $file;
					break;
				case 'file':
					if (file_exists($dir.$file)) {
						$this->fileData = array();
						$this->fileData['name'] = $file;
						$this->fileData['type'] = NULL;
						$this->fileData['tmp_name'] = $dir.$file;
						$this->fileData['error'] = 0;
						$this->fileData['size'] = round(filesize($dir.$file)/1048576, 2);
					}
					break;
				case 'upload':
				default:
					if (isset($_FILES[$file])) {
						$this->fileData = $_FILES[$file];
					}
					break;
			}
			$this->fileDir = $dir;
		} // function __construct

		/**
		 *  Return file data array
		 *  Args: none
		 *  Return: (array) file data
		 */
		public function getFileData() {
			return $this->fileData;
		} // function getFileData

		/**
		 *  Move an uploaded file from tmp to destination
		 *  Args: (str) destination directory, (str) file name
		 *  Return: (boolean) success
		 */
		public function moveFile($destination, $name = false) {
			if ($this->fileData) {
				if (!$name) {
					$name = $this->fileData['name'];
				}
				if (!preg_match('/\/$/', $destination)) {
					$destination .= '/';
				}
				$name = preg_replace('/\s+/', '_', $name);
				if (move_uploaded_file($this->fileData['tmp_name'], $destination.$name)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} // function moveFile

		/**
		 *  Rename file
		 *  Args: (str) new name
		 *  Return: (boolean) success
		 */
		public function rename($new) {
			if (!file_exists($this->fileDir.'/'.$new)) {
				if (rename($this->fileData['tmp_name'], $this->fileDir.'/'.$new)) {
					return true;
				} else {
					$this->addError('There was an error renaming the file, please try again');
				}
			} else {
				$this->addError('That file name already exists');
				$this->addErrorField('rename');
			}
			return false;
		} // function rename

		/**
		 *  Delete file
		 *  Args: none
		 *  Return: (boolean) success
		 */
		public function delete() {
			return unlink($this->fileData['tmp_name']);
		} // function delete
	} // class fileHandler

?>
