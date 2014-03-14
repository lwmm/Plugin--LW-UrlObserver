<?php

namespace LwUrlObserver\Controller;

class Group
{

    protected $request;
    protected $admin;
    protected $pageIndexes;
    protected $rgrp;
    protected $ligrp;

    public function __construct()
    {
        $this->request = \lw_registry::getInstance()->getEntry("request");
    }

    public function execute()
    {
        if ($this->request->getAlnum("cmd")) {
            $cmd = $this->request->getAlnum("cmd");
        } else {
            $cmd = "showStartPage";
        }

        $method = $cmd . "Action";
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            die("command " . $method . " doesn't exist");
        }
    }

    public function setAdmin($bool)
    {
        if ($bool === true) {
            $this->admin = true;
        } else {
            $this->admin = false;
        }
    }

    protected function isAdmin()
    {
        return $this->admin;
    }

    protected function showStartPageAction($errors = false, $type = false)
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Group\CommandResolver\getGroupsCollection::getInstance()->resolve();
            $collection = $response->getDataByKey("GroupEntitiesCollection");
            $view = new \LwUrlObserver\Views\GroupList();
            $view->setCollection($collection);
            $view->setErrors($errors);
            $view->setType($type);
            $view->setResponse($this->request->getInt("response"));
            return $view->render();
        }
    }

    protected function addEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Group\CommandResolver\add::getInstance()->getInstance(false, array("postArray" => array("group_name" => $this->request->getAlnum("group_name"))))->resolve();
            if ($response->getParameterByKey("error")) {
                return $this->showStartPageAction($response->getDataByKey("error"), "add");
            }
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Group", "cmd" => "showStartPage", "response" => 1)));
        }
    }

    protected function saveEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Group\CommandResolver\save::getInstance()->getInstance(array("id" => $this->request->getInt("id")), array("postArray" => array("group_name" => $this->request->getAlnum("group_name"))))->resolve();
            if ($response->getParameterByKey("error")) {
                return $this->showStartPageAction($response->getDataByKey("error"), "edit");
            }
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Group", "cmd" => "showStartPage", "response" => 2)));
        }
    }

    protected function deleteEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Group\CommandResolver\delete::getInstance(array("id" => $this->request->getInt("id")))->resolve();
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Group", "cmd" => "showStartPage", "response" => 3)));
        }
    }

}
