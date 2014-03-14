<?php

namespace LwUrlObserver\Model\EmailReciever\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }

    public function loadGroupNameById($id)
    {
        $this->db->setStatement("SELECT name FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND id = :id ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "group");
        $this->db->bindParameter("id", "i", $id);

        $result = $this->db->pselect1();
        return $result["name"];
    }

    public function loadEmailByEmail($email, $groupId)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE opt1text = :opt1text AND lw_object = :lw_object AND description = :description AND category_id = :category_id ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "email");
        $this->db->bindParameter("opt1text", "s", strtolower($email));
        $this->db->bindParameter("category_id", "i", $groupId);

        return $this->db->pselect();
    }
    
    public function loadAllEmailsByGroupId($groupId)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND category_id = :category_id ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "email");
        $this->db->bindParameter("category_id", "i", $groupId);
        
        return $this->db->pselect();
    }

}
