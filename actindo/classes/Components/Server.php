<?php
require_once('Zend/XmlRpc/Server.php');
/**
 * Actindo Faktura/WWS Connector
 * class to implement the xmlrpc server
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Components_Server extends Zend_XmlRpc_Server
{
    /**
     * if enabled caches aren't filled
     */
    const DEVMODE = true;
    /**
     * Cache file path
     * @var null
     */
    private $cacheFile = null;
    /**
     * Initializes Main object and sets exception handling and registers all services
     */
    public function __construct()
    {
        $this->_responseClass = 'Actindo_Connector_Components_Response';
        parent::__construct();
        Zend_XmlRpc_Server_Fault::attachFaultException('Exception');
        $this->setCacheFile();
        $this->registerServices();
    }
    /**
     * Method to register all available services
     * registers cache file
     */
    private function registerServices()
    {
        if($this->cacheFile === null || !Zend_XmlRpc_Server_Cache::get($this->cacheFile, $this))
        {
            $this->setClass('Actindo_Connector_Service_Actindo','actindo');
            $this->setClass('Actindo_Connector_Service_Category','category');
            $this->setClass('Actindo_Connector_Service_Customers','customers');
            $this->setClass('Actindo_Connector_Service_Customers','customer');
            $this->setClass('Actindo_Connector_Service_Orders','orders');
            $this->setClass('Actindo_Connector_Service_Products','product');
            $this->setClass('Actindo_Connector_Service_Settings','settings');
            if($this->cacheFile !== null)
            {
                Zend_XmlRpc_Server_Cache::save($this->cacheFile, $this);
            }
        }
    }
    /**
     * actindo calls some functions that for technical reasons we can't map directly to class methods
     * (customers.list for example, can't create a method called "list" as that is a reserved php keyword).
     * We overload this function to map those calls to different callables
     *
     * @staticvar array $map translation map of methods
     * @param Zend_XmlRpc_Request $request
     * @return Zend_XmlRpc_Response
     */
    protected function _handle(Zend_XmlRpc_Request $request)
    {
        // redirect method calls
        static $map = array(
            'customers.list' => 'customers.getList',
            'orders.list'    => 'orders.getList',
        );
        $method = $request->getMethod();
        if(isset($map[$method])) {
            $request->setMethod($map[$method]);
        }

        // exception: orders.set_status may be called with a completely different method signature (one array param)
        // -> map to different method
        if($request->getMethod() == 'orders.set_status')
        {
            if(count($request->getParams()) == 1)
            {
                $request->setMethod('orders.set_status_invoice');
            }
        }

        try {
            return parent::_handle($request);
        }
        catch(Zend_XmlRpc_Server_Exception $e)
        {
            if($e->getCode() != 623) // Calling parameters do not match signature
            {
                throw $e;
            }

            // to make the connector somewhat future proof when actindo changes request parameters
            $request = $this->forceMatchingParameters($request); // !check method docblock!
            return parent::_handle($request);
        }
    }
    /**
     * tries to match the params in the request object with the expected ones by the xmlrpc method to be called.
     * this is called when the "Calling parameters do not match signature" exception is triggered.
     * !Warning! currently the only case that is handled is when there are more params than the xmlrpc method takes (trailing params are simply cut off).
     * Other cases that can happen (and are NOT currently handled):
     * - param types don't match (string expected, int received)
     * - too few parameters, xmlrpc method expected more
     *
     * @param Zend_XmlRpc_Request $request
     * @return Zend_XmlRpc_Request
     */
    protected function forceMatchingParameters(Zend_XmlRpc_Request $request)
    {
        $info     = $this->_table->getMethod($request->getMethod());
        $params   = $request->getParams();
        $argv     = $info->getInvokeArguments();
        if (0 < count($argv) and $this->sendArgumentsToAllMethods())
        {
            $params = array_merge($params, $argv);
        }
        $tmp = $info->getPrototypes();
        $signature = array_shift($tmp);
        $parameters = $signature->getParameters();

        $methodParamCount  = count($parameters);
        $requestParamCount = count($params);

        if($requestParamCount > $methodParamCount)
        {
            // additional args added from actindo that we can't handle, throw them away
            $params = array_slice($params, 0, $methodParamCount);
            $request->setParams($params);
        }
        elseif($requestParamCount < $methodParamCount)
        {
            // method was called with less parameters than it requires
            // don't fix, this will throw an error which is probably best
        }
        else {
            // parameter count matched, check types, force int and string
            $paramArray = array();
            for($i = 0, $c = count($parameters); $i < $c; $i++)
            {
                if($parameters[$i] == 'int')
                {
                    $paramArray[] = (int) $params[$i];
                }
                elseif($parameters[$i] == 'string')
                {
                    $paramArray[] = (string) $params[$i];
                }
                else
                {
                    $paramArray[] = $params[$i];
                }
            }
            $request->setParams($paramArray);
        }

        return $request;
    }
    /**
     * tries to find a file path that is writable and sets the absolute path in $cacheFile
     */
    private function setCacheFile()
    {
        return false;
    }
}
