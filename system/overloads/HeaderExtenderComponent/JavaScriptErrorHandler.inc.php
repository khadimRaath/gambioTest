<?php
/* --------------------------------------------------------------
  JavaScriptErrorHandler.inc.php 2014-03-07 wu
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class JavaScriptErrorHandler extends JavaScriptErrorHandler_parent
{
	function proceed()
	{
		if($GLOBALS['coo_debugger']->is_enabled('log_js_errors') == true)
		{
			$this->v_output_buffer['JavaScriptErrorHandler'] = "\t\t" . '<script type="text/javascript" src="gm/javascript/GMJavaScriptErrorHandler.js"></script>' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t" . '<script type="text/javascript">' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t\t" . 'window.onerror = handleJsError;' . "\n";
			$this->v_output_buffer['JavaScriptErrorHandler'] .= "\t\t" . '</script>' . "\n";
		}
		
		parent::proceed();
	}
}