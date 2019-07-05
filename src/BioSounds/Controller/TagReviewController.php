<?php

namespace BioSounds\Controller;

use BioSounds\Entity\TagReview;
use BioSounds\Utils\Auth;

class TagReviewController extends BaseController
{
    /**
     * @param int $tagId
     * @return false|string
     * @throws \Exception
     */
	public function show(int $tagId)
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		}

		if (empty($tagId)) {
			throw new \Exception(ERROR_EMPTY_ID);
		}

		if(!Auth::isUserAdmin() &&
            (!isset($_SESSION['user_col_permission']) ||
                empty($_SESSION['user_col_permission']))
        ) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}

		$tagReview = new TagReview();

        return $this->twig->render('tag/tagReview.html.twig', [
            'disableReviewForm' => !Auth::isUserAdmin() && $tagReview->hasUserReviewed(Auth::getUserLoggedID(), $tagId),
            'reviews' => $tagReview->getListByTag($tagId),
            'tagId' => $tagId,
        ]);
	}

    /**
     * @return array|bool|int|null
     * @throws \Exception
     */
	public function save()
    {
        try {
            if (!Auth::isUserLogged()) {
                throw new \Exception(ERROR_NOT_LOGGED);
            }

            if(!Auth::isUserAdmin() &&
                (!isset($_SESSION['user_col_permission']) ||
                    empty($_SESSION['user_col_permission']))
            ) {
                throw new \Exception(ERROR_NOT_ALLOWED);
            }

            $data[TagReview::USER] = Auth::getUserLoggedID();

            foreach ($_POST as $key => $value) {
                $data[$key] = htmlentities(strip_tags(filter_var($value, FILTER_SANITIZE_STRING)), ENT_QUOTES);
            }

            if (empty($data[TagReview::SPECIES])) {
                unset($data[TagReview::SPECIES]);
            }

            (new TagReview())->insert($data);

            return json_encode([
                'errorCode' => 0,
                'message' => 'Tag review saved successfully.',
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
