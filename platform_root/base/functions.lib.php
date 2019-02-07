<?php

	/**
	 *  Retrieves a request field value
	 *  Args: (str) request field
	 *  Return: (str) request field value
	 */
	function getRequest($field) {
		if (isset($_REQUEST[$field])) {
			return $_REQUEST[$field];
		}
		return NULL;
	} // function getRequest

	/**
	 *  Retrieves a post field value
	 *  Args: (str) post field
	 *  Return: (str) post field value
	 */
	function getPost($field, $type = false) {
		if (isset($_POST[$field])) {
			return $_POST[$field];
		}
		return NULL;
	} // function getPost

	/**
	 *  Retrieves a get field value
	 *  Args: (str) get field
	 *  Return: (str) get field value
	 */
	function getGet($field, $type = false) {
		if (isset($_GET[$field])) {
			return $_GET[$field];
		}
		return NULL;
	} // function getGet

	/**
	 *  Retrieves a cookie field value
	 *  Args: (str) cookie field
	 *  Return: (str) cookie field value
	 */
	function getCookie($field, $type = false) {
		if (isset($_COOKIE[$field])) {
			return $_COOKIE[$field];
		}
		return NULL;
	} // function getCookie

	/**
	 *  Retrieves a session field value
	 *  Args: (str) session field
	 *  Return: (str) session field value
	 */
	function getSession($field, $type = false) {
		if (isset($_SESSION[$field])) {
			return $_SESSION[$field];
		}
		return NULL;
	} // function getSession

	/**
	 *  Redirects to page passed
	 *  Args: (str) page url
	 *  Returns: none
	 */
	function redirect($page) {
		header('Location: '.$page);
		exit();
	} // function redirect

	/**
	 *  Execute a query with global database object and return result object
	 *  Args: (str) query, (array) params
	 *  Returns: (result) result object
	 */
	function query($query, $params = array()) {
		$dbh = database::getInstance();
		$result = $dbh->query($query, $params);
		return $result;
	} // function query

?>
