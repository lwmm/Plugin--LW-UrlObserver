<?php

namespace LwUrlObserver\Model\EmailReciever\CommandResolver;

class save
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
        return new save($params, $data);
    }

    public function resolve()
    {
        $response = \LwUrlObserver\Model\EmailReciever\CommandResolver\getEmailEntityFromPostArray::getInstance(array(), array("postArray" => $this->data["postArray"]))->resolve();
        $entity = $response->getDataByKey("EmailEntity");

        $isValidSpecification = \LwUrlObserver\Model\EmailReciever\Specification\isValid::getInstance();
        $isValidSpecification->setGroupId($this->params["groupId"]);
        if ($isValidSpecification->isSatisfiedBy($entity)) {
            $SH = new \LwUrlObserver\Model\EmailReciever\DataHandler\StorageHandler();
            $SH->saveEntity($entity->getValues(), $this->params["id"], $this->params["groupId"]);

            $this->respone->setParameterByKey('saved', true);
        } else {
            $errors = $isValidSpecification->getErrors();
            $errors["id"] = $this->params["id"];
            $this->respone->setDataByKey('error', $errors);
            $this->respone->setParameterByKey('error', true);
        }

        return $this->respone;
    }

}
