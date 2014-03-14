<?php

namespace LwUrlObserver\Model\EmailReciever\CommandResolver;

class getGroupNameByGid
{

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
        return new getGroupNameByGid($params, $data);
    }

    public function resolve()
    {
        $QH = new \LwUrlObserver\Model\EmailReciever\DataHandler\QueryHandler();               
        $this->respone->setDataByKey('GroupName', $QH->loadGroupNameById($this->params["groupId"]));

        return $this->respone;
    }

}
