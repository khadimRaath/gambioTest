<?php
class SofortText extends SofortElement {
	
	public $text;
	
	public $escape = false;
	
	
	public function __construct($text, $escape = false, $trim = true) {
		$this->text = $trim ? trim($text) : $text;
		$this->escape = $escape;
	}
	
	public function render() {
		return $this->escape ? htmlspecialchars($this->text) : $this->text;
	}
}
?>