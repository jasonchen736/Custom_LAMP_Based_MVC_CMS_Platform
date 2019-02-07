<?php

	class defaultController extends bController {
		/**
		 *  Index page
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$this->renderContent('homepage');
		} // function index

		/**
		 *  Render cms content page
		 *  Args: (str) name
		 *  Return: none
		 */
		public function renderContent($name = false) {
			if (!$name) {
				$name = getRequest('_c');
			}
			if (empty($name)) {
				throw new Exception('404');
			}
	
			$page = page::getObject(array('name' => $name, 'languageID' => language::getCurrent('languageID')));
			if (empty($page) || !$page->exists() || $page->status == 'inactive') {
				throw new Exception('404');
			}

			if (getPost('formSubmit')) {
				$request = $_POST;
				if (isset($request['redirect'])) {
					$redirect = $request['redirect'];
				} else {
					$redirect = false;
				}
				if (isset($request['responseEmail'])) {
					$emails = $request['responseEmail'];
				} else {
					$emails = false;
				}
				$fp = new formProcessor;
				if ($fp->processRequest($request, $emails)) {
					if ($redirect) {
						redirect($redirect);
					} else {
						addSuccess('Your inquiry has been submitted, thank you for your interest');
						redirect(PAGE_URL);
					}
				} else {
					$errors = $fp->getErrors();
					foreach ($errors as $msg) {
						addError($msg);
					}
				}
			}
			$this->assignClean('_TITLE', $page->title);
			if (!empty($page->metaDescription)) {
				$this->addMeta('description', $page->metaDescription);
			}
			if (!empty($page->metaKeywords)) {
				$this->addMeta('keywords', $page->metaKeywords);
			}
			$content = $page->renderContent();
			$this->assign('content', $content);		
			$this->renderView('page.tpl');
		} // function renderContent

		/**
		 *  Preview content
		 *  Args: none
		 *  Return: none
		 */
		public function previewContent() {
			$auth = adminAuth::getInstance();
			if (!$auth->validate()) {
				redirect('/adminLogin');
			}
			$type = getRequest('_type');
			switch ($type) {
				case 'contentModule':
					$contentModule = new contentModule(getRequest('_id'));
					if ($contentModule->exists() || getPost('preview')) {
						if ($date = getRequest('_d')) {
							$history = $contentModule->getHistory($date);
							if ($history) {
								$contentModule->loadData($history);
							}
						} elseif (getPost('preview'))  {
							$values = $contentModule->toArray();
							foreach ($values as $field => $val) {
								$contentModule->$field = getPost($field);
							}
						}
						$siteTemplate = siteTemplate::getObject(array('languageID' => $contentModule->languageID ? $contentModule->languageID : getPost('languageID')));
						if ($siteTemplate) {
							$tpl = $siteTemplate->content;
							moduleDecorator::$languageID = getPost('languageID');
							$tpl = moduleDecorator::decorate($tpl);
							$content = str_replace('[content]', $contentModule->content, $tpl);
						} else {
							$content = $contentModule->content;
						}
						$this->assignClean('_TITLE', 'Preview Content Module');
						$this->assign('content', $content);
						$this->renderView('page.tpl');
					} else {
						throw new Exception('404');
					}
					break;
				case 'page':
					$page = new page(getRequest('_id'));

					if ($page->exists() || getPost('preview')) {
						if ($date = getRequest('_d')) {
							$history = $page->getHistory($date);
							if ($history) {
								$page->loadData($history);
							}
						} elseif (getPost('preview'))  {
							$values = $page->toArray();
							foreach ($values as $field => $val) {
								$page->$field = getPost($field);
							}
						}
						$this->assignClean('_TITLE', $page->title);
						if (!empty($page->metaDescription)) {
							$this->addMeta('description', $page->metaDescription);
						}
						if (!empty($page->metaKeywords)) {
							$this->addMeta('keywords', $page->metaKeywords);
						}
						$this->assign('content', $page->renderContent());
						$this->renderView('page.tpl');
					} else {
						throw new Exception('404');
					}
					break;
				case 'siteTemplate':
					$id = getRequest('_id');
					if ($id) {
						$siteTemplate = siteTemplate::getObject(array('siteTemplateID' => $id));
					} else {
						$siteTemplate = siteTemplate::getObject(array('languageID' => getPost('languageID')));
					}
					if ($siteTemplate) {
						if ($date = getRequest('_d')) {
							$history = $siteTemplate->getHistory($date);
							if ($history) {
								$siteTemplate->loadData($history);
							}
						} elseif (getPost('preview')) {
							$siteTemplate = new siteTemplate;
							$values = $siteTemplate->toArray();
							foreach ($values as $field => $val) {
								$siteTemplate->$field = getPost($field);
							}
						}
					} else {
						if (getPost('preview')) {
							$siteTemplate = new siteTemplate;
							$values = $siteTemplate->toArray();
							foreach ($values as $field => $val) {
								$siteTemplate->$field = getPost($field);
							}
						} else {
							throw new Exception('404');
						}
					}
					$page = new page;
					$page->languageID = getPost('languageID');
					$page->content = $siteTemplate->content;
					$this->assignClean('_TITLE', 'Preview Site Template');
					$this->assign('content', $page->renderContent(false));
					$this->renderView('page.tpl');
					break;
				default:
					throw new Exception('404');
					break;
			}
		} // function previewContent

		/**
		 *  Admin login page
		 *  Args: none
		 *  Return: none
		 */
		public function adminLogin() {
			$auth = adminAuth::getInstance();
			if ($auth->isLoggedIn()) {
				redirect('/');
			}
			$login = getPost('login');
			$pass = getPost('pass');
			if (getPost('submit')) {
				$sourceURL = $auth->getAuthInfo('loginSource');
				if ($auth->login($login, $pass)) {
					if (!empty($sourceURL)) {
						redirect($sourceURL);
					} else {
						redirect('/');
					}
				}
			}
			$template = new template;
			$template->assignClean('login', $login);
			$template->assignClean('pass', $pass);
			$content = $template->fetch('login.tpl');
			$page = new page;
			$page->content = $content;
			$this->assignClean('_TITLE', 'Admin Login');
			$this->assign('content', $page->renderContent());
			$this->renderAdmin('page.tpl');
		} // function adminLogin

		/**
		 *  Admin log out
		 *  Args: none
		 *  Return: none
		 */
		public function adminLogout() {
			$auth = new adminAuth;
			$auth->logout(false);
			redirect('/');
		} // function adminLogout

		/**
		 *  Select language
		 *  Args: none
		 *  Return: none
		 */
		public function languageSelect() {
			if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
				redirect($_SERVER['HTTP_REFERER']);
			} else {
				redirect('/');
			}
		} // function languageSelect
	} // class defaultController

?>
