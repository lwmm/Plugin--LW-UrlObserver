<?php

namespace LwUrlObserver\Model\Group\Specification;

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
            "group_name"
        );
    }

    static public function getInstance()
    {
        return new isValid();
    }

    protected function group_nameValidate($key, $object)
    {
        $bool = true;        
        if(!$this->defaultValidation($key, $object->getValueByKey($key), 255, true)){
            $bool = false;
        }
        
        $QH = new \LwUrlObserver\Model\Group\DataHandler\QueryHandler();
        $result = $QH->loadGroupByName($object->getValueByKey($key));   
        
        if(!empty($result)){
            $bool = false;
            $this->addError($key, EXISTING, array("newName" => $object->getValueByKey($key)));
        }
        
        return $bool;
    }

}
