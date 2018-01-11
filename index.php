<?php
    require "vendor/autoload.php";

    use Hybridars\Controller\View;
    use Hybridars\Controller\FooterController;
    use Hybridars\Controller\TopController;
    use Hybridars\Entity\Settings;
    use Hybridars\Utils\Utils;

	include_once("src/Hybridars/init.php");

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
				$className = 'Hybridars\\Controller\\'.ucfirst($class)."Controller";
                $sectionController = new $className;
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

	function getBottom()
	{
		try {
			$bottom = new FooterController();
			return $bottom->create();
		} catch(\Exception $e){
			echo 'Message: ' .$e->getMessage();
		}
	}

	function getTop()
    {
		try {
			$top = new TopController;
			return $top->create();
		} catch(\Exception $e){
			echo 'Message: ' .$e->getMessage();
		}
	}

	function showError($message, $main)
    {
		$main->error = 'Error: ' .$message;
		$main->displayError = '';
	}

	function showHome($main)
    {
		$content = new View;
		$content->projectName = Utils::getSetting(Settings::PROJECT_NAME);
		$content->projectDescription = Utils::getSetting(Settings::PROJECT_DESCRIPTION);
		$main->content = $content->render("main.phtml");
	}
