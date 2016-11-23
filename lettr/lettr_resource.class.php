<?php
/*	--------------------------------------------------------------
	lettr_resource.class.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * @package Lettr
   * @subpackage REST_Client
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  abstract class Lettr_Resource {
    private $client;
    private $path;
    
    protected function __construct($path){
      $this->client = new Lettr_Client();
      $this->path   = $path;
    }
    
    public function get($id){
      return $this->query('get', $this->path . '/' . $id);
    }
    
    public function create($data){
      if(!is_array($data)){
        throw new Lettr_IllegalArgumentException("Erwarte ein assoziatives Array als Parameter");
      }
      return $this->query('post', $this->path, $data);
    }
    
    public function update($id, $data){
      return $this->query('put', $this->path . '/' . $id, $data);
    }
    
    public function delete($id){
      return $this->query('delete', $this->path . '/' . $id);
    }
    
    public function custom($method, $action, $data = null){
      return $this->query($method, $this->path . "/$action", $data);
    }
    
    public function customId($method, $id, $action, $data = null){
      return $this->query($method, $this->path . "/$id/$action", $data);
    }
    
    protected function query($method, $path, $data = null){
      $res = $this->client->$method($path, $data);
      return $res['data'];
    }
  }