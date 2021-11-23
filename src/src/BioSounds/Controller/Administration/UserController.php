<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Entity\User;
use BioSounds\Entity\Role;
use BioSounds\Exception\ForbiddenException;
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
        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }
        // $this->getUsersList();
        return $this->twig->render('administration/users.html.twig', [
            'roles' => (new Role())->getRoles(),
            'users' => (new User())->getAllUsers(),
            'default_color' => self::DEFAULT_TAG_COLOR,
        ]);
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function save()
    {
        $userProvider = new User();

        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
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
        } else if ($userProvider->insertUser($data) > 0) {
            return json_encode([
                'errorCode' => 0,
                'message' => 'User created successfully.',
            ]);
        }
    }


    /**
     * @return false|string
     * @throws \Exception
     */
    public function resetSave()
    {
        $userProvider = new User();

        if (isset($_POST['my_pwd'])) {
            $myPwd = filter_var($_POST['my_pwd'], FILTER_SANITIZE_STRING);
            $bdMyPwd = $userProvider->getPasswordByUserId(Auth::getUserLoggedID());
            if (!Utils::checkPasswords($myPwd, $bdMyPwd)) {
                throw new \Exception('The old password is not correct.', 1);
            }
            unset($_POST['my_pwd']);
        }

        $data = [];

        foreach ($_POST as $key => $value) {
            if (strrpos($key, '_')) {
                $type = substr($key, strrpos($key, '_') + 1, strlen($key));
                $key = substr($key, 0, strrpos($key, '_'));

                switch ($type) {
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
            $userProvider->resetPasswd($data['itemID'], $data['password']);
            return json_encode([
                'errorCode' => 0,
                'message' => 'Password updated successfully.'
            ]);
        }
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function editPassword()
    {
        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }

        $userId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        return json_encode([
            'errorCode' => 0,
            'data' => $this->twig->render('administration/userPassword.html.twig', [
                'userId' => $userId,
            ]),
        ]);
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function passwordSelfService(int $id = null)
    {
        return $this->twig->render('administration/passwordSelfService.html.twig', [
            'user' => (new User())->getMyProfile($id),
            'role' => (new Role())->getMyRole($id),
        ]);
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function passwordReset()
    {
        $userId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        return json_encode([
            'errorCode' => 0,
            'data' => $this->twig->render('administration/resetPassword.html.twig', [
                'userId' => $userId,
            ]),
        ]);
    }
}
