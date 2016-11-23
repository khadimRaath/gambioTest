<?php
class SofortTag extends SofortElement {
	
	public $tagname = '';
	
	public $attributes = array();
	
	public $children = array();
	
	
	public function __construct($tagname, array $attributes = array(), $children = array()) {
		$this->tagname = $tagname;
		$this->attributes = $attributes;
		$this->children = is_array($children) ? $children : array($children);
	}
	
	
	public function render() {
		$output = '';
		$attributes = '';
		
		foreach ($this->children as $child) {
			$output .= is_object($child) ? $child->render() : $child;
		}
		
		foreach ($this->attributes as $key => $value) {
			$attributes .= " $key=\"$value\"";
		}
		
		return $this->_render($output, $attributes);
	}
	
	
	protected function _render($output, $attributes) {
		return $output !== '' ? "<{$this->tagname}{$attributes}>{$output}</{$this->tagname}>" : "<{$this->tagname}{$attributes} />";
	}
}
?>