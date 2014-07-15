<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fbabetto
 */
interface IParsableHTML {

    function getHTML();

    function setCssFilePath($filePath);

    function setLogged($username);

    function showLoginError($errorMessage);

    function setCurrentPageOnMenu($pagePath);

    function setLoginFormActionPath($pagePath);
}

?>
