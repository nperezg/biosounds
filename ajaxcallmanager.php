<?php

require_once __DIR__ . '/vendor/autoload.php';

$session = new \Hybridars\BioSounds\Security\Session();

try {
    $session->startSecureSession();

    $session->initGlobals();

    $class = isset($_GET['class']) ? $_GET['class'] : NULL;
    $action = isset($_GET['action']) ? $_GET['action'] : NULL;
    $id = isset($_GET['id']) ? $_GET['id'] : NULL;

    header('Content-Type: application/json');

    $classname = 'Hybridars\\BioSounds\\Controller\\'.ucfirst($class)."Controller";
    $t = new $classname;
    if (!empty($action)) {
        if(!empty($id))
            $result = $t->$action($id);
        else
            $result = $t->$action();
    }
    echo json_encode($result);
} catch(\Exception $e){
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
