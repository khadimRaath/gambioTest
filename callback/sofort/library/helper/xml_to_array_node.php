<?php
class XmlToArrayNode {
	
	private $_attributes = array();
	private $_children = array();
	private $_data = '';
	private $_name = '';
	private $_open = true;
	private $_ParentXmlToArrayNode = null;
	
	
	public function __construct($name, $attributes) {
		$this->_name = $name;
		$this->_attributes = $attributes;
	}
	
	
	public function addChild(XmlToArrayNode $XmlToArrayNode) {
		$this->_children[] = $XmlToArrayNode;
	}
	
	
	public function getData() {
		return $this->_data;
	}
	
	
	public function getName() {
		return $this->_name;
	}
	
	
	public function getParentXmlToArrayNode() {
		return $this->_ParentXmlToArrayNode;
	}
	
	
	public function hasChildren() {
		return count($this->_children);
	}
	
	
	public function hasParentXmlToArrayNode() {
		return $this->_ParentXmlToArrayNode instanceof XmlToArrayNode;
	}
	
	
	public function isOpen() {
		return $this->_open;
	}
	
	
	/**
	 * Renders nodes as array
	 * @param bool $simpleStructure pass true to get an array without @data and @attributes fields
	 * @throws XmlToArrayException
	 */
	public function render($simpleStructure) {
		$array = array();
		$multiples = array();
		
		foreach ($this->_children as $Child) {
			$multiples[$Child->getName()] = isset($multiples[$Child->getName()]) ? $multiples[$Child->getName()] + 1 : 0;
		}
		
		foreach ($this->_children as $Child) {
			if ($multiples[$Child->getName()]) {
				if ($simpleStructure && !$Child->hasChildren()) {
					$array[$Child->getName()][] = $Child->getData();
				} else {
					$array[$Child->getName()][] = $Child->render($simpleStructure);
				}
			} elseif ($simpleStructure && !$Child->hasChildren()) {
				$array[$Child->getName()] = $Child->getData();
			} else {
				$array[$Child->getName()] = $Child->render($simpleStructure);
			}
		}
		
		if (!$simpleStructure) {
			$array['@data'] = $this->_data;
			$array['@attributes'] = $this->_attributes;
		}
		
		return $this->_ParentXmlToArrayNode instanceof XmlToArrayNode
			? $array
			: array($this->_name => $simpleStructure && !$this->hasChildren() ? $this->getData() : $array);
	}
	
	
	public function setClosed() {
		$this->_open = false;
	}
	
	
	public function setData($data) {
		$this->_data .= $data;
	}
	
	
	public function setParentXmlToArrayNode(XmlToArrayNode $XmlToArrayNode) {
		$this->_ParentXmlToArrayNode = $XmlToArrayNode;
	}
}
?>