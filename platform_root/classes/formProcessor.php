<?php

	class formProcessor {
		private $errors = array();

		/**
		 *  Process form
		 *  Args: (array) request, (str) response email name, (str) form type
		 *  Return: (boolean) success
		 */
		public function processRequest($request, $responseEmail = false, $type = false) {
			$success = true;
			if (isset($request['formSubmit'])) {
				unset($request['formSubmit']);
			}
			if (isset($request['redirect'])) {
				unset($request['redirect']);
			}
			if (isset($request['responseEmail'])) {
				unset($request['responseEmail']);
			}
			if (!isset($request['captcha']) || captcha::validateCaptcha(isset($request['captcha']) ? $request['captcha'] : false)) {
				if (isset($request['captcha'])) {
					unset($request['captcha']);
				}
				$formData = new formData;
				$formData->languageID = language::getCurrent('languageID');
				if (isset($request['first'])) {
					$formData->first = $request['first'];
					unset($request['first']);
				}
				if (isset($request['last'])) {
					$formData->last = $request['last'];
					unset($request['last']);
				}
				if (isset($request['email'])) {
					$formData->email = $request['email'];
					unset($request['email']);
				}
				if ($type) {
					$formData->type = $type;
				} else {
					$formData->type = trim(PAGE_URL, '/');
				}
				// check if additional info field has been injected, not for saving
				$hasAdditionalInfo = isset($request['additionalInfo']);
				$request = $this->processSpecialFields($request);
				if ($request) {
					$encode = $request;
					if (!$hasAdditionalInfo && isset($encode['additionalInfo'])) {
						unset($encode['additionalInfo']);
					}
					$formData->data = json_encode($encode);
				}
				if (!$formData->save()) {
					$success = false;
					$this->errors = $formData->getErrors();
				} elseif ($responseEmail) {
					$template = new template;
					$template->assignClean('first', $formData->first);
					$template->assignClean('last', $formData->last);
					$template->assignClean('email', $formData->email);
					$template->assignClean('source', $formData->source);
					foreach ($request as $f => $v) {
						$template->assignClean($f, is_array($v) ? implode(', ', $v) : $v);
					}
					if (!is_array($responseEmail)) {
						$responseEmail = array($responseEmail);
					}
					foreach ($responseEmail as $emailName) {
						$email = email::getObject(array('name' => $emailName));
						if ($email && $email->exists()) {
							$mailer = $template->getMailer($email->name);
							$recipients = json_decode($email->recipients);
							if (empty($recipients)) {
								if ($mailer->composeMessage()) {
									$mailer->addRecipient($formData->email);
									$mailer->send();
								}
							} else {
								if ($mailer->composeMessage()) {
									$send = false;
									foreach ($recipients as $recipient) {
										if ($recipient[1] && $recipient[2] !== '') {
											if (isset($request[$recipient[1]]) && $request[$recipient[1]] == $recipient[2]) {
												$mailer->addRecipient($recipient[0]);
												$send = true;
											}
										} elseif ($recipient[1]) {
											if (isset($request[$recipient[1]])) {
												$mailer->addRecipient($recipient[0]);
												$send = true;
											}
										} else {
											$mailer->addRecipient($recipient[0]);
											$send = true;
										}
									}
									if ($send) {
										$mailer->send();
									}
								}
							}
						}
					}
				}
			} else {
				$success = false;
				$this->errors[] = 'The image text that you entered was incorrect';
			}
			return $success;
		} // function processRequest

		/**
		 *  Retrieve processing errors
		 *  Args: none
		 *  Return: (array) errors
		 */
		public function getErrors() {
			return $this->errors;
		} // function getErrors
	} // class formProcessor

?>
