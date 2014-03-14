<?php

class lw_url_observer extends lw_plugin
{

    protected $request;

    public function __construct()
    {
        parent::__construct();
        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \LwUrlObserver\Services\Autoloader();
    }

    public function buildPageOutput()
    {
        $this->response->useJQuery();
        if (isset($this->params["admin"]) && $this->params["admin"] == 1) {
            $admin = true;
        } else {
            $admin = false;
        }        

        $controller = new \LwUrlObserver\Controller\Frontend();
        $controller->setAdmin($admin);
        return $controller->execute();
    }

    public function getOutput()
    {
        return "";
    }

    public function deleteEntry()
    {
        return true;
    }

}
