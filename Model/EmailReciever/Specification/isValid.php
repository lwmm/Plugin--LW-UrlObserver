<?php

namespace LwUrlObserver\Model\EmailReciever\Specification;

define("REQUIRED", 1);    # array( 1 => array( "error" => 1, "options" => "" ));
define("MAXLENGTH", 2);   # array( 2 => array( "error" => 1, "options" => array( "maxlength" => $maxlength, "actuallength" => $strlen ) ));
define("DIGITFIELD", 3);  # array( 6 => array( "error" => 1, "options" => "" ));
define("SYNTAXURL", 4);
define("BOOL", 5);
define("EXISTING", 6);
define("SYNTAXEMAIL", 7);

class isValid extends \LwUrlObserver\Model\Base\Specification\isValidBase
{

    public function __construct()
    {
        $this->errors = array();

        $this->allowedKeys = array(
            "id",
            "email"
        );
    }
    
    public function setGroupId($gid)
    {
        $this->groupId = $gid;
    }

    static public function getInstance()
    {
        return new isValid();
    }

    protected function emailValidate($key, $object)
    {
        $bool = true;
        if (!$this->defaultValidation($key, $object->getValueByKey($key), 255, true)) {
            $bool = false;
        }

        if ($object->getValueByKey($key) != "" && !filter_var($object->getValueByKey($key), FILTER_VALIDATE_EMAIL)) {
            $this->addError($key, SYNTAXEMAIL);
            $bool = false;
        }

        $QH = new \LwUrlObserver\Model\EmailReciever\DataHandler\QueryHandler();
        $result = $QH->loadEmailByEmail($object->getValueByKey($key), $this->groupId);
        
        if (!empty($result)) {
            $bool = false;
            $this->addError($key, EXISTING, array("newEmail" => $object->getValueByKey($key)));
        }
        
        return $bool;
    }

}
