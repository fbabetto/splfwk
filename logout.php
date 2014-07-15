<?php

session_start();
//unset($_SESSION["nome"]);  // where $_SESSION["nome"] is your own variable. if you do not have one use only this as follow **session_unset();**
session_destroy();
session_unset(); //FIXME VEDI SOPRA
//header($_SERVER['HTTP_REFERER']);
//FIXME
header("location: index.php");
?>
