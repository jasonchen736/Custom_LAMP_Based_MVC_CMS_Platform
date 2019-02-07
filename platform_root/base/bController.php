<?php

	class bController {
		protected $_template;

		/**
		 *  Set template object
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			if (!$this->_template) {
				$this->_template = new template;
			}
			if (getRequest('switchLanguage')) {
				language::setCurrent(getRequest('languageID'));
			} elseif (!isset($_SESSION['language'])) {
				$language = language::getObject(array('default' => 1));
				if ($language) {
					language::setCurrent($language->languageID);
				} else {
					$language = language::find();
					if ($language) {
						language::setCurrent($language[0]['languageID']);
					} else {
						language::setCurrent(1);
					}
				}
			}
		} // function __construct

		/**
		 *  Add meta data
		 *  Args: (str) meta data, (str) index
		 *  Return: none
		 */
		protected function addMeta($index, $value) {
			$this->_template->addMeta($index, $value);
		} // function addMeta

		/**
		 *  Add a script
		 *  Args: (str) html head script, (str) index
		 *  Return: none
		 */
		protected function addScript($script, $index = false) {
			$this->_template->addScript($script, $index);
		} // function addScript

		/**
		 *  Add a style
		 *  Args: (str) html head style, (str) index
		 *  Return: none
		 */
		protected function addStyle($style, $index = false) {
			$this->_template->addStyle($style, $index);
		} // function addStyle

		/**
		 *  Assign var
		 *  Args: (str) name, (str) value
		 *  Return: none
		 */
		protected function assign($name, $value) {
			$this->_template->assign($name, $value);
		} // function assign

		/**
		 *  Assign var with cleaning
		 *  Args: (str) name, (str) value
		 *  Return: none
		 */
		protected function assignClean($name, $value) {
			$this->_template->assignClean($name, $value);
		} // function assignClean

		/**
		 *  Render sub template file as the content of a main template
		 *  Args: (str) template, (str) main template
		 *  Return: none
		 */
		protected function renderPageContent($template, $mainTemplate) {
			$this->_template->getSessionMessage(false);
			$page = new page;
			$page->content = $this->_template->fetch($template);
			$this->_template->assign('content', $page->renderContent());
			$this->_template->display($mainTemplate);
		} // function renderPageContent

		/**
		 *  Render site template
		 *  Args: (str) template
		 *  Return: none
		 */
		protected function renderView($template) {
			$this->_template->display($template);
		} // function renderView

		/**
		 *  Render admin template
		 *  Args: (str) template
		 *  Return: none
		 */
		protected function renderAdmin($template) {
			$this->_template->displayAdmin($template);
		} // function renderAdmin

		/**
		 *  Clear compiled template
		 *  Args: (str) template
		 *  Return: none
		 */
		protected function clearCompiled($template) {
			$this->_template->clearCompiledTemplate($template);
		} // function clearCompiled

		/**
		 *  Register custom resource
		 *  Args: (str) name
		 *  Return: none
		 */
		protected function registerCustomResource($name) {
			$this->_template->registerCustomResource($name);
		} // function registerCustomResource

		/**
		 *  Return mailer object for specified email
		 *  Args: (str) email to retrieve
		 *  Return: (mailer) mailer object
		 */
		protected function getMailer($emailName) {
			return $this->_template->getMailer($emailName);
		} // function getMailer
	} // class bController

?>
