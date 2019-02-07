<?php

	class dataViewNavigation extends bDataView {
		// controller for specified table
		protected $_table = 'navigation';
		// default sorting
		protected $_defaultSortField = 'navigationID';
		protected $_defaultSortOrder = 'ASC';

		/**
		 *  Copy navigation to a different language
		 *  Args: (array) navigation IDs, (array) additional options
		 *  Return: none
		 */
		public static function duplicateToLanguage($ids, $options) {
			$success = true;
			$languageID = isset($options['languageID']) ? $options['languageID'] : false;
			$language = new language($languageID);
			if ($language->exists()) {
				$languageID = $language->languageID;
				foreach ($ids as $id) {
					$error = false;
					$nav = new navigation($id);
					if ($nav->exists()) {
						$tree = navigation::getNavigation($nav->navigationID);
						$tnav = new navigation;
						$tnav->languageID = $languageID;
						$tnav->label = $nav->label;
						$tnav->url = $nav->url;
						$tnav->order = $nav->order;
						if ($tnav->save()) {
							if ($tree['sub']) {
								foreach ($tree['sub'] as $s1node) {
									$s1nav = new navigation;
									$s1nav->languageID = $languageID;
									$s1nav->parent = $tnav->navigationID;
									$s1nav->label = $s1node['label'];
									$s1nav->url = $s1node['url'];
									$s1nav->order = $s1node['order'];
									if ($s1nav->save()) {
										if ($s1node['sub']) {
											foreach ($s1node['sub'] as $s2node) {
												$s2nav = new navigation;
												$s2nav->languageID = $languageID;
												$s2nav->parent = $s1nav->navigationID;
												$s2nav->label = $s2node['label'];
												$s2nav->url = $s2node['url'];
												$s2nav->order = $s2node['order'];
												if (!$s2nav->save()) {
													$error = true;
													$success = false;
													addError('There was an error duplicating navigation record with ID '.$nav->navigationID);
													break;
												}
											}
										}
									} else {
										$success = false;
										addError('There was an error duplicating navigation record with ID '.$nav->navigationID);
										break;
									}
									if ($error) {
										break;
									}
								}
							} else {
								$success = false;
								addError('There was an error duplicating navigation record with ID '.$nav->navigationID);
							}
						} else {
							$success = false;
							addError('There was an error duplicating navigation record with ID '.$nav->navigationID);
						}
					}
				}
				if ($success) {
					addSuccess('Records duplicated');
				}
			} else {
				addError('Please select a valid language to duplicate to');
			}
			redirect($_SERVER['REQUEST_URI']);
		} // function duplicateToLanguage

		/**
		 *  Delete navigation items by id
		 *  Args: (array) navigation IDs
		 *  Return: none
		 */
		public static function deleteSelected($ids) {
			$error = false;
			foreach ($ids as $id) {
				$nav = new navigation($id);
				if ($nav->exists()) {
					if (!$nav->deleteNavigation()) {
						$error = true;
					}
				}
			}
			if ($error) {
				addError('One or more records failed to be deleted');
			} else {
				addSuccess('Records deleted');
			}
			redirect($_SERVER['REQUEST_URI']);
		} // function deleteSelected
	} // class dataViewNavigation

?>
