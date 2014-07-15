<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fbabetto
 */
interface IDBManager {

    function setOutputCharacterEncoding($characterEncoding); //call before dbConnect

    function dbConnect();

    function dbQuery($query);

    function dbFetchArray($result);

    function dbFreeResult($result);

    function dbClose();
}

?>
