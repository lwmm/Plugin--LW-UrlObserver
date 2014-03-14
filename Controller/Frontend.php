<?php

namespace LwUrlObserver\Controller;

class Frontend
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
        if ($this->request->getAlnum("controller")) {
            $contr = $this->request->getAlnum("controller");
        } else {
            $contr = "Group";
        }

        $controllerNamespace = "\\LwUrlObserver\\Controller\\" . $contr;

        if (class_exists($controllerNamespace)) {
            $controller = new $controllerNamespace();
            $controller->setAdmin($this->isAdmin());
            return $controller->execute();
        } else {
            die("controller " . $contr . " doesn't exist");
        }
    }

    public function setRootGroup($rgrp)
    {
        $this->rgrp = $rgrp;
    }

    public function setLockedInGroup($ligrp)
    {
        $this->ligrp = $ligrp;
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

}
