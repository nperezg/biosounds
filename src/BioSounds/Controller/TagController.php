<?php

namespace BioSounds\Controller;

use BioSounds\Entity\Tag;
use BioSounds\Entity\Permission;
use BioSounds\Provider\TagProvider;
use BioSounds\Utils\Auth;

class TagController extends BaseController
{
    /**
     * @param int $tagId
     * @return string
     * @throws \Exception
     */
    public function showCallDistance(int $tagId)
    {
	    return json_encode([
	        'errorCode' => 0,
            'data' => $this->twig->render('callEstimation.html.twig', [
                'tagId' => $tagId,
            ]),
        ]);
	}

    /**
     * @return false|string
     */
	public function create()
    {
        try {
            if (!Auth::isUserLogged()) {
                throw new \Exception(ERROR_NOT_LOGGED);
            }

            if (!isset($_POST["t_min"]) || !isset($_POST["t_max"]) || !isset($_POST["f_min"]) || !isset($_POST["f_max"])) {
                throw new \Exception('Data not set.');
            }

            $tag = (new Tag())
                ->setRecording(filter_var($_POST["recording_id"], FILTER_SANITIZE_NUMBER_INT))
                ->setMinTime(filter_var($_POST["t_min"], FILTER_SANITIZE_NUMBER_FLOAT))
                ->setMaxTime(filter_var($_POST["t_max"], FILTER_SANITIZE_NUMBER_FLOAT))
                ->setMinFrequency(filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT))
                ->setMaxFrequency(filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT))
                ->setUserName(Auth::getUserName())
                ->setUser(Auth::getUserLoggedID());

            return json_encode([
                'errorCode' => 0,
                'data' => $this->twig->render('tag.html.twig', [
                    'tag' => $tag,
                    'recordingName' => isset($_POST['recording_name']) ? $_POST['recording_name'] : null,
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

    /**
     * @param int $tagId
     * @return false|string
     */
	public function edit(int $tagId)
    {
        try {
            if (!Auth::isUserLogged()) {
                throw new \Exception(ERROR_NOT_LOGGED);
            }

            if (empty($tagId)) {
                throw new \Exception(ERROR_EMPTY_ID);
            }

            if (!Auth::isUserAdmin()
                && (!isset($_SESSION["user_col_permission"])
                    || empty($_SESSION["user_col_permission"]))
            ){
                throw new \Exception(ERROR_NOT_ALLOWED);
            }

            $tag = (new TagProvider())->get($tagId);

            // USERS CONTROL
            $hasReviewPerm = false;
            $displaySaveBtn = '';
            if (Auth::isUserAdmin() || ($tag[Tag::USER_ID] != Auth::getUserLoggedID())) {
                $permission = new Permission();
                $hasReviewPerm = Auth::isUserAdmin() ?: $permission->isReviewPermission($_SESSION["user_col_permission"]);
                $hasViewPerm = Auth::isUserAdmin() ?: $permission->isViewPermission($_SESSION["user_col_permission"]);
                if (!$hasReviewPerm && !$hasViewPerm) {
                    throw new \Exception(ERROR_NOT_ALLOWED);
                }

                $displaySaveBtn = 'hidden';
                if (Auth::isUserAdmin() || $hasReviewPerm) {
                    $displaySaveBtn = '';
                }
            }

            $displayDeleteBtn = '';
            if (!Auth::isUserAdmin() && $tag[Tag::USER_ID] != Auth::getUserLoggedID()) {
                $displayDeleteBtn = 'hidden';
            }
            //

            return json_encode([
                'errorCode' => 0,
                'data' => $this->twig->render('tag.html.twig', [
                    'tag' => $tag,
                    'recordingName' => isset($_POST['recording_name']) ? $_POST['recording_name'] : null,
                    'displayDeleteButton' => $displayDeleteBtn,
                    'displaySaveButton' => $displaySaveBtn,
                    'disableTagForm' => Auth::isUserAdmin() ? '' : 'true',
                    'reviewPanel' => $hasReviewPerm ? (new TagReviewController($this->twig))->show($tagId) : '',
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

    /**
     * @return false|string
     */
	public function save()
    {
        try {
            if (!Auth::isUserLogged()) {
                throw new \Exception(ERROR_NOT_LOGGED);
            }

            $data[Tag::UNCERTAIN] = 0;
            $data[Tag::REFERENCE_CALL] = 0;
            $data[Tag::DISTANCE_NOT_ESTIMABLE] = 0;

            foreach($_POST as $key => $value) {
//	    if ($value == "on") {
//            $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
//        } else {
                $data[$key] = htmlentities(strip_tags($value), ENT_QUOTES);
//        }

                if ($key === Tag::CALL_DISTANCE && empty($value)) {
                    $data[$key] = null;
                }
            }

            if (isset($data[Tag::ID]) && !empty($data[Tag::ID])) {
                $tagId = (new TagProvider())->update($data);
                return json_encode([
                    'errorCode' => 0,
                    'message' => 'Tag updated successfully.',
                ]);
            }

            $data[Tag::USER_ID] = Auth::getUserLoggedID();
            if ($data[Tag::DISTANCE_NOT_ESTIMABLE] != 1) {
                $data[Tag::DISTANCE_NOT_ESTIMABLE] = null;
            }

            unset($data[Tag::ID]);

            return json_encode([
                'errorCode' => 0,
                'message' => 'Tag created successfully.',
                'tagId' => (new TagProvider())->insert($data),
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

    /**
     * @param int $tagId
     * @return array|int
     * @throws \Exception
     */
	public function delete(int $tagId)
    {
        if (!Auth::isUserAdmin() && (new TagProvider())->get($tagId)->getUser() != Auth::getUserLoggedID()) {
            throw new \Exception('The user doesn\'t have permissions to delete this tag.');
        }
		return (new TagProvider())->delete($tagId);
	}
}
