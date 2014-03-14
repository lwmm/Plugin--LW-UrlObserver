<?php

namespace LwUrlObserver\Model\Url\CommandResolver;

class getUrlEntityFromPostArray
{
    protected $request;
    protected $respone;
    protected $params;
    protected $data;

    public function __construct($params, $data)
    {
        $this->params = $params;
        $this->data = $data;
        $this->respone = \LwUrlObserver\Services\Response::getInstance();
    }
    
    public static function getInstance($params = false, $data = false)
    {
        return new getUrlEntityFromPostArray($params, $data);
    }
    
    public function resolve()
    {       
        $id = false;
        if(isset($this->params["id"])){
            $id = $this->params["id"];
        }
        
        $entity = new \LwUrlObserver\Model\Url\Object\Url($id);
        $entity->setValues($this->data["postArray"]);     

        $this->respone->setDataByKey("UrlEntity", $entity);
        return $this->respone;
    }
}