<?php

namespace LwUrlObserver\Model\Url\DataHandler;

class StorageHandler
{

    protected $db;

    public function __construct()
    {
        $this->db = \lw_registry::getInstance()->getEntry("db");
    }

    public function addEntity($array, $gid)
    {
        $this->db->setStatement("INSERT INTO t:lw_master (lw_object, url, category_id, description, opt1text, opt1bool) VALUES (:lw_object, :url, :category_id, :description, :opt1text, :opt1bool) ");
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("url", "s", $array["url"]);
        $this->db->bindParameter("category_id", "i", $gid);
        $this->db->bindParameter("opt1text", "s", strtolower($array["url"]));
        $this->db->bindParameter("opt1bool", "i", $array["reachable"]);
        $this->db->bindParameter("description", "s", "url");

        $id = $this->db->pdbinsert($this->db->gt("lw_master"));

        if ($id > 0) {
            return $id;
        }
        return false;
    }

    public function saveEntity($array, $id, $gid)
    {
        $this->db->setStatement("UPDATE t:lw_master SET url = :url, opt1text = :opt1text, opt1bool = :opt1bool WHERE lw_object = :lw_object AND description = :description AND id = :id AND category_id = :category_id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("url", "s", $array["url"]);
        $this->db->bindParameter("opt1text", "s", strtolower($array["url"]));
        $this->db->bindParameter("opt1bool", "i", $array["reachable"]);
        $this->db->bindParameter("description", "s", "url");
        $this->db->bindParameter("category_id", "i", $gid);

        return $this->db->pdbquery();
    }

    public function deleteEntityById($id, $gid)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE id = :id AND lw_object = :lw_object AND description = :description AND category_id = :category_id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("lw_object", "s", "lw_url_observer");
        $this->db->bindParameter("description", "s", "url");
        $this->db->bindParameter("category_id", "i", $gid);

        return $this->db->pdbquery();
    }

}
