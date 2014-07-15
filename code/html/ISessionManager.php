<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fbabetto
 */
interface ISessionManager {

    function isLogged();

    function getLogged();

    function authenticate($host, $port, $username, $password, $usernamePrefix);

    function getGroups($host, $port, $username, $password, $usernamePrefix, $baseDn, $filter);

    function getInfo($host, $port, $username, $password, $usernamePrefix, $baseDn, $filter);
}

?>
