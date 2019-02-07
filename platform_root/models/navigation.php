<?php

	class navigation extends bModel {
		// active record table
		protected $_table = 'navigation';
		// field name of primary key
		protected $_id = 'navigationID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'navigationID' => false,
			'languageID' => true,
			'label' => true,
			'url' => true,
			'parent' => false,
			'order' => false,
		);

		/**
		 *  Get navigation structure for navigation id
		 *  Args: (int) parent id, (int) language id
		 *  Return: (array) navigation array
		 */
		public static function getNavigation($navigationID = false, $languageID = false) {
			$result = navigation::find(array('languageID' => $languageID ? $languageID : language::getCurrent('languageID'), 'ORDER BY' => '`parent` DESC, `order` ASC'));
			$navigation = array();
			$nav = array();
			if ($result) {
				foreach ($result as $row) {
					$nav[$row['navigationID']] = array(
						'url' => $row['url'],
						'label' => $row['label'],
						'parent' => $row['parent'],
						'order' => $row['order'],
						'sub' => false
					);
				}
				foreach ($nav as $key => $val) {
					if ($navigationID && $navigationID == $key) {
						$navigation = $nav[$key];
						break;
					}
					if ($val['parent'] && isset($nav[$val['parent']])) {
						if (!$nav[$val['parent']]['sub']) {
							$nav[$val['parent']]['sub'] = array($key => $nav[$key]);
						} else {
							$nav[$val['parent']]['sub'][$key] = $nav[$key];
						}
						unset($nav[$key]);
					}
				}
			}
			if (!$navigationID) {
				$navigation = $nav;
			}
			return $navigation;
		} // function getNavigation

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->navigationID = NULL;
			} else {
			}
			return $result;
		} // function beforeSave

		/**
		 *  Delete navigation item and all sub items
		 *  Args: none
		 *  Return: (boolean) deleted
		 */
		public function deleteNavigation() {
			$items = array($this->navigationID);
			$sql = "SELECT DISTINCT `b`.`navigationID` 
				FROM `navigation` `a` 
				JOIN `navigation` `b` ON (`a`.`navigationID` = `b`.`parent` OR `a`.`navigationID` = `b`.`navigationID`)
				WHERE `a`.`parent` = ?";
			$result = query($sql, $items);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$items[] = $row['navigationID'];
				}
			}
			$sql = "DELETE FROM `navigation` WHERE `navigationID` IN ('".implode("', '", $items)."')";
			$result = query($sql);
			return $result->count > 0;
		} // function deleteNavigation

		/**
		 *  Get menu item level
		 *  Args: none
		 *  Return: (integer) menu item level
		 */
		public function getLevel() {
			$level = 0;
			$parent = $this->parent;
			if ($parent > 0) {
				$nav = new navigation($parent);
				if ($nav->parent > 0) {
					$level = 2;
				} else {
					$level = 1;
				}
			}
			return $level;
		} // function getLevel
	} // class navigation

?>
