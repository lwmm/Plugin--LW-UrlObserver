<?php

namespace LwUrlObserver\Model\Url\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }
    
    public function loadAllUrlByGroupId($groupId)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND category_id = :category_id ");
        $this->db->bindParameter("category_id", "i", $groupId);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "url");
        
        return $this->db->pselect();
    }
    
    public function loadUrlById($id)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND id = :id ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "url");
        $this->db->bindParameter("id", "i", $id);
        
        return $this->db->pselect1();
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
    
    public function loadUrlByUrl($url, $groupId)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND opt1text = :opt1text AND category_id = :category_id  ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "url");
        $this->db->bindParameter("opt1text", "s", strtolower($url));
        $this->db->bindParameter("category_id", "i", $groupId);
        
        return $this->db->pselect();
    }
}
