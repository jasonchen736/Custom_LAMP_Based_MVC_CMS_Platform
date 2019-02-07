<?php

	class page extends bModel {
		// active record table
		protected $_table = 'page';
		// history table (optional)
		protected $_historyTable = 'pageHistory';
		// field name of primary key
		protected $_id = 'pageID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'pageID' => false,
			'languageID' => true,
			'name' => true,
			'type' => true,
			'title' => true,
			'content' => true,
			'summary' => false,
			'metaDescription' => false,
			'metaKeywords' => false,
			'metaTags' => false,
			'status' => true,
			'articleDate' => false,
			'dateAdded' => false,
			'lastModified' => false
		);
		// status options
		public static $statusOptions = array(
			'active' => 'active',
			'inactive' => 'inactive',
		);
		// type options
		public static $typeOptions = array(
			'content' => 'content',
			//'news' => 'news'
		);

		/**
		 *  Get sorted news article years
		 *  Args: none
		 *  Return: (array) years
		 */
		public static function getNewsYears() {
			$years = array();
			$current = date('Y');
			$sql = "SELECT DISTINCT YEAR(`articleDate`) as `year` FROM `pages` WHERE `type` = 'news' AND `status` = 'active' ORDER BY `year` DESC";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					if ($row['year'] != $current) {
						$years[] = $row['year'];
					}
				}
			}
			return $years;
		} // function getNewsYears

		/**
		 *  Get sorted news article by year
		 *  Args: (str) year
		 *  Return: (array) years
		 */
		public static function getNewsArchives($year = false) {
			if (!$year) {
				$year = date('Y');
			} else {
				$year = (int) $year;
			}
			$start = database::dateToSql($year.'-01-01 00:00:00', true);
			$end = database::dateToSql($year.'-12-31 23:59:59', true);
			$sql = "SELECT * FROM `pages` WHERE `type` = 'news' AND `status` = 'active' AND `articleDate` BETWEEN ? AND ? ORDER BY `articleDate` DESC";
			$result = query($sql, array($start, $end));
			return $result->fetchAll();
		} // function getNewsYears

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->pageID = NULL;
				$this->setRaw('dateAdded', 'NOW()');
				$this->setRaw('lastModified', 'NOW()');
			} else {
				$this->setRaw('lastModified', 'NOW()');
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate based on unique content name and language
		 *  Args: none
		 *  Return: (boolean) is duplicate content
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `pageID` FROM `".$this->_table."` WHERE `name` = ? AND `languageID` = ?";
			$result = query($sql, array($this->name, $this->languageID));
			if ($result->count > 0) {
				$id = $this->pageID;
				while ($row = $result->fetch()) {
					if ($row['pageID'] != $id) {
						$this->addError('There is an existing page with the same name', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate

		/**
		 *  Render page content with module embeds
		 *  Args: (boolean) include site template
		 *  Return: (str) rendered content
		 */
		public function renderContent($includeSiteTemplate = true) {
			$languageID = $this->languageID ? $this->languageID : language::getCurrent('languageID');
			$content = $this->content;
			$isOnlineStore = isset($this->isOnlineStore)?$this->isOnlineStore:false;
			if ($includeSiteTemplate) {
				$siteTemplate = siteTemplate::getObject(array('languageID' => $languageID));
				if ($siteTemplate) {
					$tpl = $siteTemplate->content;
					$content = str_replace('[content]', $content, $tpl);
				}
			}
			preg_match_all('/\[content:(.*)\]/U', $content, $matches);
			if (!empty($matches[1])) {
				foreach ($matches[1] as $name) {
					$module = contentModule::find(array('name' => $name, 'languageID' => $languageID));
					if ($module) {
						$content = str_replace('[content:'.$name.']', $module[0]['content'], $content);
					}
				}
				$content = preg_replace('/\[content:.*\]/U', '', $content);
			}
			$content = moduleDecorator::decorate($content);
			return $content;
		} // function renderContent
	} // class page

?>
