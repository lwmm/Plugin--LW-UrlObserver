<?php

namespace LwUrlObserver\Model\Group\CommandResolver;

class add
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
        return new add($params, $data);
    }

    public function resolve()
    {
        $response = \LwUrlObserver\Model\Group\CommandResolver\getGroupEntityFromPostArray::getInstance(array(), array("postArray" => $this->data["postArray"]))->resolve();
        $entity = $response->getDataByKey("GroupEntity");
        
        $isValidSpecification = \LwUrlObserver\Model\Group\Specification\isValid::getInstance();
        if ($isValidSpecification->isSatisfiedBy($entity)) {
            $SH = new \LwUrlObserver\Model\Group\DataHandler\StorageHandler();
            $id = $SH->addEntity($entity->getValues());

            $this->respone->setParameterByKey('saved', true);
            $this->respone->setParameterByKey('id', $id);
        } else {
            $this->respone->setDataByKey('error', $isValidSpecification->getErrors());
            $this->respone->setParameterByKey('error', true);
        }

        return $this->respone;
    }

}
