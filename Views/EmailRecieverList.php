<?php

namespace LwUrlObserver\Views;

class EmailRecieverList
{

    protected $view;

    public function __construct()
    {
        $this->view = new \lw_view(dirname(__FILE__) . '/Templates/EmailRecieverList.phtml');
    }
    
    public function setCollection($collection)
    {
        $this->view->collection = $collection;
    }
    
    public function setErrors($errors)
    {
        $this->view->errors = $errors;
    }
    
    public function setResponse($response)
    {
        $this->view->response = $response;
    }
    
    public function setType($type)
    {
        $this->view->type = $type;
    }
    
    public function setGroupId($gid)
    {
        $this->view->groupId = $gid;
    }
    
    public function setGroupName($groupName)
    {
        $this->view->groupName = $groupName;
    }

    public function render()
    {
        return $this->view->render();
    }

}
