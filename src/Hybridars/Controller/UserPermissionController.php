<?php

namespace Hybridars\Controller;

use Hybridars\Models\UserPermission;
use Hybridars\Entity\User;
use Hybridars\Entity\Permission;
use Hybridars\Entity\Collection;
use Hybridars\Utils\Auth;

class UserPermissionController
{
    protected $template = 'userPermission.phtml';
    protected $view;

    private $userID;
    
    public function __construct() {
		if (!Auth::isUserAdmin()){
			throw new \Exception("User has no access to the administration."); 
		}		
		$this->view = new View(); 
    }
    
    public function create() {
		if (!Auth::isUserAdmin()){
			throw new \Exception("User has no access to the administration."); 
		}
		$this->view->userLabel = UserPermission::USER;
		$this->view->colLabel = UserPermission::COLLECTION;
		$this->view->permLabel = UserPermission::PERMISSION;
        return $this->view->render($this->template);
    }
    
	public function manage($userID){
		if (!Auth::isUserAdmin()){
			throw new \Exception("User has no access to the administration."); 
		}
		$user = new User();		
		$this->view->username = $user->getUserName($userID);
		$this->view->userID = $userID;
		
		$userPermission = new UserPermission();
		$listCollections = $userPermission->getColPermissionsByUser($userID);
		$permission = new Permission();
		$viewID = $permission->getViewID();
		$reviewID = $permission->getReviewID();
		foreach($listCollections as $colData) {
			$disabled = "";
			$colID = $colData[Collection::PRIMARY_KEY];
			$colName = $colData[Collection::NAME];
			$colPerm = $colData[UserPermission::PERMISSION];
			
			$this->view->collectionsList .= "<tr><td>$colID<input type='hidden' name='isnew' value='" . ($colPerm == NULL ?  "1" : "0") . "'></td>";
			$this->view->collectionsList .= "<td>$colName</td>";
			$this->view->collectionsList .= "<td><input id='" . $colID . "_mandatory' value='$viewID' type='checkbox' " . ($colPerm == $viewID ?  "checked" : "") . "></td>";
			$this->view->collectionsList .= "<td><input id='" . $colID . "_secondary' class='master-check' value='$reviewID' type='checkbox' " . ($colPerm == $reviewID ?  "checked" : "") . "></td>";
			$this->view->collectionsList .= "</tr>";
		}
		return $this->create();
	}
	
	public function save(){
		if (!Auth::isUserAdmin()){
			throw new \Exception("User has no access to the administration."); 
		}
		
		$data = json_decode($_POST['rows']);
		$userPermission = new UserPermission();
		
		$permission = new Permission();
		$viewID = $permission->getViewID();
		$reviewID = $permission->getReviewID();
				
	    foreach($data as $row){
			$colData = filter_var_array((array) $row);
			
			$new = $colData["isNew"];
			unset($colData["isNew"]);
			
			$permission = $colData[UserPermission::PERMISSION];	

			if($permission != $viewID && $permission != $reviewID) {
				if(!$new)
					$userPermission->deleteUserPermission($colData[UserPermission::USER], $colData[UserPermission::COLLECTION]);
				continue;
			}

			if($new)
				$userPermission->insertUserPermission($colData);
			else
				$userPermission->updateUserPermission($colData);
		}
		return true;
	}	
}
