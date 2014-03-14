<?php

namespace LwUrlObserver\Model\EmailReciever\DataHandler;

class StorageHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }
    
    public function addEntity($array, $gid)
    {
        $this->db->setStatement("INSERT INTO t:lw_master (lw_object, name, category_id, description, opt1text) VALUES (:lw_object, :name, :category_id, :description, :opt1text) ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("name", "s", $array["email"]);
        $this->db->bindParameter("category_id", "i", $gid);
        $this->db->bindParameter("opt1text", "s", strtolower($array["email"]));
        $this->db->bindParameter("description", "s", "email");

        $id = $this->db->pdbinsert($this->db->gt("lw_master"));

        if ($id > 0) {
            return $id;
        }
        return false;
    }
    
    public function saveEntity($array, $id, $gid)
    {
        $this->db->setStatement("UPDATE t:lw_master SET name = :name, opt1text = :opt1text WHERE lw_object = :lw_object AND description = :description AND id = :id AND category_id = :category_id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("name", "s", $array["email"]);
        $this->db->bindParameter("opt1text", "s", strtolower($array["email"]));
        $this->db->bindParameter("description", "s", "email");
        $this->db->bindParameter("category_id", "i", $gid);
        
        return $this->db->pdbquery();
    }
    
    public function deleteEntityById($id, $gid)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE id = :id AND lw_object = :lw_object AND description = :description AND category_id = :category_id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "email");
        $this->db->bindParameter("category_id", "i", $gid);
      
        return $this->db->pdbquery();
    }

}
