<?php

namespace classes\controllers;

use \classes\controllers\View;
use \classes\models\User;
use \classes\models\TagReview;
use \classes\models\Animal;
use \classes\utils\Auth;

class TagReviewController {

    protected $template = 'tagreview.phtml';
    protected $view;
    
    public function __construct() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(!Auth::isUserAdmin() && (!isset($_SESSION["user_col_permission"]) || empty($_SESSION["user_col_permission"]))){
			throw new \Exception(ERROR_NOT_ALLOWED);
		}	
		$this->view = new View(); 
		$this->view->disableReviewForm = "false";
		$this->view->reviewsList = "";
    }
    
    private function create() {
        return $this->view->render($this->template);
    }
    
	public function show($tagID){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($tagID)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		if(!Auth::isUserAdmin() && (!isset($_SESSION["user_col_permission"]) || empty($_SESSION["user_col_permission"]))) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}
		
		$tagReview = new TagReview();
		if(!Auth::isUserAdmin() && $tagReview->hasUserReviewed(Auth::getUserLoggedID(), $tagID))
			$this->view->disableReviewForm = "true";
			date_default_timezone_set('UTC');
		$reviews = $tagReview->getTagReviews($tagID);	
		if(!empty($reviews)){
			foreach($reviews as $value){
				$date = strtotime($value[TagReview::DATE]);
				$this->view->reviewsList .= "<tr><td>" . $value[User::FULL_NAME] . "</td><td>" . $value[TagReview::STATUS_NAME] . "</td>";
				$this->view->reviewsList .= "<td>" . $value[Animal::BINOMIAL] . "</td><td>" . date("d/m/Y", $date) . "</td></tr>";
			}			
		}	
				
		$this->view->animalFN = TagReview::ANIMAL;
		$this->view->soundTagFN = TagReview::SOUND_TAG;
		$this->view->statusFN = TagReview::STATUS;
		$this->view->commentsFN = TagReview::COMMENTS;
		$this->view->soundTagID = $tagID;
		
		return $this->create();
	}
	
	public function save(){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(!Auth::isUserAdmin() && (!isset($_SESSION["user_col_permission"]) || empty($_SESSION["user_col_permission"]))) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}
		
		$data = array();
		$data[TagReview::USER] = Auth::getUserLoggedID();

	    foreach($_POST as $key => $value){
			$data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
		}
		
		if(empty($data[TagReview::ANIMAL]))
			unset($data[TagReview::ANIMAL]);

		$tagReview = new TagReview();
		return $tagReview->insertTagReview($data);
	}	
}
?>	
