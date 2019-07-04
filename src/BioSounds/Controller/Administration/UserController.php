<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Entity\User;
use BioSounds\Entity\Role;
use BioSounds\Utils\Auth;
use BioSounds\Utils\Utils;

class UserController extends BaseController
{
    const SECTION_TITLE = 'Users';
	const DEFAULT_TAG_COLOR = '#FFFFFF';

    /**
     * @return false|string
     * @throws \Exception
     */
    public function create()
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
       // $this->getUsersList();
        return $this->twig->render('administration/users.html.twig', [
            'roles' => 	(new Role())->getRoles(),
            'users' => (new User())->getAllUsers(),
            'default_color' => self::DEFAULT_TAG_COLOR,
        ]);
    }

    /**
     * @return false|string
     */
    public function save()
    {
        try {
            $userProvider = new User();

            if (!Auth::isUserAdmin()) {
                throw new \Exception(ERROR_NO_ADMIN);
            }

            if (isset($_POST['admin_pwd'])) {
                $adminPwd = filter_var($_POST['admin_pwd'], FILTER_SANITIZE_STRING);
                $bdAdminPwd = $userProvider->getPasswordByUserId(Auth::getUserLoggedID());
                if (!Utils::checkPasswords($adminPwd, $bdAdminPwd)) {
                    throw new \Exception('The administrator password is not correct.', 1);
                }
                unset($_POST['admin_pwd']);
            }

            $data = [];

            foreach ($_POST as $key => $value) {
                if (strrpos($key, '_')) {
                    $type = substr($key, strrpos($key, '_') + 1, strlen($key));
                    $key = substr($key, 0, strrpos($key, '_'));

                    switch ($type) {
                        case 'email':
                            $data[$key] =  filter_var($value, FILTER_SANITIZE_EMAIL);
                            break;
                        case 'checkbox':
                            $data[$key] =  filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                            break;
                        case 'select-one':
                            $data[$key] =  filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                            break;
                        case 'password':
                            $password = filter_var($value, FILTER_SANITIZE_STRING);
                            $data[$key] = Utils::encodePasswordHash($password);
                            break;
                        default:
                            $data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                            break;
                    }
                } else {
                    $data[$key] =  filter_var($value, FILTER_SANITIZE_STRING);
                }
            }

            if (isset($data['itemID'])) {
                $userProvider->updateUser($data);
                return json_encode([
                    'errorCode' => 0,
                    'message' => 'User updated successfully.'
                ]);
            }
            else if($userProvider->insertUser($data) > 0) {
                return json_encode([
                    'errorCode' => 0,
                    'message' => 'User created successfully.',
                ]);
            }
        } catch(\Exception $exception) {
            error_log($exception->getMessage());
            http_response_code(400);

            return json_encode([
                'errorCode' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }
	}

    /**
     * @return false|string
     */
	public function editPassword()
    {
        try {
            if (!Auth::isUserAdmin()) {
                throw new \Exception(ERROR_NO_ADMIN);
            }

            $userId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            return json_encode([
                'errorCode' => 0,
                'data' => $this->twig->render('administration/userPassword.html.twig', [
                    'userId' => $userId,
                ]),
            ]);
        } catch(\Exception $exception) {
            error_log($exception->getMessage());
            http_response_code(400);

            return json_encode([
                'errorCode' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }
	}
}
