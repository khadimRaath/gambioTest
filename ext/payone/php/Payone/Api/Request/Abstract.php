<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 2)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */


abstract class Payone_Api_Request_Abstract
    implements Payone_Api_Request_Interface, Payone_Protocol_Filter_Filterable
{
    /**
     * @var int
     */
    protected $mid = NULL;

    /**
     * @var int
     */
    protected $portalid = NULL;

    /**
     * @var string
     */
    protected $key = NULL;

    /**
     * @var string
     */
    protected $mode = NULL;

    /**
     * @var string
     */
    protected $request = NULL;

    /**
     * @var string
     */
    protected $encoding = NULL;

    /**
     * name of the solution-partner (company)
     *
     * @var string
     */
    protected $solution_name = NULL;

    /**
     * version of the solution-partner's app / extension / plugin / etc..
     *
     * @var string
     */
    protected $solution_version = NULL;

    /**
     * system-name
     *
     * @var string
     */
    protected $integrator_name = NULL;

    /**
     * system-version
     *
     * @var string
     */
    protected $integrator_version = NULL;

    protected $api_version = '3.10';
    protected $add_paydata = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }

    /**
     * @param array $data
     */
    public function init(array $data = array())
    {
        foreach ($data as $key => $value)
        {
            if($key == 'add_paydata')
            {
                foreach($value as $paydata_name => $paydata_value)
                {
                    $this->setAddPaydata($paydata_name, $paydata_value);
                }
            }
            else
            {
                $key = ucwords(str_replace('_', ' ', $key));
                $method = 'set' . str_replace(' ', '', $key);

                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $data)
        {
            if ($data === null) {
                continue;
            }
            if($key == 'add_paydata')
            {
                foreach($this->add_paydata as $paydata_key => $paydata_value)
                {
                    $result['add_paydata['.$paydata_key.']'] = $paydata_value;
                }
            }
            else if ($data instanceof Payone_Api_Request_Parameter_Interface) {
                /**
                 * @var Payone_Api_Request_Parameter_Interface $data
                 */
                $result = array_merge($result, $data->toArray());
            }
            else {
                $result[$key] = $data;
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $stringArray = array();
        foreach ($this->toArray() as $key => $value) {
            $stringArray[] = $key . '=' . $value;
        }

        $result = implode('|', $stringArray);
        return $result;
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function getValue($key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     * @param string $name
     * @return boolean|null
     */
    public function setValue($key, $name)
    {
        return $this->set($key, $name);
    }

    /**
     * @param $name
     * @return null|mixed
     */
    public function get($name)
    {
        if (strpos($name, '/')) {
            $explodedName = explode('/', $name);
            if (count($explodedName) != 2) {
                return null;
            }
            $property = $explodedName[0];
            $propertyName = $explodedName[1];
            if (property_exists($this, $property)) {
                $object = $this->$property;
                /**
                 * @var $object Payone_Api_Request_Parameter_Interface
                 */
                if (!($object instanceof Payone_Api_Request_Parameter_Interface)) {
                    return null;
                }

                return $object->get($propertyName);
            }
        }
        elseif (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return boolean|null
     */
    public function set($name, $value)
    {
        if (strpos($name, '/')) {
            $explodedName = explode('/', $name);
            if (count($explodedName) != 2) {
                return null;
            }
            $property = $explodedName[0];
            $propertyName = $explodedName[1];
            if (property_exists($this, $property)) {
                $object = $this->$property;
                /**
                 * @var $object Payone_Api_Request_Parameter_Interface
                 */
                if (!($object instanceof Payone_Api_Request_Parameter_Interface)) {
                    return null;
                }

                return $object->set($propertyName, $value);
            }
        }
        elseif (property_exists($this, $name)) {
            $this->$name = $value;
            return true;
        }
        return null;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = md5($key);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param int $mid
     */
    public function setMid($mid)
    {
        $this->mid = $mid;
    }

    /**
     * @return int
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $portalid
     */
    public function setPortalid($portalid)
    {
        $this->portalid = $portalid;
    }

    /**
     * @return int
     */
    public function getPortalid()
    {
        return $this->portalid;
    }

    /**
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * set the system-Name
     *
     * @param string $integrator_name
     */
    public function setIntegratorName($integrator_name)
    {
        $this->integrator_name = $integrator_name;
    }

    /**
     * @return string
     */
    public function getIntegratorName()
    {
        return $this->integrator_name;
    }

    /**
     * set the system-version
     *
     * @param string $integrator_version
     */
    public function setIntegratorVersion($integrator_version)
    {
        $this->integrator_version = $integrator_version;
    }

    /**
     * @return string
     */
    public function getIntegratorVersion()
    {
        return $this->integrator_version;
    }

    /**
     * set the name of the solution-partner (company)
     *
     * @param string $solution_name
     */
    public function setSolutionName($solution_name)
    {
        $this->solution_name = $solution_name;
    }

    /**
     * @return string
     */
    public function getSolutionName()
    {
        return $this->solution_name;
    }

    /**
     * set the version of the solution-partner's app / extension / plugin / etc..
     *
     * @param string $solution_version
     */
    public function setSolutionVersion($solution_version)
    {
        $this->solution_version = $solution_version;
    }

    /**
     * @return string
     */
    public function getSolutionVersion()
    {
        return $this->solution_version;
    }

    /* 2016-05 EXTENSIONS BELOW */

    public function setAddPaydata($paydata_key, $paydata_value)
    {
        if($paydata_value === null)
        {
            unset($this->add_paydata[(string)$paydata_key]);
        }
        else
        {
            $this->add_paydata[(string)$paydata_key] = (string)$paydata_value;
        }
    }

    public function getAddPaydata($paydata_key)
    {
        $paydata_value = isset($this->add_paydata[$paydata_key]) ? $this->add_paydata[$paydata_key] : null;
        return $paydata_value;
    }

}
