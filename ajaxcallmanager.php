<?php
    require "vendor/autoload.php";

    include_once("src/Hybridars/init.php");
    
    secure_session_start();
  /*  
	if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') <= 0)  
	  return false;*/
    
    $class = isset($_GET['class']) ? $_GET['class'] : NULL;
    $action = isset($_GET['action']) ? $_GET['action'] : NULL;
    $id = isset($_GET['id']) ? $_GET['id'] : NULL;

	try {	
		$classname = 'Hybridars\\Controller\\'.ucfirst($class)."Controller";
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
