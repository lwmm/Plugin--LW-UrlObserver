<?php

$observedElements = array();
$emails = array();
$errors = array(
    "errorReachableUrlIsNotReachable" => array(),
    "errorNotReachableUrlIsReachable" => array()
);
$dailyMailWasSent = true;
$lastScan = 0;

$hour = 12; # Uhrzeit ab wann die Taegliche "alles ok" mail geschickt werden soll
$min = 10;


error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

$config = parse_ini_file("/var/www/c38/lw_configs/conf.inc.php", true);

class Autoloader
{

    public function __construct($config)
    {
        $this->config = $config;
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className)
    {
        if (strstr($className, 'lw_')) {
            $path = $this->config["path"]["framework"] . "lw/";
            $filename = $path . $className . ".class";
        }

        $filename = str_replace('\\', '/', $filename) . '.php';

        if (is_file($filename)) {
            include_once($filename);
        }
    }

}

class UrlCheck
{

    protected function contains($str, $needle)
    {
        return (strpos($str, $needle) !== false);
    }

    public function check($url)
    {
        $headers = get_headers($url);
        return (isset($headers) && count($headers) > 0 && $this->contains($headers[0], "200"));
    }

}

$autoloader = new Autoloader($config);
if ($config["lwdb"]["type"] == "mysql" || $config["lwdb"]["type"] == "mysqli") {
    include_once($config["path"]["framework"] . "lw/lw_db_mysqli.class.php");
    $db = new \lw_db_mysqli($config["lwdb"]["user"], $config["lwdb"]["pass"], $config["lwdb"]["host"], $config["lwdb"]["name"]);
    $db->connect();
} elseif ($config["lwdb"]["type"] == "oracle") {
    include_once($config["path"]["framework"] . "lw/lw_db_oracle.class.php");
    $db = new \lw_db_oracle($config["lwdb"]["user"], $config["lwdb"]["pass"], $config["lwdb"]["host"], $config["lwdb"]["name"]);
    $db->connect();
}


$db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
$db->bindParameter("lw_object", "s", "lw_url_observer");
$db->bindParameter("description", "s", "group");

$result = $db->pselect();
foreach ($result as $group) {
    $observedElements[$group["id"]] = array("name" => $group["name"]);
}

$db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
$db->bindParameter("lw_object", "s", "lw_url_observer");
$db->bindParameter("description", "s", "url");

$result = $db->pselect();
foreach ($result as $url) {
    $observedElements[$url["category_id"]]["urls"][] = array("url" => $url["url"], "reachable" => $url["opt1bool"]);
}

$db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
$db->bindParameter("lw_object", "s", "lw_url_observer");
$db->bindParameter("description", "s", "email");

$result = $db->pselect();
foreach ($result as $email) {
    $emails[$email["name"]][] = $email["category_id"];
}

$urlCheck = new UrlCheck();
foreach ($observedElements as $id => $element) {
    foreach ($element["urls"] as $url) {
        $urlReachable = $urlCheck->check($url["url"]);

        if ($url["reachable"] && !$urlReachable) {
            $errors["errorReachableUrlIsNotReachable"][$id][] = $url["url"];
        }

        if (!$url["reachable"] && $urlReachable) {
            $errors["errorNotReachableUrlIsReachable"][$id][] = $url["url"];
        }
    }
}

$db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
$db->bindParameter("lw_object", "s", "lw_url_observer");
$db->bindParameter("description", "s", "scan");
$result = $db->pselect();

if (empty($result)) {
    $date = date("YmdHis");
    $db->setStatement("INSERT INTO t:lw_master (lw_object, description, lw_first_date, lw_last_date) VALUES (:lw_object, :description, :lw_first_date, :lw_last_date) ");
    $db->bindParameter("lw_object", "s", "lw_url_observer");
    $db->bindParameter("description", "s", "scan");
    $db->bindParameter("lw_first_date", "i", $date);
    $db->bindParameter("lw_last_date", "i", $date);
    die($db->prepare());
    $result = $db->pdbquery();
}

$db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND description = :description ");
$db->bindParameter("lw_object", "s", "lw_url_observer");
$db->bindParameter("description", "s", "scan");
$result = $db->pselect1();

$lastScan = $result["lw_last_date"];
$day = substr($lastScan, 6, 2);

if (date("d") > $day && date("H") > $hour && date("i") > $min) {
    $dailyMailWasSent = false;
}

if (!$dailyMailWasSent) {
    $db->setStatement("UPDATE t:lw_master SET lw_last_date = :lw_last_date WHERE lw_object = :lw_object AND description = :description ");
    $db->bindParameter("lw_object", "s", "lw_url_observer");
    $db->bindParameter("description", "s", "scan");
    $db->bindParameter("lw_last_date", "i", date("YmdHis"));

    $result = $db->pdbquery();
}

$message = "";

$header = "******************************************************************" . PHP_EOL;
$header.= "** logicworks - Url Beobachtung                                 **" . PHP_EOL;
$header.= "**                                                              **" . PHP_EOL;
$header.= "** STATUS BERICHT " . date("d.m.Y H:i:s") . "                           **" . PHP_EOL;
$header.= "**                                                              **" . PHP_EOL;
$header.= "******************************************************************" . PHP_EOL;
$header.= PHP_EOL . PHP_EOL;

foreach ($emails as $email => $groups) {
    $errString = "";
    $errosFound = false;
    foreach ($groups as $group) {
        $errString.="Url-Gruppe: " . $observedElements[$group]["name"] . PHP_EOL . PHP_EOL;
        if (isset($errors["errorReachableUrlIsNotReachable"][$group])) {
            $errosFound = true;
            foreach ($errors["errorReachableUrlIsNotReachable"][$group] as $errUrl) {
                $errString.= "[ " . $errUrl . " ] -- ERWARTETER STATUS [ erreichbar ] -- AKTUELLER STATUS [ nicht erreichbar ]" . PHP_EOL;
            }
        }
        if (isset($errors["errorNotReachableUrlIsReachable"][$group])) {
            $errosFound = true;
            foreach ($errors["errorNotReachableUrlIsReachable"][$group] as $errUrl) {
                $errString.= "[ " . $errUrl . " ] -- ERWARTETER STATUS [ nicht erreichbar ] -- AKTUELLER STATUS [ erreichbar ]" . PHP_EOL;
            }
        }
        $errString.= PHP_EOL;
    }

    if ($errosFound) {
        $content = "Es wurden folgende Fehler gefunden :" . PHP_EOL . PHP_EOL;
        #mail($email, 'logicworks - Url Beobachtung', $header.$content.$errString."".PHP_EOL);
    } else {
        $content = "Alles in Ordnung." . PHP_EOL . PHP_EOL;
        if (!$dailyMailWasSent) {
            #mail($email, 'logicworks - Url Beobachtung', $header.$content.$errString."".PHP_EOL);
        }
    }
}
