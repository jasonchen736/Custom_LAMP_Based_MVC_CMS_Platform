<?php

	define('DB_HOST', 'localhost');
	define('DB_PORT', 3306);
	define('DB_USER', '');
	define('DB_PASS', '');
	define('DB_NAME', '');

	define('MAIL_PROTOCOL', 'nativemail');
	define('MAIL_SERVER', '');
	define('MAIL_PORT', 25);
	define('MAIL_AUTHENTICATION', false);
	define('MAIL_USER', '');
	define('MAIL_PASSWORD', '');

	define('SESSION_DURATION', 10800);

	define('ADMIN', false);
	define('DEVELOPMENT', false);

	if (isset($_SERVER['SCRIPT_URL'])) {
		define('PAGE_URL', $_SERVER['SCRIPT_URL']);
	} elseif (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
		define('PAGE_URL', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	} elseif (isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) {
		define('PAGE_URL', parse_url($_SERVER['REDIRECT_URL'], PHP_URL_PATH));
	} else {
		define('PAGE_URL', '/');
	}

	define('SYSTEM_ROOT', dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR);
	define('APP_ROOT', dirname(__DIR__).DIRECTORY_SEPARATOR);

	define('CONTROLLER_DIR', APP_ROOT.'controllers'.DIRECTORY_SEPARATOR);
	define('TEMPLATE_DIR', APP_ROOT.'templates'.DIRECTORY_SEPARATOR);
	define('IMAGE_DIR', APP_ROOT.'www'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR);
	set_include_path(SYSTEM_ROOT.'base'.PATH_SEPARATOR.SYSTEM_ROOT.'library'.PATH_SEPARATOR.SYSTEM_ROOT.'classes'.PATH_SEPARATOR.SYSTEM_ROOT.'models'.PATH_SEPARATOR.SYSTEM_ROOT.'dataViews'.PATH_SEPARATOR);

	date_default_timezone_set('America/New_York');

	define('DEFAULT_CONTROLLER', 'default');

?>
