<?php

require_once __DIR__ . '/vendor/autoload.php';

use Hybridars\BioSounds\Controller\View;
use Hybridars\BioSounds\Controller\FooterController;
use Hybridars\BioSounds\Controller\TopController;
use Hybridars\BioSounds\Entity\Setting;
use Hybridars\BioSounds\Utils\Utils;

$main = new View;
$session = new \Hybridars\BioSounds\Security\Session();

try {
    $session->startSecureSession();
} catch(\Exception $e){
    showError($e->getMessage(), $main);
    showHome($main);
}

$session->initGlobals();

$main->header = getHeader();
$main->bottom = getBottom();
$main->top = getTop();
$main->error = "";
$main->displayError = "hidden";
$main->title = "BioSounds";

Utils::deleteOldTmpFiles();

$class = isset($_GET['class']) ? $_GET['class'] : NULL;
$action = isset($_GET['action']) ? $_GET['action'] : NULL;
$id = isset($_GET['id']) ? $_GET['id'] : NULL;
$param = isset($_GET['param']) ? $_GET['param'] : NULL;
$type = isset($_GET['type']) ? $_GET['type'] : null;

if (empty($class)) {
    showHome($main);
}
else {
    if($class == "project"){
        $project = new View();
        $main->content = "<div class='container main'>".$project->render('about.phtml')."</div>";
    } else {
        try {
            $className = 'Hybridars\\BioSounds\\Controller\\'.ucfirst($class)."Controller";
            $sectionController = new $className;
            if(!empty($action)){
                if(!empty($id)){
                    if(!empty($param)) {
                        if(!empty($type))
                            $sectionController->$action($id, $param, $type);
                        else
                            $sectionController->$action($id, $param);
                    } else
                        $sectionController->$action($id);
                }
                else
                    $sectionController->$action();
            }
            $main->content = "<div class='container main'>".$sectionController->create()."</div>";
            if(method_exists($sectionController,'getTitle'))
                $main->title .= " - " . $sectionController->getTitle();
        } catch(\Exception $e){
            error_log($e);
            showError($e->getMessage(), $main);
            showHome($main);
        }
    }
}
echo $main->render('index.phtml');

function getHeader(){
    try {
        $headerView = new View();
        return $headerView->render('header.phtml');
    } catch(\Exception $e){
        echo 'Message: ' .$e->getMessage();
    }
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
    $content->projectName = Utils::getSetting(Setting::PROJECT_NAME);
    $content->projectDescription = Utils::getSetting(Setting::PROJECT_DESCRIPTION);
    $main->content = $content->render("main.phtml");
}
