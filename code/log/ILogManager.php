<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ILogManager
 *
 * @author fbabetto
 */
interface ILogManager {

    function fatal($message);

    function error($message);

    function warn($message);

    function info($message);

    function debug($message);

    function trace($message);
}

?>
