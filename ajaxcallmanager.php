<?php

require_once __DIR__ . '/vendor/autoload.php';

$session = new \Hybridars\BioSounds\Security\Session();
try {
    $session->startSecureSession();
} catch(\Exception $e){
    header('Content-Type: application/json');
    $response_array['status'] = 'error';
    $response_array['message'] = $e->getMessage();
    echo json_encode($response_array);
}

$session->initGlobals();

/*
if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') <= 0)
  return false;*/

$class = isset($_GET['class']) ? $_GET['class'] : NULL;
$action = isset($_GET['action']) ? $_GET['action'] : NULL;
$id = isset($_GET['id']) ? $_GET['id'] : NULL;

try {
    $classname = 'Hybridars\\BioSounds\\Controller\\'.ucfirst($class)."Controller";
    $t = new $classname;
    if(!empty($action)){
        if(!empty($id))
            $result = $t->$action($id);
        else
            $result = $t->$action();
    }
    header('Content-Type: application/json');
    echo json_encode($result);
} catch(\Exception $e){
    header('Content-Type: application/json');
    $response_array['status'] = 'error';
    $response_array['message'] = $e->getMessage();
    echo json_encode($response_array);
}
