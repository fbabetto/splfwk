<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBManager
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/IDBManager.php';

abstract class DBManager implements IDBManager {

    protected $type;
    protected $host;
    protected $port;
    protected $dbName;
    protected $username;
    protected $password;
    protected $characterEncoding;
    protected $log;

    protected function __construct($connectionSettings, $log) {

        $this->log = $log;

        if (key_exists('type', $connectionSettings)) {
            $this->type = $connectionSettings['type'];
        }
        if (key_exists('address', $connectionSettings)) {
            $this->host = $connectionSettings['address'];
        }
        if (key_exists('port', $connectionSettings)) {
            $this->port = $connectionSettings['port'];
        }
        if (key_exists('db', $connectionSettings)) {
            $this->dbName = $connectionSettings['db'];
        }
        if (key_exists('username', $connectionSettings)) {
            $this->username = $connectionSettings['username'];
        }
        if (key_exists('password', $connectionSettings)) {
            $this->password = $connectionSettings['password'];
        }
        if (key_exists('encoding', $connectionSettings)) {
            $this->characterEncoding = $connectionSettings['encoding'];
        }
    }

    public function setOutputCharacterEncoding($characterEncoding) {
        $this->characterEncoding = $characterEncoding;
    }

    protected function logQuery($query) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $username = "not set";
        if (array_key_exists('logged', $_SESSION)) {
            $username = $_SESSION['logged'];
        }
        $query = trim($query);
        $command = substr($query, 0, 6);
        $command = strtoupper($command);
        if ($command != 'SELECT') {
            $this->log->info("username='$username'\nSQL=$query");
        }
    }

}

?>
