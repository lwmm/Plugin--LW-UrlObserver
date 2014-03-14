<?php

namespace LwUrlObserver\Model\Group\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }
    
    public function loadAllGroups()
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "group");
        
        return $this->db->pselect();
    }
    
    public function loadGroupByName($name)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description AND opt1text = :opt1text ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "group");
        $this->db->bindParameter("opt1text", "s", strtolower($name));
        
        return $this->db->pselect();
    }
    
    public function getAmountOfUrlByGroupId($id)
    {
        $this->db->setStatement("SELECT COUNT(*) as anzahl FROM t:lw_master WHERE category_id = :category_id AND lw_object = :lw_object AND description = :description ");
        $this->db->bindParameter("category_id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "url");

        $result = $this->db->pselect1();
        return $result["anzahl"];
    }
}
