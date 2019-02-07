<?php

	class templateEmailResource extends templateDBResource {
		// resource name
		protected $resourceName = 'email';
		// source information
		protected $table = 'email';
		protected $idField = 'name';
		protected $modifiedField = 'lastModified';

		/**
		 *  Return sql query
		 *  Args: none
		 *  Return: (str) query
		 */
		protected function getQuery() {
			$sql = "SELECT `a`.`name`, `a`.`subject`, `a`.`fromEmail`, `a`.`lastModified`, 
						CONCAT(IFNULL(`b`.`html`, ''), `a`.`html`, IFNULL(`c`.`html`, '')) AS `html`, 
						CONCAT(IFNULL(`b`.`text`, ''), `a`.`text`, IFNULL(`c`.`text`, '')) AS `text` 
					FROM `".$this->table."` `a`
					LEFT JOIN `emailSection` `b` ON (`a`.`headerID` = `b`.`emailSectionID`)
					LEFT JOIN `emailSection` `c` ON (`a`.`footerID` = `c`.`emailSectionID`)
					WHERE `a`.`".$this->idField."` = ?";
			return $sql;
		} // function getQuery
	} // templateEmailResource

?>