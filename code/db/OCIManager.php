<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OCIManager
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/DBManager.php';

class OCIManager extends DBManager implements IDBManager {

    private $connection;

    public function __construct($connectionSettings, $log) {
        parent::__construct($connectionSettings, $log);
        if ($this->type != 'oci') {
            throw new DatabaseTypeException("The type of connection passed is not correct: expected 'oci', '$this->type' given.\n");
        }
    }

//	public function dbConnect($host, $port, $dbName, $username, $password) {
//	public function dbConnect() {
//		if(!$this->characterEncoding) {
//			$this->connection= oci_connect($this->username, $this->password, $this->host.':'.$this->port.'/'.$this->dbName);
//		}
//		else {
//			$this->connection= oci_connect($this->username, $this->password, $this->host.':'.$this->port.'/'.$this->dbName, $this->characterEncoding);
//		}
//		//gli errori saranno gestiti dal chiamante?
//		if(!$this->connection) return FALSE;
//		else return TRUE;
//	}

    public function dbConnect() {
        if (!isset($this->host) && !isset($this->port) && !isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = oci_connect($this->username, $this->password);
        } elseif (isset($this->host) && !isset($this->port) && !isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = oci_connect($this->username, $this->password, $this->host);
        } elseif (isset($this->host) && isset($this->port) && !isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = oci_connect($this->username, $this->password, $this->host . ':' . $this->port);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = oci_connect($this->username, $this->password, $this->host . ':' . $this->port . '/' . $this->dbName);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && isset($this->characterEncoding)) {
            $this->connection = oci_connect($this->username, $this->password, $this->host . ':' . $this->port . '/' . $this->dbName, $this->characterEncoding);
        } else {
            // gestione errore connessione
            $this->log->fatal('Not enough connection parameters passed to dbConnect');
        }

        if (!$this->connection) {
            $this->log->error('Connection to ' . $this->host . ':' . $this->port . '/' . $this->dbName . ' with username ' . $this->username . 'failed.');
            $errorArray = oci_error();
            $errorMessage = $errorArray['message'];
            $this->log->error($errorMessage);
            return FALSE;
        } else
            return TRUE;
    }

    public function dbQuery($query) {
        $this->logQuery($query);
        $stid = oci_parse($this->connection, $query);
        //qui possono essere sollevate eccezioni che sono gestite dal client
        $isSuccessful = oci_execute($stid); //ritorna un bool
        if (!$isSuccessful) {
            $this->log->error('Query ' . $query . ' failed to be executed.');
        }
        return $stid; // in ogni caso ritorno $stid anche se è andato male perché contiene errore
    }

    public function dbFetchArray($result) {
        return oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS);
    }

    public function dbFreeResult($result) {
        $isSuccessful = oci_free_statement($result);
        if (!$isSuccessful) {
            $errorArray = oci_error($result);
            $errorMessage = $errorArray['message'];
            $this->log->error('oci_free_statement failed.');
            $this->log->error($errorMessage);
        }
        return $isSuccessful;
    }

    public function dbClose() {
        $isSuccessfull = oci_close($this->connection);
        if (!$isSuccessfull) {
            $errorArray = oci_error($this->connection);
            $errorMessage = $errorArray['message'];
            $this->log->error('oci_close failed.');
            $this->log->error($errorMessage);
        }
        $this->connection = NULL;
        return $isSuccessfull;
    }

}

?>
