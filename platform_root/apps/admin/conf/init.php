<?php

	require_once 'conf.php';
	require_once SYSTEM_ROOT.'base/functions.lib.php';

	/**
	 *  This function helps to autoload a class without explicitly requiring it
	 *  Args: (str) class name
	 *  Return: none
	 */
	function autoload($class_name) {
		if (strpos($class_name, 'Smarty') === false) {
			require_once $class_name.'.php';
		}
	} // function autoload

	spl_autoload_register('autoload');

	if (PHP_SAPI != 'cli') {
		require_once SYSTEM_ROOT.'classes/sessionMessage.php';

		// initialize and set session handler
		register_shutdown_function('session_write_close');
		sessionHandlerDB::initialize();
		sessionHandlerDB::setHandler();

		// start session
		session_start();
	}

?>
