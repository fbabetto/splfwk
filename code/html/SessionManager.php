<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionManager
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/../../constants.php';
require_once dirname(__FILE__) . '/ILDAPConnection.php';
require_once dirname(__FILE__) . '/LDAPConnection.php';

class SessionManager implements ISessionManager {

	private $log;

	public function __construct($log) {
		$this->log = $log;
		if (!isset($_SESSION))
			session_start();
	}

	public function isLogged() {
		return isset($_SESSION['logged']);
	}

	public function getLogged() {
		return $_SESSION['logged'];
	}

	public function authenticate($host, $port, $username, $password, $usernamePrefix) {
		if (!isset($_SESSION['logged'])) {
			$ldapConnection;
			try {
				//print_r($usernamePrefix);
				//print_r($host);
				$ldapConnection = new LDAPConnection($host, $port, $usernamePrefix . $username, $password, $this->log);
			} catch (ErrorException $e) {//recupero eventuali errori di autenticazione ldap
				//echo 'error authenticating with LDAP'.$e->getMessage();
				$this->log->warn('Authentication at host ' . $host . ':' . $port . ' with username ' . $usernamePrefix . $username . ' failed');
				//FIXME NON FUNZIONA IL LOG
				return FALSE;
			}
			$ldapConnection->closeConnection();
			///echo 'Ok, autenticazione, username=\''.$username.'\'';
			$_SESSION['logged'] = $username;
			return TRUE;
		}
		else return TRUE;
	}

	public function getGroups($host, $port, $username, $password, $usernamePrefix, $baseDn, $filter) {
		$ldapConnection;
		try {
			$ldapConnection = new LDAPConnection($host, $port, $usernamePrefix . $username, $password, $this->log);
	}
	catch (ErrorException $e) {
		$this->log->warn('Authentication at host ' . $host . ':' . $port . ' with username ' . $usernamePrefix . $username . ' failed while getting groups.');
		//FIXME
			echo "connection failed\n";
		}
		//echo "ok\n";
		$result=$ldapConnection->getGroups($baseDn, $filter);
		$ldapConnection->closeConnection();
		$_SESSION['groups']=$result;
//		echo $result;
		return $result;
		//ritorna un array col risultato (i gruppi si spera) al chiamante e li salva in sessione
	}

	public function getInfo($host, $port, $username, $password, $usernamePrefix, $baseDn, $filter) {
		$ldapConnection;
		try {
			$ldapConnection = new LDAPConnection($host, $port, $usernamePrefix . $username, $password, $this->log);
	}
	catch (ErrorException $e) {
		$this->log->warn('Authentication at host ' . $host . ':' . $port . ' with username ' . $usernamePrefix . $username . ' failed while getting info.');
		//FIXME
			echo "connection failed\n";
		}
		//echo "ok\n";
		$result=$ldapConnection->getInfo($baseDn, $filter);
		$ldapConnection->closeConnection();
		if(is_array($result)) {
			//array_merge($_SESSION, $result);
			$_SESSION['name']=$result['name'];
			$_SESSION['email']=$result['email'];
			$_SESSION['telephoneNumber']=$result['telephoneNumber'];
			$_SESSION['mobile']=$result['mobile'];

		}


//		echo $result;
		return $result;
		//ritorna un array col risultato (i gruppi si spera) al chiamante e li salva in sessione
	}

}

?>
