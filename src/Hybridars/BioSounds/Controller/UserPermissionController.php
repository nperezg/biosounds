<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\UserPermission;
use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Entity\Permission;
use Hybridars\BioSounds\Entity\Collection;
use Hybridars\BioSounds\Utils\Auth;

class UserPermissionController
{
    protected $template = 'userPermission.phtml';
    protected $view;

    /**
     * UserPermissionController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
		if (!Auth::isUserAdmin()) {
			throw new \Exception('User has no access to the administration.');
		}		
		$this->view = new View(); 
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function create()
    {
		if (!Auth::isUserAdmin()) {
			throw new \Exception('User has no access to the administration.');
		}
		$this->view->userLabel = UserPermission::USER;
		$this->view->colLabel = UserPermission::COLLECTION;
		$this->view->permLabel = UserPermission::PERMISSION;
        return $this->view->render($this->template);
    }

    /**
     * @param int $userId
     * @return false|string
     * @throws \Exception
     */
	public function manage(int $userId)
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception('User has no access to the administration.');
		}
		$user = new User();		
		$this->view->username = $user->getUserName($userId);
		$this->view->userID = $userId;
		
		$userPermission = new UserPermission();
		$listCollections = $userPermission->getColPermissionsByUser($userId);
		$permission = new Permission();
		$viewID = $permission->getViewId();
		$reviewID = $permission->getReviewId();

		foreach ($listCollections as $colData) {
			$colID = $colData[Collection::PRIMARY_KEY];
			$colName = $colData[Collection::NAME];
			$colPerm = $colData[UserPermission::PERMISSION];
			
			$this->view->collectionsList .= "<tr><td>$colID<input type='hidden' name='isnew' value='" . ($colPerm == null ?  '1' : '0') . "'></td>";
			$this->view->collectionsList .= "<td>$colName</td>";
			$this->view->collectionsList .= "<td><input id='" . $colID . "_mandatory' value='$viewID' type='checkbox' " . ($colPerm == $viewID ?  "checked" : "") . "></td>";
			$this->view->collectionsList .= "<td><input id='" . $colID . "_secondary' class='master-check' value='$reviewID' type='checkbox' " . ($colPerm == $reviewID ?  "checked" : "") . "></td>";
			$this->view->collectionsList .= "</tr>";
		}
		return $this->create();
	}

    /**
     * @return bool
     * @throws \Exception
     */
	public function save(): bool
    {
		if (!Auth::isUserAdmin()) {
			throw new \Exception('User has no access to the administration.');
		}
		
		$data = json_decode($_POST['rows']);
		$userPermission = new UserPermission();
		
		$permission = new Permission();
		$viewID = $permission->getViewId();
		$reviewID = $permission->getReviewId();
				
	    foreach ($data as $row) {
			$colData = filter_var_array((array) $row);
			
			$new = $colData["isNew"];
			unset($colData["isNew"]);
			
			$permission = $colData[UserPermission::PERMISSION];	

			if ($permission != $viewID && $permission != $reviewID) {
				if (!$new) {
				    $userPermission->deleteUserPermission($colData[UserPermission::USER], $colData[UserPermission::COLLECTION]);
                }
				continue;
			}

			if ($new) {
                $userPermission->insertUserPermission($colData);
            } else {
                $userPermission->updateUserPermission($colData);
            }
		}
		return true;
	}	
}
