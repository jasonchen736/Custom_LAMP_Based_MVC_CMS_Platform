<?php

	class sessionHandlerDB {
		// database handler
		private static $dbh;
		private static $life;

		/**
		 *  Set session life
		 *  Args: none
		 *  Return: none
		 */
		public static function initialize() {
			self::$dbh = database::getInstance();
			self::$life = SESSION_DURATION;
		} // function initialize

		/**
		 *  Establish custom session functions
		 *  Args: none
		 *  Return: none
		 */
		public static function setHandler() {
			session_set_save_handler(
				array('sessionHandlerDB', 'open'),
				array('sessionHandlerDB', 'close'),
				array('sessionHandlerDB', 'read'),
				array('sessionHandlerDB', 'write'),
				array('sessionHandlerDB', 'destroy'),
				array('sessionHandlerDB', 'gc')
			);
		} // function setHandler

		/**
		 *  Session open
		 *  Args: (str) save path, (str) session name
		 *  Return: (boolean) true
		 */
		public static function open($save_path, $session_name) {
			return true;
		} // function open

		/**
		 *  Session close
		 *  Args: none
		 *  Return: (boolean) true
		 */
		public static function close() {
			self::gc();
			return true;
		} // function close

		/**
		 *  Read a session and return data
		 *  Args: (str) session id
		 *  Return: (str) session data
		 */
		public static function read($id) {
			$data = '';
			$result = self::$dbh->query("SELECT `session_data` FROM `session` WHERE `session_id` = ? AND `expires` > ?", array($id, time()));
			if ($result->count > 0) {
				$row = $result->fetch();
				$data = $row['session_data'];
			}
			return $data;
		} // function read

		/**
		 *  Write session data to database
		 *  Args: (str) session id, (str) session data
		 *  Return: (boolean) true
		 */
		public static function write($id, $data) {
			$time = time() + self::$life;
			self::$dbh->query("INSERT INTO `session` (`session_id`, `session_data`, `expires`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `session_data` =  ?, `expires` = ?", array($id, $data, $time, $data, $time));
			return true;
		} // function write

		/**
		 *  Destroys a session
		 *  Args: (str) session id
		 *  Return: (boolean) true
		 */
		public static function destroy($id) {
			self::$dbh->query("DELETE FROM `session` WHERE `session_id` = ?", array($id));
			return true;
		} // function destroy

		/**
		 *  Garbage collection removes expired sessions
		 *  Args: none
		 *  Return: (boolean) true
		 */
		public static function gc() {
			self::$dbh->query('DELETE FROM `session` WHERE `expires` < ?', array(time()));
			return true;
		} // function gc
	} // class sessionHandlerDB

?>
