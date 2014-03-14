<?php

namespace LwUrlObserver\Views;

class GroupList
{

    protected $view;

    public function __construct()
    {
        $this->view = new \lw_view(dirname(__FILE__) . '/Templates/GroupList.phtml');
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

    public function render()
    {
        return $this->view->render();
    }

}
