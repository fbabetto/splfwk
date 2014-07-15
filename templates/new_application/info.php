<?php

$simpleframeworkPath = "%EDIT ME!%";
require_once $simpleframeworkPath . '/code/html/HTMLPage.php';

$basePath = dirname(__FILE__);
$page = new HTMLPage($basePath, 'info.php');
session_start();

if (isset($_SESSION) && array_key_exists('logged', $_SESSION)) {
	$username = $_SESSION['logged'];
	$h2 = "Informazioni sull'utente $username";
	$content=null;
	$page->setContent($h2, $content);
	$page->addContent("<p>L'utente $username appartiene ai seguenti gruppi:</p>");
	$groups = $_SESSION['groups'];
	$page->addContent("<ul>\n");
	foreach ($groups as $group) {
		$page->addContent("	<li>$group</li>\n");
	}
	$page->addContent("</ul>\n");
	$page->addContent("<p>Le informazioni dell'utente $username sono le seguenti</p>");
	$page->addContent("<ul>\n");
	$name = $_SESSION['name'];
	$telephoneNumber = $_SESSION['telephoneNumber'];
	$mobile = $_SESSION['mobile'];
	$email = $_SESSION['email'];
	if(isset($name))
		$page->addContent("<li>Nome: $name</li>\n");
	else $page->addContent("<li>Nome: non presente</li>\n");
	if(isset($email))
		$page->addContent("<li>E-mail: $email</li>\n");
	else $page->addContent("<li>E-Mail: non presente</li>\n");
	if(isset($telephoneNumber))
		$page->addContent("<li>Telefono: $telephoneNumber</li>\n");
	else $page->addContent("<li>Telefono: non presente</li>\n");
	if(isset($mobile))
		$page->addContent("<li>Cellulare: $mobile</li>\n");
	else $page->addContent("<li>Cellulare: non presente</li>\n");
	$page->addContent("</ul>\n");
}
else {
	$page->setContent('Informazioni', '<p class="alert">Devi essere autenticato per vedere queste informazioni</p>');
}

echo $page->getHTML();
?>
