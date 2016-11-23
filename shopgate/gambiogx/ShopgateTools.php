<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */


class ShopgateTools
{
    /**
     * @param bool $asArray
     *
     * @return array|string
     */
    public static function  getGambioVersion($asArray = true)
    {
        // Get Gambio GX version
        if (defined('DIR_FS_DOCUMENT_ROOT') && file_exists(DIR_FS_DOCUMENT_ROOT . 'release_info.php')) {
            include(DIR_FS_DOCUMENT_ROOT . 'release_info.php');
        } elseif (file_exists(dirname(__FILE__) . '/../../release_info.php')) {
            include(dirname(__FILE__) . '/../../release_info.php');
        }

        if (!isset($gx_version)) {
            $gx_version = '';
        }

        $gambioGXVersion = array(
            'main_version' => '1',
            'sub_version'  => '0',
            'revision'     => '0',
        );

        $gxVersionFileDestination = '/' . trim(DIR_FS_CATALOG, '/') . '/release_info.php';
        if (file_exists($gxVersionFileDestination)) {
            require_once $gxVersionFileDestination;
            if (preg_match(
                '/(?P<main_version>[1-9]+).(?P<sub_version>[0-9]+).(?P<revision>[0-9]+)/', $gx_version, $matches
            )) {
                $gambioGXVersion = array(
                    'main_version' => $matches['main_version'],
                    'sub_version'  => $matches['sub_version'],
                    'revision'     => $matches['revision'],
                );
            }
        }

        self::loadLogger(implode('.', $gambioGXVersion));

        return ($asArray) ? $gambioGXVersion : implode(".", $gambioGXVersion);
    }

    /**
     * @param string $version
     *
     * @return bool
     */
    public static function isGambioVersionLowerThan($version)
    {
        return (version_compare($version, self::getGambioVersion(false)) >= 0) ? true : false;
    }

    /**
     * @param string $version
     */
    protected static function loadLogger($version)
    {
        // debugger already present
        if (in_array('Debugger', get_declared_classes())) {
            return;
        }

        // not for GambioGX 1.x
        if (version_compare($version, '2.0.0', '<')) {
            return;
        }

        // path for GambioGX 2.0.0.0 to 2.1.0.0
        $debugfilePath = '';
        if (version_compare($version, '2.1.0', '<')) {
            $debugfilePath = DIR_FS_CATALOG . 'system/core/Debugger.inc.php';
        }

        // path for GambioGX 2.1.0.0 and higher 
        if (version_compare($version, '2.1.0', '>=')) {
            $debugfilePath = DIR_FS_CATALOG . 'system/core/logging/Debugger.inc.php';
        }

        // don't break operations when the file can't be found, just silently exit
        if (!file_exists($debugfilePath)) {
            return;
        }

        require_once $debugfilePath;
        $GLOBALS['coo_debugger'] = new Debugger();
    }

    /**
     * Helps retrieving the category
     * index table that was present
     * in GX2+ only
     *
     * @return string
     */
    public static function getCategoryIndexTable()
    {
        $table = 'categories_index';
        if (ShopgateTools::isGambioVersionLowerThan('2.0.0')) {
            $table = '';
        } elseif (ShopgateTools::isGambioVersionLowerThan('2.1.0')) {
            $table = 'feature_index';
        }

        return $table;
    }
} 
