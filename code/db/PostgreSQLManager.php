<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostgreSQLManager
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/DBManager.php';

class PostgreSQLManager extends DBManager implements IDBManager {

    private $connection;

//	public function dbConnect($host, $port, $dbName, $username, $password) {
//		$this->connection= pg_connect('host='.$host.' port='.$port.' dbname='.$dbName.' user='.$username.' password= '.$password);
//		$isSuccessful = FALSE;
//		if($this->characterEncoding) $isSuccessful=pg_set_client_encoding($this->connection);
//		if(!$this->connection || !$isSuccessful) return FALSE;
//		else return TRUE;
//	}

    public function __construct($connectionSettings, $log) {
        parent::__construct($connectionSettings, $log);
        if ($this->type != 'postgresql') {
            throw new DatabaseTypeException("The type of connection passed is not correct: expected 'postgresql', '$this->type' given.\n");
        }
    }

    public function dbConnect() {
        if (!isset($this->host) && !isset($this->port) && isset($this->dbName) && !isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName);
        } elseif (isset($this->host) && !isset($this->port) && isset($this->dbName) && !isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName . ' host=' . $this->host);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && !isset($this->username) && !isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName . ' host=' . $this->host . ' port=' . $this->port);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName . ' host=' . $this->host . ' port=' . $this->port . ' user=' . $this->username . ' password=' . $this->password);
        } elseif (isset($this->host) && isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName . ' host=' . $this->host . ' port=' . $this->port . ' user=' . $this->username . ' password=' . $this->password . ' options=\'--client_encoding=' . $this->characterEncoding);
        } elseif (isset($this->host) && !isset($this->port) && isset($this->dbName) && isset($this->username) && isset($this->password) && !isset($this->characterEncoding)) {
            $this->connection = pg_connect('dbname=' . $this->dbName . ' host=' . $this->host . ' user=' . $this->username . ' password=' . $this->password);
        } else {
            $this->log->fatal('Not enough connection parameters passed to dbConnect');
        }
        if (!$this->connection) {
            $this->log->error('Connection to ' . $this->host . ':' . $this->port . '/' . $this->dbName . ' with username ' . $this->username . '
				failed.');
            $this->log->error(pg_last_error($this->connection));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function dbQuery($query) {
        $escapedQuery = pg_escape_string(utf8_encode($query));
        $this->logQuery($escapedQuery);
        return pg_query($this->connection, $escapedQuery);
    }

    public function dbFetchArray($result) {
        // mode in this case is not used as only associative array are returned and the null should be always on
        return pg_fetch_assoc($result);
    }

    public function dbFreeResult($result) {
        $isSuccessful = pg_free_result($result);
        if (!$isSuccessful) {
            $this->log->error('pg_free_result failed.');
            $this->log->error(pg_last_error($this->connection));
        }
        return $isSuccessful;
    }

    public function dbClose() {
        $isSuccessful = pg_close($this->connection);
        if (!$isSuccessful) {
            $this->log->error('pg_close failed.');
            $this->log->error(pg_last_error($this->connection));
        }
        $this->connection = NULL;
        return $isSuccessful;
    }

}

?>
