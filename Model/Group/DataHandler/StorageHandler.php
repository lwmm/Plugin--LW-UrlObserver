<?php

namespace LwUrlObserver\Model\Group\DataHandler;

class StorageHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }

    public function addEntity($array)
    {
        $this->db->setStatement("INSERT INTO t:lw_master (lw_object, name, description, opt1text) VALUES (:lw_object, :name, :description, :opt1text) ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("name", "s", $array["group_name"]);
        $this->db->bindParameter("opt1text", "s", strtolower($array["group_name"]));
        $this->db->bindParameter("description", "s", "group");

        $id = $this->db->pdbinsert($this->db->gt("lw_master"));

        if ($id > 0) {
            return $id;
        }
        return false;
    }

    public function saveEntity($array, $id)
    {
        $this->db->setStatement("UPDATE t:lw_master SET name = :name, opt1text = :opt1text WHERE lw_object = :lw_object AND description = :description AND id = :id");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("name", "s", $array["group_name"]);
        $this->db->bindParameter("opt1text", "s", strtolower($array["group_name"]));
        $this->db->bindParameter("description", "s", "group");

        return $this->db->pdbquery();
    }

    public function deleteEntityById($id)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE id = :id AND lw_object = :lw_object AND description = :description ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "group");

        return $this->db->pdbquery();
    }

}
