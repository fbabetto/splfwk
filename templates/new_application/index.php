<?php
$simpleframeworkPath = "%EDIT ME!%";
require_once $simpleframeworkPath.'/code/html/HTMLPage.php';
require_once $simpleframeworkPath.'/code/log/LogManager.php';
require_once $simpleframeworkPath.'/code/db/OCIManager.php';
require_once $simpleframeworkPath.'/code/db/PostgreSQLManager.php';
$basePath=dirname(__FILE__);

$page = new HTMLPage($basePath, 'index.php');
$h2="Page title here";
$content=<<<EOC
	<p>Insert page content here.</p>
	<p>Another test paragraph.</p>
EOC;

$page->setContent($h2, $content);

$page->addContent('<p>Some additional content you may want to add here.</p>');
$page->addContent('<p>Or here.</p>');

// example of database usage
//$dbConnection = $page->createDbManager('connectionName');
//$dbConnection->dbConnect();
//$query='SELECT * FROM EXAMPLE';
//$result = $dbConnection->dbQuery($query);
//foreach($dbConnection->dbFetchArray($result) as $row) {
//	//DO SOMETHING
//}
//
//$dbConnection->dbFreeResult($result);
//$dbConnection->dbClose();

echo $page->getHTML();

?>