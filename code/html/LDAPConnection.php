<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LDAPConnection
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/LDAPConnection.php';
require_once dirname(__FILE__) . '/../exception/LDAPConnectionException.php';
require_once dirname(__FILE__) . '/../exception/LDAPBindException.php';

class LDAPConnection implements ILDAPConnection {

    private $ldapHost;
    private $ldapPort;
    private $rdn;
    private $password;
    private $connection;
    private $log;

    //private $bind;

    public function __construct($ldapHost, $ldapPort, $rdn, $password, $log) {
        $this->ldapHost = $ldapHost;
        $this->ldapPort = $ldapPort;
        $this->rdn = $rdn;
        $this->password = $password;
        $this->log = $log;
        if ($this->rdn && $this->password) {
            $this->connection = ldap_connect($this->ldapHost, $this->ldapPort);
            if (!$this->connection) {
                $msg = 'connection to ' . $this->ldapHost . ':' . $this->ldapPort . ' FAILED';
                $this->log->warn($msg);
                throw new LDAPConnectionException($msg);
            }
            @$bind = ldap_bind($this->connection, $this->rdn, $this->password);
            if ($bind != 1) {
                $msg = 'bind to ' . $this->ldapHost . ':' . $this->ldapPort . 'with rdn ' . $this->rdn . ' failed';
                $this->log->warn($msg);
                throw new LDAPBindException($msg);
            }
        } else {
            //$this->log->info('connection to '.$this->ldapHost.':'.$this->ldapPort.' FAILED');
            throw new LDAPConnectionException('connection to ' . $this->ldapHost . ':' . $this->ldapPort . ' FAILED. Trying to connect with empty username or password.');
            echo "failed";
        }
        //echo $bind;
        //echo "ok\n";
    }

    public function closeConnection() {
        ldap_close($this->connection);
    }

    public function getRDN() {
        return $this->rdn;
    }

    public function getGroups($baseDn, $searchQuery) {
        $requiredAttributes = array("memberOf");
        $searchResult = ldap_search($this->connection, $baseDn, "(&(objectClass=person)(sAMAccountName=$searchQuery))", $requiredAttributes);

        $entries = ldap_get_entries($this->connection, $searchResult);
        $groupsRaw = array();

        foreach ($entries as $key => $value) {
            if (is_array($value)) {
                $groupsRaw = array();
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        //print_r($v);
                        foreach ($v as $ind => $gr) {
                            $groupsRawTmp = array();
                            if (!is_int($gr)) {
                                $groupsRawTmp = explode(',', $gr);
                            }

                            $groupsRaw = array_merge($groupsRaw, $groupsRawTmp);
                        }
//						print_r($groupsRaw);
                    }
                }
            }
        }
        $groups = array();
        foreach ($groupsRaw as $g) {
            if (substr($g, 0, 3) == 'CN=') {
                array_push($groups, substr($g, 3));
            }
        }

        return $groups;
    }

    public function getInfo($baseDn, $searchQuery) {
        $requiredAttributes = array("mail", "telephoneNumber", "mobile", "cn"); //cn è uguale a Name
        $searchResult = ldap_search($this->connection, $baseDn, "(&(objectClass=person)(sAMAccountName=$searchQuery))", $requiredAttributes);

        $entries = ldap_get_entries($this->connection, $searchResult);
//		print_r($entries);

        $name = null;
        $email = null;
        $telephoneNumber = null;
        $mobile = null;
        foreach ($entries as $key => $value) {
            if (is_array($value)) {
//				print_r($value['cn'][0]);
                if (array_key_exists('cn', $value) && array_key_exists(0, $value['cn']))
                    $name = $value['cn'][0];
                //print_r($value['mail'][0]);
                if (array_key_exists('mail', $value) && array_key_exists(0, $value['mail']))
                    $email = $value['mail'][0];
                //print_r($value['telephonenumber'][0]);
                if (array_key_exists('telephonenumber', $value) && array_key_exists(0, $value['telephonenumber']))
                    $telephoneNumber = $value['telephonenumber'][0];
                //print_r($value['mobile'][0]);
                if (array_key_exists('mobile', $value) && array_key_exists(0, $value['mobile']))
                    $mobile = $value['mobile'][0];
            }
        }
        $info = array('name' => $name, 'email' => $email, 'telephoneNumber' => $telephoneNumber, 'mobile' => $mobile);
//		print_r($info);
        return $info;
    }

}

?>
