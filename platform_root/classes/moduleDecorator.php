<?php

	/**
	 *  Any public methods placed here may be called by a content page by adding a tag with the format [module:function name]
	 *  The decorate method will replace module tags with html
	 */
	class moduleDecorator {
		protected $template = false;
		public static $languageID = false;

		/**
		 *  Decorate content
		 *  Args: (str) content
		 *  Return: (str) decorated content
		 */
		public static function decorate($content) {
			preg_match_all('/\[module:(.*)\]/U', $content, $matches);
			if (!empty($matches[1])) {
				$decorator = new moduleDecorator;
				foreach ($matches[1] as $name) {
					if (method_exists($decorator, $name) && is_callable(array($decorator, $name))) {
						$module = $decorator->$name();
						if ($module) {
							$content = str_replace('[module:'.$name.']', $module, $content);
						}
					}
				}
				$content = preg_replace('/\[module:.*\]/U', '', $content);
			}
			return $content;
		} // function decorate

		/**
		 *  Instantiate template object
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			$this->template = new template;
		} // function __construct

		/**
		 *  Language selector template
		 *  Args: none
		 *  Return: (str) html
		 */
		public function languageSelector() {
			if (self::$languageID) {
				$language = new language(self::$languageID);
				if ($language->exists()) {
					$currentLanguage = $language->toArray();
				} else {
					$currentLanguage = language::getCurrent();
				}
			} else {
				$currentLanguage = language::getCurrent();
			}
			$language = array(
				'current' => $currentLanguage,
				'list' => language::find(array('ORDER BY' => '`name` ASC'))
			);
			$this->template->assign('language', $language);
			return $this->template->fetch('modules/languageSelector.tpl');
		} // function languageSelector

		/**
		 *  Top navigation template
		 *  Args: none
		 *  Return: (str) html
		 */
		public function topNav() {
			$navigation = navigation::getNavigation(false, self::$languageID);
			foreach ($navigation as &$top) {
				if ($top['sub']) {
					foreach ($top['sub'] as &$s1) {
						if ($s1['sub']) {
							if (count($s1['sub']) > 8) {
								$size = ceil(count($s1['sub']) / 2);
								$s1['sub'] = array_chunk($s1['sub'], $size);
							} else {
								$s1['sub'] = array($s1['sub']);
							}
						}
					}
				}
			}
			unset($s1);
			unset($top);
			$this->template->assign('navigation', $navigation);
			return $this->template->fetch('modules/topNav.tpl');
		} // function topNav
	} // class moduleDecorator

?>
