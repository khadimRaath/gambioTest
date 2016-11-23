<?php
/* --------------------------------------------------------------
   sessions.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(sessions.php,v 1.16 2003/04/02); www.oscommerce.com 
   (c) 2003	 nextcommerce (sessions.php,v 1.5 2003/08/13); www.nextcommerce.org 
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: sessions.php 1195 2005-11-28 21:10:52Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

@ini_set("session.gc_maxlifetime", 1440);
@ini_set("session.gc_probability", 100);

// deprecated - not used anymore
if(!defined('STORE_SESSIONS'))
{
	define('STORE_SESSIONS', '');
}

function xtc_session_start()
{
	return session_start();
}

function xtc_session_register($p_variable)
{
	global $session_started;

	if($session_started == true)
	{
		if(!isset($_SESSION[$p_variable]))
		{
			$_SESSION[$p_variable] = $GLOBALS[$p_variable];

			return true;
		}
		else
		{
			return false;
		}
	}
}

function xtc_session_is_registered($p_variable)
{
	return isset($_SESSION[$p_variable]);
}

function xtc_session_unregister($p_variable)
{
	unset($_SESSION[$p_variable]);
}

function xtc_session_id($sessid = '')
{
	if(!empty($sessid))
	{
		return session_id($sessid);
	}
	else
	{
		return session_id();
	}
}

function xtc_session_name($name = '')
{
	if(!empty($name))
	{
		return session_name($name);
	}
	else
	{
		return session_name();
	}
}

function xtc_session_close()
{
	if(function_exists('session_close'))
	{
		return session_close();
	}
}

function xtc_session_destroy()
{
	return session_destroy();
}

function xtc_session_save_path($path = '')
{
	if(!empty($path))
	{
		return session_save_path($path);
	}
	else
	{
		return session_save_path();
	}
}

function xtc_session_recreate()
{

	$session_backup = $_SESSION;

	unset($_COOKIE[xtc_session_name()]);

	xtc_session_destroy();

	xtc_session_start();

	$_SESSION = $session_backup;
	unset($session_backup);
}