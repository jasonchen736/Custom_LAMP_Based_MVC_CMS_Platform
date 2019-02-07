<?php

	require_once SYSTEM_ROOT.'library/Smarty/libs/Smarty.class.php';

	class template extends Smarty {
		// registered resources
		private $_registeredResources = array();
		// header variables
		// assert priority placement order by specifying indexes
		private $_meta = array(
			'description' => false,
			'keywords' => false
		);
		private $_styles = array();
		private $_scripts = array();
		private $_siteTags = array(
			'header' => array(),
			'footer' => array()
		);
		// resource templates
		public static $_resourceTemplates = array();

		/**
		 *  Array walk function for encoding html special chars
		 *  Args: (mixed) array parameter value, (str) key
		 *  Returns: none
		 */
		public static function htmlentitiesWalk(&$item, $key) {
			$item = htmlentities($item);
		} // function htmlentitiesWalk

		/**
		 *  Initiate smart and register paths
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			parent::__construct();
			$this->initialize();
		} // function __construct

		/**
		 *  Set up resources that can be accessed while templating
		 *  Args: (str) path
		 *  Return: none
		 */
		public function initialize($path = false) {
			if (!$path) {
				$path = TEMPLATE_DIR;
			}
			$this->setTemplateDir($path.'source/');
			$this->setCompileDir($path.'compiled/');
			$this->setConfigDir($path.'cache/');
			$this->setCacheDir($path.'configs/');
		} // function initialize

		/**
		 *  Set header data, then executes & displays the template results (override)
		 *  Args: (str) template name, (str) cache id, (str) compile id, (str) parent id
		 *  Return: none
		 */
		public function display($template = null, $cache_id = null, $compile_id = null, $parent = null) {
			$this->setHeaderData();
			siteTag::retrieveSiteTagsByMatch();
			$this->_siteTags['header'] = siteTag::appendSiteTags($this->_siteTags['header'], 'header');
			$this->_siteTags['footer'] = siteTag::appendSiteTags($this->_siteTags['footer'], 'footer');
			$this->assign('_SITETAGS', $this->_siteTags);
			$auth = adminAuth::getInstance();
			$this->assign('_ADMIN', $auth->validate());
			$this->getSessionMessage();
			parent::display($template, $cache_id, $compile_id);
		} // function display

		/**
		 *  Set admin related, then executes & displays the template results
		 *  Args: (str) resource name, (str) cache id, (str) compile id
		 *  Return: none
		 */
		public function displayAdmin($resource_name, $cache_id = null, $compile_id = null) {
			$languages = language::find(array('ORDER BY' => '`name` ASC'));
			$languageOptions = array();
			foreach ($languages as $language) {
				$languageOptions[$language['languageID']] = $language['name'];
			}
			$this->setHeaderData();
			$this->getSessionMessage();
			$this->assignClean('lastQuery', bDataView::getLastQuery());
			$this->assign('currentLanguage', language::getCurrent());
			$this->assign('languageSelectOptions', $languageOptions);
			$auth = new adminAuth;
			$this->assign('admin', $auth->getAuthInfo());
			parent::display($resource_name, $cache_id, $compile_id);
		} // function displayAdmin

		/**
		 *  Set no data, just execute & display the template results
		 *  Args: (str) resource name, (str) cache id, (str) compile id
		 *  Return: none
		 */
		public function displayPlain($resource_name, $cache_id = null, $compile_id = null) {
			parent::display($resource_name, $cache_id, $compile_id);
		} // function displayPlain

		/**
		 *  Add meta data
		 *  Args: (str) meta data, (str) index
		 *  Return: none
		 */
		public function addMeta($index, $value) {
			$this->_meta[$index] = htmlentities(strip_tags($value));
		} // function addMeta

		/**
		 *  Add a script
		 *  Args: (str) html head script, (str) index
		 *  Return: none
		 */
		public function addScript($script, $index = false) {
			if ($index) {
				$this->_scripts[$index] = $script;
			} else {
				$this->_scripts[] = $script;
			}
		} // function addScript

		/**
		 *  Add a style
		 *  Args: (str) html head style, (str) index
		 *  Return: none
		 */
		public function addStyle($style, $index = false) {
			if ($index) {
				$this->_styles[$index] = $style;
			} else {
				$this->_styles[] = $style;
			}
		} // function addStyle

		/**
		 *  Assign that performs additional output escape
		 *  Args: (str) smarty assigned name, (mixed) value
		 *  Return: none
		 */
		public function assignClean($name, $value) {
			if (is_array($value)) {
				array_walk_recursive($value, array('self', 'htmlentitiesWalk'));
			} else {
				$value = htmlentities($value);
			}
			$this->assign($name, $value);
		} // function assignClean

		/**
		 *  Assigns message arrays from sessionMessage
		 *  Args: (boolean) clear all messages
		 *  Return: none
		 */
		public function getSessionMessage($clear = true) {
			$this->assign('haveMessages', haveMessages() || haveSuccess() || haveErrors());
			$this->assign('errorMessages', getErrors());
			$this->assign('successMessages', getSuccess());
			$this->assign('generalMessages', getMessages());
			$this->assign('errorFields', getErrorFields());
			if ($clear) {
				clearAllMessages();
			}
		} // function getSessionMessage

		/**
		 *  Assign meta, script, styles, and site tags variables
		 *  Args: none
		 *  Return: none
		 */
		public function setHeaderData() {
			$this->assign('_PAGE_URL', PAGE_URL);
			$this->assign('_META', $this->_meta);
			$this->assign('_STYLES', $this->_styles);
			$this->assign('_SCRIPTS', $this->_scripts);
		} // function setHeaderData

		/**
		 *  Register custom template resource
		 *  Args: (str) resource name
		 *  Return: none
		 */
		public function registerCustomResource($name) {
			if (!isset($this->_registeredResources[$name])) {
				switch ($name) {
					case 'email':
						$this->registerResource($name, new templateEmailResource);
						$this->_registeredResources[$name] = true;
						break;
					default:
						break;
				}
			}
		} // function registerCustomResource

		/**
		 *  Return mailer object for specified email
		 *  Args: (str) email to retrieve
		 *  Return: (mailer) mailer object
		 */
		public function getMailer($email) {
			$this->registerCustomResource('email');
			$mailer = new mailer;
			$errorReporting = $this->error_reporting;
			$this->error_reporting = E_ALL & ~E_NOTICE;
			$mailer->setMessage('subject', $this->fetch('email:'.$email.'.subject'));
			$mailer->setMessage('from', $this->fetch('email:'.$email.'.fromEmail'));
			$mailer->setMessage('html', $this->fetch('email:'.$email.'.html'));
			$mailer->setMessage('text', $this->fetch('email:'.$email.'.text'));
			$this->error_reporting = $errorReporting;
			return $mailer;
		} // function getMailer
	} // class template

?>
