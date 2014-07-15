<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateFileNotFoundException
 *
 * @author fbabetto
 */
class TemplateFileNotFoundException extends ErrorException {

    public function __construct($message) {
        parent::__construct($message);
    }

}

?>
