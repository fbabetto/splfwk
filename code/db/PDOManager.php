<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDOManager
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/DBManager.php';

class PDOManager extends DBManager implements IDBManager {

    private $connection;

    public function __construct($connectionSettings, $log) {
        parent::__construct($connectionSettings, $log);
        if ($this->type != 'pdo') {
            throw new DatabaseTypeException("The type of connection passed is not correct: expected 'pdo', '$this->type' given.\n");
        }
    }

    public function dbConnect() {
        if (isset($this->host) && !isset($this->port) && isset($this->dbName) && !isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbName");
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && !isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbName");
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbName", $this->username);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbName", $this->username, $this->password);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && isset($this->characterEncoding)) {
            $this->connection = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbName;charset=$this->characterEncoding", $this->username, $this->password);
        } else {
            // gestione errore connessione
            $this->log->fatal('Not enough connection parameters passed to dbConnect');
        }
        if (!$this->connection) {
            $this->log->error('Connection to ' . $this->host . ':' . $this->port . '/' . $this->dbName . ' with username ' . $this->username . 'failed.');
            return FALSE;
        } else
            return TRUE;
    }

    public function dbQuery($query) {//return a PDOStatement
        $escapedQuery = $this->connection->quote($query);
        $this->logQuery($escapedQuery);
        return $this->connection->query($escapedQuery);
    }

    public function dbFetchArray(PDOStatement $result) {
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function dbFreeResult(PDOStatement $result) {
        $isSuccessful = $result->closeCursor();
        if (!$isSuccessful) {
            $this->log->error('PDOStatement::closeCursor() failed.');
            $errorArray = $this->connection->errorInfo();
            $errorMessage = implode('-', $errorArray);
            $this->log->error($errorMessage);
        }
        return $isSuccessful;
    }

    public function dbClose() {
        //http://www.php.net/manual/en/pdo.connections.php
        $this->connection = NULL;
        return TRUE;
    }

}

?>
