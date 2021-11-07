<?php

namespace BioSounds\Controller;

use BioSounds\Entity\Collection;
use BioSounds\Entity\Recording;
use BioSounds\Entity\Site;
use BioSounds\Entity\Sound;
use BioSounds\Exception\NotAuthenticatedException;
use BioSounds\Provider\CollectionProvider;
use BioSounds\Provider\RecordingProvider;
use BioSounds\Service\RecordingService;
use BioSounds\Utils\Auth;

class CollectionController extends BaseController
{
    const GALLERY_TEMPLATE = 'collection/views/gallery.html.twig';
    const LIST_TEMPLATE = 'collection/views/list.html.twig';
    const ITEMS_PAGE = 9;
    const SECTION_TITLE = 'Collections';

    private $page;
    private $colId;
    private $recordingNum;
    private $pageNum;
    private $openCollection = false;
    private $collection;
    private $recordings = [];
    private $filter = [];

    protected $view;

    /**
     * @param int $id
     * @param int $page
     * @param string|null $view
     * @return string
     * @throws \Exception
     */
    public function show(int $id, int $page = 1, string $view = null)
    {
        $this->colId = $id;

        //TODO: Set open collections in administration (not hard-coded)
        if ($id == 1 || $id == 3 || $id == 18 || $id == 31 || $id == 19 || $id == 39) {
            $this->openCollection = true;
        }

        $this->checkPermissions();

        $this->isAccessible();

        $this->page = $page;

        $this->collection = (new CollectionProvider())->get($this->colId);

        $display = $view == null ? $this->collection->getView() : $view;

        if (isset($_POST['species']) && !empty($_POST['species'])) {
            $this->filter[Sound::SPECIES_ID] = filter_var($_POST['species'], FILTER_VALIDATE_INT);
        }

        if (isset($_POST['rating']) && !empty($_POST['rating'])) {
            $this->filter[Sound::RATING] = filter_var($_POST['rating'], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST['site']) && !empty($_POST['site'])) {
            $this->filter[Site::PRIMARY_KEY] = filter_var($_POST['site'], FILTER_VALIDATE_INT);
        }

        if (isset($_POST['doi']) && !empty($_POST['doi'])) {
            $this->filter[Recording::DOI] = filter_var($_POST['doi'], FILTER_SANITIZE_STRING);
        }



        $this->recordingNum = (new RecordingProvider())->countReady($this->colId, $this->filter);
        if ($this->recordingNum > 0) {
            $this->pageNum = ceil($this->recordingNum / self::ITEMS_PAGE);

            if ($this->page > $this->pageNum) {
                $this->page = 1;
            }
        }

        $this->recordings = (new RecordingService())->getListWithImages(
            $this->colId,
            self::ITEMS_PAGE,
            self::ITEMS_PAGE * ($this->page - 1),
            $this->filter
        );

        if (isset($_POST['site-name'])) {
            $this->filter['siteName'] = filter_var($_POST['site-name'], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST['species-name'])) {
            $this->filter['speciesName'] = filter_var($_POST['species-name'], FILTER_SANITIZE_STRING);
        }

        return $this->twig->render('collection/collection.html.twig', [
            'collection' => $this->collection,
            'pageNum' => $this->pageNum,
            'currentPage' => $this->page,
            'recordingNum' => $this->recordingNum,
            'list' => $this->recordings,
            'template' => $display == Collection::LIST_VIEW ? self::LIST_TEMPLATE : self::GALLERY_TEMPLATE,
            'display' => $display,
            'filter' => $this->filter,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function checkPermissions()
    {
        if (!Auth::isUserLogged()) {
            throw new NotAuthenticatedException();
        }

        if (empty($this->colId)) {
            throw new \Exception(ERROR_EMPTY_ID);
        }
    }

    private function isAccessible()
    {
        $visibleCollObjs = Auth::isUserAdmin() ? (new CollectionProvider())->getList() : (new CollectionProvider())->getAccessedList((Auth::getUserID() == null) ? 0 : Auth::getUserID());

        $vCollIDs = array();
        foreach ($visibleCollObjs as $vCollObj) {
            $vCollIDs[] = $vCollObj->getId();
        }

        if (!in_array($this->colId, $vCollIDs)) {
            throw new \Exception(ERROR_EMPTY_ID);
        }
    }
}
