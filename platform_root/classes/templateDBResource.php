<?php

	class templateDBResource extends Smarty_Resource_Custom {
		// resource name
		protected $resourceName = false;
		// source information
		protected $table = false;
		protected $idField = false;
		protected $modifiedField = false;

		/**
		 *  Return sql query
		 *  Args: none
		 *  Return: (str) query
		 */
		protected function getQuery() {
			$sql = "SELECT *
				FROM `".$this->table."`
				WHERE `".$this->idField."` = ?";
			return $sql;
		} // function getQuery

		/**
		 *  Smarty resource get template function - retrieve template using an ID field
		 *    Argument template name uses the format "template.field"
		 *    ex: email.subject, email.html, receipt.content
		 *  Args: (str) template name, (str) template source, (smarty) template modification time
		 *  Return: none
		 */
		protected function fetch($template, &$source, &$mtime) {
			$source = null;
			$mtime = null;
			list($name, $field) = explode('.', $template);
			if ($name) {
				if (!isset(template::$_resourceTemplates[$this->resourceName]) || !isset(template::$_resourceTemplates[$this->resourceName][$name]) || empty(template::$_resourceTemplates[$this->resourceName][$name])) {
					$sql = $this->getQuery();
					$result = query($sql, array($name));
					if ($result->count > 0) {
						$row = $result->fetch();
						if (!isset(template::$_resourceTemplates[$this->resourceName])) {
							template::$_resourceTemplates[$this->resourceName] = array();
						}
						template::$_resourceTemplates[$this->resourceName][$name] = array();
						foreach ($row as $key => $val) {
							template::$_resourceTemplates[$this->resourceName][$name][$key] = $val;
						}
					}
				}
				if (isset(template::$_resourceTemplates[$this->resourceName][$name][$field])) {
					$source = template::$_resourceTemplates[$this->resourceName][$name][$field];
				}
				if (isset(template::$_resourceTemplates[$this->resourceName][$name][$this->modifiedField])) {
					$mtime = strtotime(template::$_resourceTemplates[$this->resourceName][$name][$this->modifiedField]);
				}
			}
		} // function fetch
	} // templateDBResource

?>
