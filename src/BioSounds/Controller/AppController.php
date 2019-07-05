<?php

namespace BioSounds\Controller;

use BioSounds\Classes\BaseClass;
use BioSounds\Utils\Utils;
use BioSounds\Security\Session as Session;

class AppController extends BaseClass
{
    private $error = '';
    private $title = 'BioSounds';

    /**
     * AppController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initApp();
        parent::__construct();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function start()
    {
        try {
            $class = isset($_GET['class']) ? $_GET['class'] : null;
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            $param = isset($_GET['param']) ? $_GET['param'] : null;
            $type = isset($_GET['type']) ? $_GET['type'] : null;

            if (!empty($class)) {
                $className = 'BioSounds\\Controller\\'.ucfirst($class) . 'Controller';
                $sectionController = new $className($this->twig);

                if (!empty($action)) {
                    if (!empty($id)) {
                        if (!empty($param)) {
                            if(!empty($type)) {
                                return $sectionController->$action($id, $param, $type);
                            } else {
                                return $sectionController->$action($id, $param);
                            }
                        } else {
                            return $sectionController->$action($id);
                        }
                    } else {
                        return $sectionController->$action();
                    }
                }

//                if (!empty($action)) {
//                    return $sectionController->$action($id, $param, $type);
//                }

                return $sectionController->create();
            }

            return $this->twig->render('index.html.twig', [
                'title' => $this->title,
            ]);

        } catch(\Exception $e){
            error_log($e);

            return $this->twig->render('index.html.twig', [
                'title' => $this->title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function initApp()
    {
        try {
           (new Session())->startSecureSession();

            Utils::deleteOldTmpFiles();
        } catch(\Exception $e) {
            $this->error = $e->getMessage();
        }
    }
}
