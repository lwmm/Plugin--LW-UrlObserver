<?php

namespace LwUrlObserver\Model\Url\Specification;

define("REQUIRED", 1);    # array( 1 => array( "error" => 1, "options" => "" ));
define("MAXLENGTH", 2);   # array( 2 => array( "error" => 1, "options" => array( "maxlength" => $maxlength, "actuallength" => $strlen ) ));
define("DIGITFIELD", 3);  # array( 6 => array( "error" => 1, "options" => "" ));
define("SYNTAXURL", 4);
define("BOOL", 5);
define("EXISTING", 6);

class isValid extends \LwUrlObserver\Model\Base\Specification\isValidBase
{

    public function __construct()
    {
        $this->errors = array();

        $this->allowedKeys = array(
            "id",
            "url",
            "reachable"
        );
    }

    public function setValidationTypeEdit($type = false)
    {
        $this->edit = $type;
    }
    
    public function setGroupId($gid)
    {
        $this->groupId = $gid;
    }

    static public function getInstance()
    {
        return new isValid();
    }

    protected function urlValidate($key, $object)
    {
        $bool = true;
        if (!$this->defaultValidation($key, $object->getValueByKey($key), 255, true)) {
            $bool = false;
        }

        if ($object->getValueByKey($key) != "" && !filter_var($object->getValueByKey($key), FILTER_VALIDATE_URL)) {
            $this->addError($key, SYNTAXURL);
            $bool = false;
        }


        $QH = new \LwUrlObserver\Model\Url\DataHandler\QueryHandler();
        if ($this->edit) {
            $result = $QH->loadUrlById($object->getId());
            if ($result["opt1text"] != strtolower($object->getValueByKey($key))) {
                $result = $QH->loadUrlByUrl($object->getValueByKey($key), $this->groupId);
                if (!empty($result)) {
                    $bool = false;
                    $this->addError($key, EXISTING, array("newUrl" => $object->getValueByKey($key)));
                }
            }
        } else {
            $result = $QH->loadUrlByUrl($object->getValueByKey($key), $this->groupId);
            if (!empty($result)) {
                $bool = false;
                $this->addError($key, EXISTING, array("newUrl" => $object->getValueByKey($key)));
            }
        }
        
        return $bool;
    }

    protected function reachableValidate($key, $object)
    {
        $bool = true;

        if ($object->getValueByKey($key) != "" && !ctype_digit($object->getValueByKey($key))) {
            $this->addError($key, DIGITFIELD);
            $bool = false;
        }
        if ($object->getValueByKey($key) != "" && $object->getValueByKey($key) > 1) {
            $this->addError($key, BOOL);
            $bool = false;
        }

        return $bool;
    }

}
