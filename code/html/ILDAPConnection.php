<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fbabetto
 */
interface ILDAPConnection {

    function closeConnection();

    function getRDN();

    function getGroups($baseDn, $resultIdentifier);

    function getInfo($baseDn, $searchQuery);
}

?>
