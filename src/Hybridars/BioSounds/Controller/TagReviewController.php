<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Entity\TagReview;
use Hybridars\BioSounds\Entity\Species;
use Hybridars\BioSounds\Utils\Auth;

class TagReviewController
{
    protected $template = 'tagReview.phtml';
    protected $view;

    /**
     * TagReviewController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(
		    !Auth::isUserAdmin() &&
            (!isset($_SESSION['user_col_permission']) || empty($_SESSION['user_col_permission']))
        ) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}	
		$this->view = new View(); 
		$this->view->disableReviewForm = 'false';
		$this->view->reviewsList = '';
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    private function create()
    {
        return $this->view->render($this->template);
    }

    /**
     * @param int $tagId
     * @return false|string
     * @throws \Exception
     */
	public function show(int $tagId)
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if (empty($tagId)) {
			throw new \Exception(ERROR_EMPTY_ID);
		}
		if(!Auth::isUserAdmin() &&
            (!isset($_SESSION['user_col_permission']) ||
                empty($_SESSION['user_col_permission']))
        ) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}
		
		$tagReview = new TagReview();
		if (!Auth::isUserAdmin() && $tagReview->hasUserReviewed(Auth::getUserLoggedID(), $tagId)) {
            $this->view->disableReviewForm = 'true';
        }

		date_default_timezone_set('UTC');

		if (!empty($reviews = $tagReview->getListByTag($tagId))) {
			foreach ($reviews as $value) {
				$date = strtotime($value[TagReview::DATE]);
				$this->view->reviewsList .= '<tr><td>' . $value['reviewer'] . '</td><td>' .
                    $value['status'] . '</td>';
				$this->view->reviewsList .= '<td>' . $value[Species::BINOMIAL] . '</td><td>' .
                    date('d/m/Y', $date) . '</td></tr>';
			}			
		}	
				
		$this->view->animalFN = TagReview::SPECIES;
		$this->view->soundTagFN = TagReview::TAG;
		$this->view->statusFN = TagReview::STATUS;
		$this->view->commentsFN = TagReview::COMMENTS;
		$this->view->soundTagID = $tagId;
		
		return $this->create();
	}

    /**
     * @return array|bool|int|null
     * @throws \Exception
     */
	public function save()
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(!Auth::isUserAdmin() &&
            (!isset($_SESSION['user_col_permission']) ||
                empty($_SESSION['user_col_permission']))
        ) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}

		if (empty($_POST['tag_review_status_id'])) {
            return null;
        }

		$data[TagReview::USER] = Auth::getUserLoggedID();

	    foreach ($_POST as $key => $value) {
			$data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
		}
		
		if (empty($data[TagReview::SPECIES])) {
            unset($data[TagReview::SPECIES]);
        }

		$tagReview = new TagReview();
		return $tagReview->insert($data);
	}	
}
