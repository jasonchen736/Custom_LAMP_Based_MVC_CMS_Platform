<?php

        require_once dirname(__DIR__).'/conf/init.php';

	if (isset($_POST['captcha'])) {
		if (captcha::validateCaptcha(getPost('captcha'), false)) {
			$valid = 1;
		} else {
			$valid = 0;
		}
		echo $valid;
	} else {
		$captchaConfig = array(
			'TTF_folder' => SYSTEM_ROOT.'/fonts/',
			'chars' => 5,
			'minsize' => 20,
			'maxsize' => 30,
			'maxrotation' => 25,
			'noise' => true
		);
		$captcha = new captcha($captchaConfig);
		$captcha->generateCaptcha();
	}

?>
