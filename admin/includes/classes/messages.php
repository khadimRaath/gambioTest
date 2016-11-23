<?php
/*
   --------------------------------------------------------------
   Messages.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------


 */
// messageStack is too inflexible for certain use cases

class Messages_ORIGIN {
	protected $messages;
	protected $namespace;
	
	public function __construct($namespace = 'messages') {
		$this->namespace = $namespace;
		$this->messages = array();
		if(isset($_SESSION[$namespace])) {
			$this->messages = $_SESSION[$namespace];
		}
	}
	
	public function addMessage($message) {
		$this->messages[] = $message;
		$this->toSession();
	}
	
	protected function toSession() {
		$_SESSION[$this->namespace] = $this->messages;
	}
	
	public function getMessages() {
		return $this->messages;
	}
	
	public function reset() {
		$this->messages = array();
		$this->toSession();
	}
	
	public function isEmpty() {
		return count($this->messages) == 0;
	}
}

MainFactory::load_origin_class('Messages');
