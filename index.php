<?php    
    spl_autoload_extensions(".php,.class.php,.inc");
    spl_autoload_register();
    
    define("CONTROLLERS_NAMESPACE", "classes\\controllers\\");
    define("CONTROLLERS_DIR", "classes/controllers/");
    define("TEMPLATES_DIR", "templates\\");
    define("SERVICES_DIR", "classes\\services");
    
    use classes\controllers\View;
    use classes\controllers\BottomController;
    use classes\controllers\TopController;
    use classes\controllers\LoginController;
    use classes\models\Settings;
    use classes\utils\Utils;

	include_once("classes/init.inc");
				
	secure_session_start();
	Utils::deleteOldTmpFiles();
    
    $class = isset($_GET['class']) ? $_GET['class'] : NULL;
    $action = isset($_GET['action']) ? $_GET['action'] : NULL;
	$id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $param = isset($_GET['param']) ? $_GET['param'] : NULL;
        
	$main = new View;    
	$main->header = getHeader();
	$main->bottom = getBottom();
	$main->top = getTop();
	$main->error = "";
	$main->displayError = "hidden";
	$main->title = "BioSounds";
    
    if(empty($class)){		
		showHome($main);
	}    
    else {
		if($class == "project"){
			$project = new View();
			$main->content = "<div class='container main'>".$project->render('about.phtml')."</div>";
		} else {	
			try {	
				$classname = CONTROLLERS_NAMESPACE.$class."Controller";
				$file = CONTROLLERS_DIR.$class."controller.class.php";
				if(!file_exists($file)){
					showError("Invalid URL.", $main);
					showHome($main);
				} else {						
					$sectionController = new $classname;				
					if(!empty($action)){
						if(!empty($id)){
							if(!empty($param))
								$sectionController->$action($id, $param);
							else
								$sectionController->$action($id);
						}
						else
							$sectionController->$action();	
					}						
					$main->content = "<div class='container main'>".$sectionController->create()."</div>";
					if(method_exists($sectionController,'getTitle'))
						$main->title .= " - " . $sectionController->getTitle();
				}
			} catch(\Exception $e){
				showError($e->getMessage(), $main);
				showHome($main);
			}
		}
	}
	echo $main->render('index.phtml');
	
	function getHeader(){
		return file_get_contents("templates/header.phtml");
	}
	
	function getBottom(){
		try {
			$bottom = new BottomController;
			return $bottom->create();
		} catch(\Exception $e){
			echo 'Message: ' .$e->getMessage();
		}
	}
	
	function getTop(){
		try {
			$top = new TopController;
			return $top->create();
		} catch(\Exception $e){
			echo 'Message: ' .$e->getMessage();
		}
	}
	
	function showError($message, $main){
		$main->error = 'Error: ' .$message;
		$main->displayError = '';
	}
	
	function showHome($main){
		$content = new View;
		/*				<?php	
					// die MySQL-Daten entsprechend anpassen
					$db = @new MySQLi($host, $user, $password, $database);
					if (mysqli_connect_errno()) {
						die('Konnte keine Verbindung zu Datenbank aufbauen, MySQL meldete: '.mysqli_connect_error());
						// ist zwar keine saubere Fehlermeldung aber ist ja auch nur ne einfache Inplementierung
					}
					$sql = 'SELECT Titel, Datum, Inhalt	FROM News ORDER BY Datum DESC';
					// "ORDER BY" damit die Datensätze nach der Datumsspalte sortiert werden, absteigend

					$result = $db->query($sql);
					if (!$result) {
						die ('Konnte den Folgenden Query nicht senden: '.$sql."<br />\nFehlermeldung: ".$db->error);
					}
					if (!$result->num_rows) {
						echo '<p>Es sind keine Newsbeitr&auml;ge vorhanden</p>';
					} else {
						while ($row = $result->fetch_assoc()) {
							$datum=substr($row['Datum'], 0 , -3); //mit substr die Sekunden :ss entfern
							echo '<h4>'.$row['Titel'].'</h4>'.$datum.'';
							echo '<p class="small">'.$row['Inhalt'].'</p>';    
						}
					}
				?>
		*/	
		$content->projectName = Utils::getSetting(Settings::PROJECT_NAME);	
		$content->projectDescription = Utils::getSetting(Settings::PROJECT_DESCRIPTION);
		$main->content = $content->render("main.phtml");	
	}
?>
