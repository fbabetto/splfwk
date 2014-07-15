<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SettingsFileNotFoundException
 *
 * @author fbabetto
 */
class SettingsFileNotFoundException extends ErrorException {

    public function __construct($message) {
        parent::__construct($message);
    }

}

?>
