<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Entity\Role;
use Hybridars\BioSounds\Utils\Auth;
use Hybridars\BioSounds\Utils\Utils;

class UserController
{
	const DEFAULT_TAG_COLOR = "#FFFFFF";

    protected $listTpl = 'usersList.phtml';
    protected $formTpl = 'userForm.phtml';
    protected $pwdTpl = 'userPassword.phtml';
    protected $view;
    protected $user;

    public function __construct() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view = new View(); 
		$this->user = new User();
    }
    
    public function create() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->getContent();
        return $this->view->render($this->listTpl);
    }

    public function save(){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		if(isset($_POST["admin_pwd"])){
			$adminPwd = filter_var($_POST["admin_pwd"], FILTER_SANITIZE_STRING); 
			$bdAdminPwd = $this->user->getPasswordByUserID(Auth::getUserLoggedID());
			if(Utils::checkPasswords($adminPwd, $bdAdminPwd))
				unset($_POST["admin_pwd"]);
			else
				throw new \Exception("The administrator password is not correct."); 	
		}
		
		$data = array();

		foreach($_POST as $key => $value){
			if(strpos($key, "_")){
				$type = substr($key, strpos($key, "_") + 1, strlen($key));
				$key = substr($key, 0, strpos($key, "_"));
				
				switch($type){
					case "email":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_EMAIL); 
						break;
					case "checkbox":
						$data[$key] =  filter_var($value, FILTER_VALIDATE_BOOLEAN); 
						break;
					case "select-one":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_NUMBER_INT); 
						break;
					case "password":
						$password = filter_var($value, FILTER_SANITIZE_STRING);
						$data[$key] = Utils::encodePasswordHash($password);
						break;
                    default:
                        $data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                }
			} else
				$data[$key] =  filter_var($value, FILTER_SANITIZE_STRING); 
		}
		if(isset($data["itemID"]))
			return $this->user->updateUser($data);
		else {
			if($this->user->insertUser($data) > 0)
				header("Location: " . APP_URL . "/admin/users");
				die();
		}
	}
	
	public function show($userID){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view->userPasswordLabel = User::PASSWORD;
		$this->view->userID = $userID;
		return $this->view->render($this->pwdTpl);
	}
    
    private function getContent(){
		if (Auth::isUserAdmin()){
			$this->getUsersList();
			$this->view->usernameLabel = User::NAME . "_text";
			$this->view->userFullNameLabel = User::FULL_NAME . "_text";
			$this->view->userEmailLabel = User::EMAIL . "_email";
			$this->view->userRoleLabel = User::ROLE . "_select-one";
			$this->view->userPwdLabel = User::PASSWORD . "_password";
		}
	}
	
	private function getUsersList(){	
		$currentUserID = Auth::getUserLoggedID();
		$listUsers = $this->user->getAllUsers();
		$this->view->numUsers = count($listUsers);
		
		$rol = new Role();			
		$roles = $rol->getRoles();
		
		//New User Form
		$this->view->rolesList = "";
		
		foreach($roles as $values){				
			$this->view->rolesList .= "<option value='" . $values["ID"] . "'>" . $values["Name"] . "</option>";
		}
		//
		
		$this->view->listUsers = "";
		
		foreach($listUsers as $userData) {
			$disabled = "";
			$privHidden = "";
			$userName = $userData[User::NAME];
			$userFullName = $userData[User::FULL_NAME];
			$userID = $userData[User::ID];
			$userEmail = $userData[User::EMAIL];
			$userRole = $userData[User::ROLE];
			$userActive = $userData[User::ACTIVE];
            $userTagColor = empty($userData[User::TAG_COLOR]) ? self::DEFAULT_TAG_COLOR : $userData[User::TAG_COLOR];
			
			$this->view->listUsers .= "<tr><td><input type='text' name='" . User::FULL_NAME . "' value='$userFullName'><input type='hidden' name='itemID' value='$userID'></td><td>$userName</td>";
			$this->view->listUsers .= "<td><input type='email' name='" . User::EMAIL . "' value='$userEmail'></td>";
			
			$this->view->listUsers .= "<td>";	
			$isAdmin = $rol->isRoleAdmin($userRole);		

			if ($currentUserID == $userID || ($isAdmin && ($this->user->countOtherAdminUsers($userID) == 0)))
				$disabled = "disabled";
				
			$this->view->listUsers .= "<select $disabled name='" . User::ROLE . "'>";
			foreach($roles as $values){				
				$this->view->listUsers .= "<option value='" . $values["ID"] . "' " . ($userRole == $values["ID"] ? "selected" : "") . ">" . $values["Name"] . "</option>";
			}

			$this->view->listUsers .= "</select></td>";
			$this->view->listUsers .= "<td><input name='" . User::ACTIVE . "' type='checkbox' " . ($userActive ?  "checked" : "") . " $disabled></td>";
			
			if($isAdmin)
				$privHidden = "hidden";

			$this->view->listUsers .= "<td><a href='ajaxcallmanager.php?class=user&action=show&id=$userID' class='open-modal' title='Edit Password'><span class='glyphicon glyphicon-pencil'></span></a></td>";
			
			$this->view->listUsers .= "<td><a href='ajaxcallmanager.php?class=UserPermission&action=manage&id=$userID' class='open-modal' title='Collection Privileges' $privHidden><span class='glyphicon glyphicon-tasks'></span></a></td>";

            $this->view->listUsers .= '<td><input type="color" name="TagColor" alt= "User tags color" value=' . $userTagColor . '></td>';;
						
			$this->view->listUsers .= "</tr>";
		}
	}
}
