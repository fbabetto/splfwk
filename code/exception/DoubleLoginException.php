<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DoubleLoginException
 *
 * @author fbabetto
 */
class DoubleLoginException extends ErrorException {

    public function __construct($message) {
        parent::__construct($message);
    }

}

?>
