<?php

namespace LwUrlObserver\Model\Url\CommandResolver;

class getUrlCollection
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
        return new getUrlCollection($params, $data);
    }
    
    public function resolve()
    {
        $collection = array();

        $QH = new \LwUrlObserver\Model\Url\DataHandler\QueryHandler();
        $result = $QH->loadAllUrlByGroupId($this->params["groupId"]);   
        
        foreach($result as $group){
            $entity = new \LwUrlObserver\Model\Url\Object\Url($group["id"]);
            $entity->setValues($group);
            $collection[] = $entity;
        }
        
        $this->respone->setDataByKey("UrlEntitiesCollection", $collection);
        return $this->respone;
    }
}