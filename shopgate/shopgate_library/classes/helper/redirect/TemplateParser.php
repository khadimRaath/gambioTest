<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Redirect_TemplateParser implements Shopgate_Helper_Redirect_TemplateParserInterface
{
	public function getVariables($template)
	{
		$matches = array();
		if (!preg_match_all('/{([^}]+)}*/', $template, $matches)) {
			return array();
		}
		
		$variables = array();
		foreach ($matches[1] as $variable) {
			$parts = explode(':', $variable);
			
			$variable = new Shopgate_Model_Redirect_HtmlTagVariable();
			$variable->setName($parts[0]);
			
			if (!empty($parts[1])) {
				$variable->setFunctionName($parts[1]);
			}
			
			$variables[] = $variable;
		}
		
		return $variables;
	}
	
	public function process($template, $variable, $replacement)
	{
		$variableString = '{' . $variable->getName();
		
		if ($variable->getFunctionName()) {
			$variableString .= ':' . $variable->getFunctionName();
		}
		
		$variableString .= '}';
		
		return str_replace(
			$variableString,
			$this->filterVariableValue($replacement, $variable->getFunctionName()),
			$template
		);
	}
	
	public function filterVariableValue($value, $functionName = '')
	{
		switch ($functionName) {
			case Shopgate_Helper_Redirect_TemplateParserInterface::FUNCTION_NAME_HEX:
				return bin2hex($value);
			case Shopgate_Helper_Redirect_TemplateParserInterface::FUNCTION_NAME_URLENCODED:
				return urlencode($value);
			case Shopgate_Helper_Redirect_TemplateParserInterface::FUNCTION_NAME_BASE64:
				return base64_encode($value);
			case Shopgate_Helper_Redirect_TemplateParserInterface::FUNCTION_NAME_ESCAPED:
				return addslashes(htmlentities($value));
		}
		
		return $value;
	}
}