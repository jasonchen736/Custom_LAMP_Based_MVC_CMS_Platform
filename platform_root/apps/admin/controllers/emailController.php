<?php

	class emailController extends bAdminController {
		/**
		 *  Access control
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			parent::__construct();
			$this->auth->checkAccess('CONTENT');
		} // function __construct

		/**
		 *  Show the Email overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewEmail::processOverviewAction($recordOverviewAction, 'email', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$headers = emailSection::getSections('header');
			$headers[0] = 'None';
			ksort($headers);
			$footers = emailSection::getSections('footer');
			$footers[0] = 'None';
			ksort($footers);
			$view = new dataViewEmail;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Email Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('headers', $headers);
			$this->assignClean('footers', $footers);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assignClean('hasExport', false);
			$this->renderAdmin('email.tpl');	
		} // function index

		/**
		 *  Add new Email section
		 *  Args: none
		 *  Return: none
		 */
		public function addEmail() {
			return $this->editEmail();
		} // functon addEmail

		/**
		 *  Edit Email section
		 *  Args: (mixed) Email object or Email id
		 *  Return: none
		 */
		public function editEmail($editEmail = false, $mode = 'add') {
			if (!$editEmail) {
				$editEmail = (int) getRequest('emailID');
			}
			if (!$editEmail) {
				$editEmail = new email;
			} else {
				if (is_numeric($editEmail)) {
					$editEmail = new email($editEmail);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$editEmail->exists() || $editEmail->languageID != language::getCurrent('languageID'))) {
					addError('That Email does not exist');
					redirect('/email');
				}
			}
			$headers = emailSection::getSections('header');
			$headers[0] = 'None';
			ksort($headers);
			$footers = emailSection::getSections('footer');
			$footers[0] = 'None';
			ksort($footers);
			$this->assignClean('_TITLE', 'Email Admin');
			$this->assignClean('editEmail', $editEmail->toArray());
			$this->assign('html', $editEmail->html);
			$this->assign('text', $editEmail->text);
			$this->assign('recipients', json_decode($editEmail->recipients));
			$this->assignClean('headers', $headers);
			$this->assignClean('footers', $footers);
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('emailEdit.tpl');
		} // function editEmail

		/**
		 *  Save a new Email record
		 *  Args: (obj) email
		 *  Return: none
		 */
		public function saveEmail($editEmail = false) {
			if (!$editEmail) {
				$editEmail = new email;
			}
			$update = $editEmail->exists() ? true : false;
			$editEmail->languageID = getPost('languageID');
			$editEmail->name = getPost('name');
			$editEmail->subject = getPost('subject');
			$editEmail->html = preg_replace('/ mce_[^=]*="[^"]*"/', '', getPost('html'));
			$editEmail->text = getPost('text');
			$editEmail->fromEmail = getPost('fromEmail');
			$editEmail->headerID = getPost('headerID');
			$editEmail->footerID = getPost('footerID');
			$emails = getPost('recipient');
			$conditions = getPost('condition');
			$values = getPost('value');
			$recipients = array();
			foreach ($emails as $k => $v) {
				if ($v) {
					$recipients[] = array($v, isset($conditions[$k]) ? $conditions[$k] : '', isset($values[$k]) ? $values[$k] : '');
				}
			}
			$editEmail->recipients = json_encode($recipients);
			if ($editEmail->save()) {
				$this->clearCompiledTemplates($editEmail);
				addSuccess('Email saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/email/editEmail?emailID='.$editEmail->emailID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/email/addEmail');
				}
			} else {
				addError('An error occurred while saving the Email');
				$editEmail->updateSessionMessage();
			}
			return $this->editEmail($editEmail, $update ? 'edit' : 'add');
		} // function saveEmail

		/**
		 *  Update an existing Email record
		 *  Args: none
		 *  Return: none
		 */
		public function updateEmail() {
			$editEmail = new email(getRequest('emailID'));
			if ($editEmail->exists()) {
				return $this->saveEmail($editEmail);
			} else {
				addError('That Email does not exist');
				redirect('/email');
			}
		} // function updateEmail		

		/**
		 *  Display an email preview
		 *  Args: none
		 *  Return: none
		 */
		public function preview() {
			$id = (int) getRequest('emailID');
			$email = new email($id);
			headers::sendNoCacheHeaders();
			if ($email->exists()) {
				$mailer = $this->getMailer($email->name);
				$this->assignClean('_TITLE', 'Preview Email: '.$email->name);
				$this->assign('html', $mailer->get('html'));
				$this->assign('text', $mailer->get('text'));
				$this->renderView('emailPreview.tpl');
			} else {
				echo 'Email template not found';
			}
		} // function preview

		/**
		 *  Send email form and action
		 *  Args: none
		 *  Return: none
		 */
		public function sendEmail() {
			$id = (int) getRequest('emailID');
			$email = new email($id);
			if ($email->exists()) {
				// retrieve smarty tags
				$subject = $email->subject;
				$html = $email->html;
				$text = $email->text;
				preg_match_all('/{\$([\w\d_\-\.]+)}/', $subject, $subjectTagsA);
				preg_match_all('/{\$([\w\d_\-\.]+)}/', $html, $htmlTagsA);
				preg_match_all('/{\$([\w\d_\-\.]+)}/', $text, $textTagsA);
				$headerID = $email->headerID;
				if ($headerID) {
					$emailHeader = new emailSection($headerID);
					$headerhtml = $emailHeader->html;
					$headertext = $emailHeader->text;
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $headerhtml, $htmlHeaderTags);
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $headertext, $textHeaderTags);
				} else {
					$htmlHeaderTags = array(1 => array());
					$textHeaderTags = array(1 => array());
				}
				$footerID = $email->footerID;
				if ($footerID) {
					$emailFooter = new emailSection($footerID);
					$footerhtml = $emailFooter->html;
					$footertext = $emailFooter->text;
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $footerhtml, $htmlFooterTags);
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $footertext, $textFooterTags);
				} else {
					$htmlFooterTags = array(1 => array());
					$textFooterTags = array(1 => array());
				}
				// retrieve mailer tags
				$mailer = $this->getMailer($email->name);
				$subject = $mailer->get('subject');
				$html = $mailer->get('html');
				$text = $mailer->get('text');
				preg_match_all('/{([\w\d_\-\.]+)}/', $subject, $subjectTagsB);
				preg_match_all('/{([\w\d_\-\.]+)}/', $html, $htmlTagsB);
				preg_match_all('/{([\w\d_\-\.]+)}/', $text, $textTagsB);
				$emailTags = array_unique(array_merge($subjectTagsA[1], $htmlTagsA[1], $textTagsA[1], $subjectTagsB[1], $htmlTagsB[1], $textTagsB[1], $htmlHeaderTags[1], $textHeaderTags[1], $htmlFooterTags[1], $textFooterTags[1]));
				sort($emailTags);
				$this->assignClean('_TITLE', 'Send Email: '.$email->name);
				$this->assignClean('email', $email->toArray());
				$this->assignClean('emailTags', $emailTags);
				$this->renderAdmin('emailSend.tpl');
			} else {
				addError('Email not found');
				redirect('/email');
			}
		} // function sendEmail

		/**
		 *  Send email
		 *  Args: none
		 *  Return: none
		 */
		public function send() {
			$id = (int) getRequest('emailID');
			$email = new email($id);
			if ($email->exists()) {
				$address = getPost('email', 'email');
				if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
					// retrieve smarty tags
					$subject = $email->subject;
					$html = $email->html;
					$text = $email->text;
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $subject, $subjectTagsA);
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $html, $htmlTagsA);
					preg_match_all('/{\$([\w\d_\-\.]+)}/', $text, $textTagsA);
					$headerID = $email->headerID;
					if ($headerID) {
						$emailHeader = new emailSection($headerID);
						$headerhtml = $emailHeader->html;
						$headertext = $emailHeader->text;
						preg_match_all('/{\$([\w\d_\-\.]+)}/', $headerhtml, $htmlHeaderTags);
						preg_match_all('/{\$([\w\d_\-\.]+)}/', $headertext, $textHeaderTags);
					} else {
						$htmlHeaderTags = array(1 => array());
						$textHeaderTags = array(1 => array());
					}
					$footerID = $email->footerID;
					if ($footerID) {
						$emailFooter = new emailSection($footerID);
						$footerhtml = $emailFooter->html;
						$footertext = $emailFooter->text;
						preg_match_all('/{\$([\w\d_\-\.]+)}/', $footerhtml, $htmlFooterTags);
						preg_match_all('/{\$([\w\d_\-\.]+)}/', $footertext, $textFooterTags);
					} else {
						$htmlFooterTags = array(1 => array());
						$textFooterTags = array(1 => array());
					}
					$smartyTags = array_unique(array_merge($subjectTagsA[1], $htmlTagsA[1], $textTagsA[1], $htmlHeaderTags[1], $textHeaderTags[1], $htmlFooterTags[1], $textFooterTags[1]));
					// retrieve mailer tags
					$mailer = $this->getMailer($email->name);
					$subject = $mailer->get('subject');
					$html = $mailer->get('html');
					$text = $mailer->get('text');
					preg_match_all('/{([\w\d_\-\.]+)}/', $subject, $subjectTagsB);
					preg_match_all('/{([\w\d_\-\.]+)}/', $html, $htmlTagsB);
					preg_match_all('/{([\w\d_\-\.]+)}/', $text, $textTagsB);
					$mailerTags = array_unique(array_merge($subjectTagsB[1], $htmlTagsB[1], $textTagsB[1]));
					// assign smarty tags
					$arrays = array();
					foreach ($smartyTags as $tag) {
						if (!preg_match('/\./', $tag)) {
							$this->assign($tag, getPost('#'.$tag));
						} else {
							$vars = explode('.', $tag);
							if (!isset($arrays[$vars[0]])) {
								$arrays[$vars[0]] = array();
							}
							$arrays[$vars[0]][$vars[1]] = getPost('#'.preg_replace('/\./', '#', $tag));
						}
					}
					if (!empty($arrays)) {
						foreach ($arrays as $key => $val) {
							$this->assign($key, $val);
						}
					}
					$mailer = $this->getMailer($email->name);
					if ($mailer->composeMessage()) {
						$mailer->addRecipient($address);
						// assign mailer tags
						foreach ($mailerTags as $tag) {
							$mailer->assignTag($address, $tag, getPost('#'.preg_replace('/\./', '#', $tag)));
						}
						if ($mailer->send()) {
							addSuccess('Email sent');
						} else {
							addError('An error occurred while sending the email');
						}
					} else {
						addError('An error occurred while sending the email');
					}
				} else {
					addError('Invalid email address');
					addErrorField('email');
				}
				redirect('/email/sendEmail?emailID='.$email->emailID);
			} else {
				addError('Email not found');
				redirect('/email');
			}
		} // function send

		/**
		 *  Clear compiled template for email
		 *  Args: (obj) email object
		 *  Return: none
		 */
		private function clearCompiledTemplates($editEmail) {
			$this->registerCustomResource('email');
			$this->clearCompiled('email:'.$editEmail->name.'.subject');
			$this->clearCompiled('email:'.$editEmail->name.'.fromEmail');
			$this->clearCompiled('email:'.$editEmail->name.'.html');
			$this->clearCompiled('email:'.$editEmail->name.'.text');
		} // function clearCompiledTemplates
	} // class emailController

?>
