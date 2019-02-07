<?php

	require_once dirname(__DIR__).'/conf/init.php';
	require_once 'bController.php';
	if (ADMIN) {
		require_once 'bAdminController.php';
	}

	ob_start();
	if ($_class = getGet('_c')) {
		$_class .= 'Controller';
		$_method = getGet('_m');
		if (empty($_method)) {
			$_method = 'index';
		}
	} else {
		$_class = DEFAULT_CONTROLLER.'Controller';
		$_method = 'index';
	}
	$_controllerFile = CONTROLLER_DIR.$_class.'.php';
	
	try {
		if (is_file($_controllerFile)) {
			require_once $_controllerFile;
			$_controller = new $_class;
			
			if (strpos($_method, '_') !== 0 && method_exists($_controller, $_method) && is_callable(array($_controller, $_method))) {
				$_controller->$_method();
			} else {
				throw new Exception('404');
			}
		} else {

			require_once CONTROLLER_DIR.DEFAULT_CONTROLLER.'Controller.php';
			$_class = DEFAULT_CONTROLLER.'Controller';
			$_controller = new $_class;
			if (ADMIN) {
				$_method = getGet('_c');
				
				if (strpos($_method, '_') !== 0 && method_exists($_controller, $_method) && is_callable(array($_controller, $_method))) {
					$_controller->$_method();
				} else {
					error_reporting(E_ALL); ini_set('display_errors', 1);
					echo $_class . " : " . $_method; exit;
					throw new Exception('404');
				}
			} else {
				$_method = getGet('_c');
				if (strpos($_method, '_') !== 0 && method_exists($_controller, $_method) && is_callable(array($_controller, $_method))) {
					$_controller->$_method();
				} else {
					$_controller->renderContent();
				}
			}
		}
	} catch (Exception $e) {
		require_once CONTROLLER_DIR.'errorController.php';
		$_controller = new errorController;
		ob_clean();
		$_controller->error($e->getMessage());
	}
	ob_end_flush();

?>
