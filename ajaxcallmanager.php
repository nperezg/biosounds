<?php    
    spl_autoload_extensions(".php,.class.php,.inc");
    spl_autoload_register();
    
    define("CONTROLLERS_NAMESPACE", "classes\\controllers\\");
    define("CONTROLLERS_DIR", "classes/controllers/");
    define("TEMPLATES_DIR", "templates\\");
    define("SERVICES_DIR", "classes\\services");
    
    include_once("classes/init.inc");
    
    secure_session_start();
  /*  
	if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') <= 0)  
	  return false;*/
    
    $class = isset($_GET['class']) ? $_GET['class'] : NULL;
    $action = isset($_GET['action']) ? $_GET['action'] : NULL;
    $id = isset($_GET['id']) ? $_GET['id'] : NULL;

	try {	
		$classname = CONTROLLERS_NAMESPACE.$class."Controller";
		$file = CONTROLLERS_DIR.$class."controller.class.php";
		if(!file_exists($file)){
			header('Content-Type: application/json');
			$response_array['status'] = 'error';
		    $response_array['message'] = "ERROR: Invalid URL!";
			echo json_encode($response_array);
		} else {						
			$t = new $classname;				
			if(!empty($action)){
				if(!empty($id))
					$result = $t->$action($id);	
				else
					$result = $t->$action();
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		}
	} catch(\Exception $e){
		header('Content-Type: application/json');
		$response_array['status'] = 'error';
		$response_array['message'] = $e->getMessage();
	    echo json_encode($response_array);
	}
?>
