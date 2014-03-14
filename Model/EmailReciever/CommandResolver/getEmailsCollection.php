<?php

namespace LwUrlObserver\Model\EmailReciever\CommandResolver;

class getEmailsCollection
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
        return new getEmailsCollection($params, $data);
    }
    
    public function resolve()
    {
        $collection = array();

        $QH = new \LwUrlObserver\Model\EmailReciever\DataHandler\QueryHandler();
        $result = $QH->loadAllEmailsByGroupId($this->params["groupId"]);   
        
        foreach($result as $group){
            $entity = new \LwUrlObserver\Model\EmailReciever\Object\Email($group["id"]);
            $entity->setValues($group);
            $collection[] = $entity;
        }
        
        $this->respone->setDataByKey("EmailsEntitiesCollection", $collection);
        return $this->respone;
    }
}