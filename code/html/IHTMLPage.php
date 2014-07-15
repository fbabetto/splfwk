<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fbabetto
 */
interface IHTMLPage {

    function addContent($contentToAdd);

    function setContent($contentTitle, $contentToSet);

    function getHTML();

    function createDbManager($connectionName);
}

?>
