<?php

namespace LwUrlObserver\Controller;

class Url
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
            $cmd = "showUrlList";
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
    
    protected function showUrlListAction($errors = false, $type = false)
    {
        $response = \LwUrlObserver\Model\Url\CommandResolver\getGroupNameByGid::getInstance(array("groupId" => $this->request->getInt("gid")))->resolve();
        $groupName = $response->getDataByKey("GroupName");
        
        $response = \LwUrlObserver\Model\Url\CommandResolver\getUrlCollection::getInstance(array("groupId" => $this->request->getInt("gid")))->resolve();
        $collection = $response->getDataByKey("UrlEntitiesCollection");
        
        $view = new \LwUrlObserver\Views\UrlList();
        $view->setResponse($this->request->getInt("response"));
        $view->setGroupId($this->request->getInt("gid"));
        $view->setErrors($errors);
        $view->setType($type);
        $view->setGroupName($groupName);
        $view->setCollection($collection);
        return $view->render();
    }

    protected function addEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Url\CommandResolver\add::getInstance()->getInstance(array("groupId" => $this->request->getInt("gid")), array("postArray" => $this->request->getPostArray()))->resolve();
            if ($response->getParameterByKey("error")) {
                return $this->showUrlListAction($response->getDataByKey("error"), "add");
            }
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Url", "cmd" => "showUrlList", "response" => 1, "gid" => $this->request->getInt("gid"))));
        }
    }

    protected function saveEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Url\CommandResolver\save::getInstance()->getInstance(array("groupId" => $this->request->getInt("gid"), "id" => $this->request->getInt("id")), array("postArray" => $this->request->getPostArray()))->resolve();
            if ($response->getParameterByKey("error")) {
                return $this->showUrlListAction($response->getDataByKey("error"), "edit");
            }
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Url", "cmd" => "showUrlList", "response" => 2, "gid" => $this->request->getInt("gid"))));
        }
    }

    protected function deleteEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LwUrlObserver\Model\Url\CommandResolver\delete::getInstance(array("groupId" => $this->request->getInt("gid"), "id" => $this->request->getInt("id")))->resolve();
            \LwUrlObserver\Services\Page::reload(\LwUrlObserver\Services\Page::getUrl(array("plugin" => "LwUrlObserver", "controller" => "Url", "cmd" => "showUrlList", "response" => 3, "gid" => $this->request->getInt("gid"))));
        }
    }
}
