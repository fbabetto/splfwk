<?php
require_once dirname(__FILE__) . '/constants.php';
session_name('admin');
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>SimpleFramework</title>
            <link rel="stylesheet" type="text/css" href="templates/style.css"/>
    </head>
    <body>
        <div id="header">
            <h1>SimpleFramework</h1>
        </div>
        <div id="adminmenu">
            <ul>
                <?php
                if (array_key_exists('logged', $_SESSION)) {
                    echo<<<EOM
					<li>Autenticato come amministratore</li>
					<li>
						<a href="logout_admin.php">Logout</a>
					</li>

EOM;
                } else {
                    echo "<li>Non sei autenticato</li>\n";
                }
                ?>
            </ul>
        </div>
        <div id="simpleframeworkcontent">
            <?php
            //echo `whoami`;
            if (isset($_SESSION) && array_key_exists('logged', $_SESSION)) {
                //SONO AUTENTICATO
                if (!isset($_GET) || !array_key_exists('application_name', $_GET) || $_GET['application_name'] === '') {
                    //if(getenv('APACHE_RUN_USER')) {
                    if (is_writable(dirname(__DIR__))) {
                        echo '<h2>Gestione applicazioni</h2>';
                        $user = exec('whoami');
//						echo '<p>La nuova applicazione verrà creata in ' . dirname(__DIR__) . ' e tale cartella deve essere scrivibile dall\'utente di apache "' . getenv('APACHE_RUN_USER') . '"</p>' . "\n";
                        echo '<p>La nuova applicazione verrà creata in ' . dirname(__DIR__) . ' e tale cartella deve essere scrivibile dall\'utente di apache "' . $user . '"</p>' . "\n";
                        echo<<<EOF
			<form method="get" action="index.php">
			<fieldset>
			<legend>Crea una nuova applicazione per SimpleFramework</legend>
			<label for="name">Nome: </label>
			<input type="text" name="application_name"/ size="20">
			</fieldset>
			<input type="submit" value="crea">
			</form>
			</div>
EOF;
                    } else {
                        echo "<p class=\"alert\">La cartella " . dirname(__DIR__) . " non è scrivibile, il framework non può creare l'applicazione; dovrai crearla manualmente.</p>\n";
                        echo "<ul>\n";
                        echo "<li>Copia la cartella " . dirname(__DIR__) . "/templates/new_application dove vuoi installare la nuova applicazione e rinominala;</li>\n";
                        echo "<li>modifica il percorso assoluto del framework nella variabile <code>\$simpleframeworkPath</code> nel file index.html;</li>\n";
                        echo "<li><p>crea il file <code>logout.php</code> con il seguente contenuto:</p>\n";
                        echo '<p><code>&lt;?php include \'%simpleframework_path%/logout.php\'; ?&gt;</code></p>' . "\n";
                        echo "dove <code>%simpleframework_path%</code> è il percorso assoluto di installazione del framework.</li>.\n";
                        echo "</ul>\n";
                    }
                } else {
                    echo "<div id=\"content\">\n";
                    $newApplicationPath = '../' . htmlspecialchars($_GET['application_name']);
                    //echo $path;
                    if (isset($newApplicationPath)) {
                        echo '<p>' . $newApplicationPath . '</p>';
                        $isSuccessful = mkdir($newApplicationPath . '/templates', 0775, TRUE);
                        if ($isSuccessful) {

                            echo '<p>' . FWK_BASEPATH . '/templates/new_application/' . '</p>';
                            $handle = fopen($newApplicationPath . '/logout.php', 'w');
                            $stringToWrite = '<?php include \'' . FWK_BASEPATH . '/logout.php\'; ?>';
                            //echo $stringToWrite;
                            $isSuccessful = fwrite($handle, $stringToWrite);
                            fclose($handle);
                            if ($isSuccessful) {
                                echo shell_exec('cp -R ' . FWK_BASEPATH . '/templates/new_application/* ' . $newApplicationPath);
                                //echo $isSuccessful;
                                //if ($isSuccessful) {
                                echo shell_exec('chmod -R 775 ' . $newApplicationPath);
                                //	echo $isSuccessful;
                                //}
                            }

                            setFwkPath($newApplicationPath . '/index.php');
                            setFwkPath($newApplicationPath . '/info.php');
                        }
                        //if (!$isSuccessful) {
                        //	echo "<p class=\"alert\">Permission problems while creating the new application.</p>\n";
                        //}

                        echo "</div>\n";
                    } else {
                        echo "<p class=\"alert\">The application name is not valid.</p>\n";
                    }
                }
            } else {
                //
                if (isset($_POST) && array_key_exists('username', $_POST) && array_key_exists('password', $_POST)) {
                    if (htmlspecialchars($_POST['username']) === 'admin' && htmlspecialchars($_POST['password']) === 'arpavArpav') {//FIXME PASSWORD SHOULD NOT BE HARDCODED HERE!
                        $_SESSION['logged'] = htmlspecialchars($_POST['username']);
                        header("location: index.php");
                    } else
                        echo '<div>Autenticazione Fallita.</div>';
                }
                echo '<h2>Login</h2>' . "\n";
                echo<<<EOF
		<p class="alert">Devi essere autorizzato per creare una nuova applicazione.</p>
		<form method="post" action="index.php">
			<fieldset>
				<legend>Accesso Amministratore</legend>
				<label for="nome">Nome utente:</label><br><input type="text" name="username" size="20"><p></p>
				<label for="password">Password:</label><br><input type="password" name="password" size="20">
			</fieldset>
<input type="submit" value="conferma">
</form>
EOF;
            }

            function setFwkPath($filePath) {
                $handle = fopen($filePath, 'r+');
                $cont = fread($handle, filesize($filePath));
                fclose($handle);
                //echo $cont;
                $handle = fopen($filePath, 'w');
                $newIncludeString = FWK_BASEPATH;
                $pattern = '%EDIT ME!%';
                $newIndexContent = str_replace($pattern, $newIncludeString, $cont);
                fwrite($handle, $newIndexContent);
                fclose($handle);
            }

            function copyr($source, $dest) {
                // recursive function to copy
                // all subdirectories and contents:
                if (is_dir($source)) {
                    $dir_handle = opendir($source);
                    $sourcefolder = basename($source);
                    mkdir($dest . "/" . $sourcefolder);
                    while ($file = readdir($dir_handle)) {
                        if ($file != "." && $file != "..") {
                            if (is_dir($source . "/" . $file)) {
                                copyr($source . "/" . $file, $dest . "/" . $sourcefolder);
                                chmod($dest . "/" . $sourcefolder, 0775);
                            } else {
                                copy($source . "/" . $file, $dest . "/" . $file);
                                chmod($dest . "/" . $file, 0775);
                            }
                        }
                    }
                    closedir($dir_handle);
                } else {
                    // can also handle simple copy commands
                    copy($source, $dest);
                }
            }
            ?>
        </div>
        <div id="footer">
            simpleframework application
        </div>
    </body>
</html>