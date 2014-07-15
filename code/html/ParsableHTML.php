<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParsableHTML
 *
 * @author fbabetto
 */
// http://howtoprog.com/it/contents/php_e_domdocument-modificare_e_eliminare_un_nodo/
//FIXME USANDO http://www.ultramegatech.com/2009/07/generating-xhtml-documents-using-domdocument-in-php/
//sistemare usando load/save xml
require_once dirname(__FILE__) . '/../exception/ParseHTMLException.php';

class ParsableHTML implements IParsableHTML {

    private $pElement;
    private $log;

    public function __construct($htmlCode, $log) {//FIXME METTERE $LOG DI TIPO LOGGER IDEM PER ALTRE CLASSI
        $this->pElement = new DOMDocument();
        $this->pElement->validateOnParse = true;
        $this->pElement->formatOutput = TRUE;
        libxml_use_internal_errors(true);
        $this->pElement->loadHTML($htmlCode);
        $this->log = $log;
        //FIXME CONTROLLARE SE CARICAMENTO HA AVUTO SUCCESSO
        //basta usare debug errori visto sul vecchio progetto
        //echo $this->pElement->saveXML();
    }

    public function getHTML() {
        return $this->pElement->saveHTML();
    }

    public function setCssFilePath($filePath) {
        // the css could be included by link tag or with '@'FIXME
        $link = $this->pElement->getElementsByTagName('link');
        $link->item(0)->setAttribute('href', 'templates/style.css');
    }

    public function setLogged($username) {
        $login = $this->pElement->getElementById('login');
        $form = $login->getElementsByTagName('form');
        $login->removeChild($form->item(0));
        $a = $this->pElement->createElement('a', $username);
        $a->setAttribute('class', 'username');
        $a->setAttribute('href', 'info.php');
        $p = $this->pElement->createElement('p');
        $login->appendChild($p);
        $p->appendChild($a);
        $logoutLink = $this->pElement->createElement('a', 'Logout');
        $logoutLink->setAttribute('href', 'logout.php');
        $login->appendChild($logoutLink);
    }

    public function showLoginError($errorMessage) {
        $login = $this->pElement->getElementById('login');
        $p = $this->pElement->createElement('p', $errorMessage);
        $p->setAttribute('class', 'error');
        $login->appendChild($p);
    }

    public function setCurrentPageOnMenu($pagePath) {
        $menu = $this->pElement->getElementById('menu');
        $entries = $menu->getElementsByTagName('li');
        foreach ($entries as $entry) {
            foreach ($entry->childNodes as $child) {
                if ($child->hasAttributes()) {
                    if ($child->getAttribute('href') === $pagePath) {
                        //echo $child->getNodePath();
                        $parent = $child->parentNode;
                        $pageName = $child->nodeValue;
                        $parent->removeChild($child);
                        $parent->nodeValue = $pageName;
                        $parent->setAttribute('id', 'activepage');
                    }
                }
            }
        }
    }

    public function setLoginFormActionPath($pagePath) {
        $login = $this->pElement->getElementById('login');
        $form = $login->getElementsByTagName('form');
        $form->item(0)->setAttribute('action', $pagePath);
    }

}

?>
