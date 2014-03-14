<?php

namespace LwUrlObserver\Model\Group\CommandResolver;

class getGroupsCollection
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
        return new getGroupsCollection($params, $data);
    }
    
    public function resolve()
    {
        $collection = array();

        $QH = new \LwUrlObserver\Model\Group\DataHandler\QueryHandler();
        $result = $QH->loadAllGroups();   
        
        foreach($result as $group){
            $group["observed_urls"] = $QH->getAmountOfUrlByGroupId($group["id"]);
            $entity = new \LwUrlObserver\Model\Group\Object\Group($group["id"]);
            $entity->setValues($group);
            $collection[] = $entity;
        }
        
        $this->respone->setDataByKey("GroupEntitiesCollection", $collection);
        return $this->respone;
    }
}