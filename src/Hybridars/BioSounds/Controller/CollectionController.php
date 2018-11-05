<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Classes\BaseController;
use Hybridars\BioSounds\Entity\Collection;
use Hybridars\BioSounds\Entity\Sound;
use Hybridars\BioSounds\Provider\CollectionProvider;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Service\RecordingService;
use Hybridars\BioSounds\Utils\Auth;

class CollectionController extends BaseController
{
    const GALLERY_TEMPLATE = 'collection/views/gallery.html.twig';
    const LIST_TEMPLATE = 'collection/views/list.html.twig';

    private $template;

    protected $view;
    private $display;

    private $page;
    private $colId;
    private $recordingNum;
    private $pageNum;
    private $openCollection = false;
    private $collection;
    private $recordings = [];

    private $filter = [];
    
    const ITEMS_PAGE = 9;

    /**
     * @return string
     * @throws \Exception
     */
    public function create()
    {
        $this->checkPermissions();

        return $this->render('collection.html.twig', [
            'collection' => $this->collection,
            'pageNum' => $this->pageNum,
            'page' => $this->page,
            'recordingNum' => $this->recordingNum,
            'list' => $this->recordings,
            'template' => $this->template,
            'baseUrl' => APP_URL,
            'display' => $this->display,
            'filter' => $this->filter,
        ]);
    }

    /**
     * @param int $id
     * @param int $page
     * @param string|null $view
     * @throws \Exception
     */
    public function show(int $id, int $page, string $view = null)
    {
        $this->colId = $id;

        //TODO: Set open collections in administration (not hard-coded)
        if ($id == 1 || $id == 3 || $id == 18) {
            $this->openCollection = true;
        }

        $this->checkPermissions();

        $this->page = $page;

        $this->collection = (new CollectionProvider())->get($this->colId);

        $this->display = $view == null ? $this->collection->getView() : $view;
        $this->template = $this->display == Collection::LIST_VIEW ? self::LIST_TEMPLATE : self::GALLERY_TEMPLATE;

        if (isset($_POST['species']) && !empty($_POST['species'])) {
            $this->filter[Sound::SPECIES_ID] = filter_var($_POST['species'], FILTER_VALIDATE_INT);
        }

        if (isset($_POST['rating']) && !empty($_POST['rating'])) {
            $this->filter[Sound::RATING] = filter_var($_POST['rating'], FILTER_SANITIZE_STRING);
        }

        $recordingProvider = new RecordingProvider();
        $this->recordingNum = $recordingProvider->countReady($this->colId, $this->filter);

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

        if (isset($_POST['species-name'])) {
            $this->filter['speciesName'] = filter_var($_POST['species-name'], FILTER_SANITIZE_STRING);
        }
    }

    /**
     * TODO: Move to collection admin
     * @return string
     * @throws \Exception
     */
    public function getList()
    {
        if (!Auth::isUserAdmin()){
            throw new \Exception(ERROR_NO_ADMIN);
        }

        $list = (new CollectionProvider())->getListOrderById();

        $this->view->listCollections = "";

        foreach($list as $item){
            $this->view->listCollections .= "<tr><th scope='row'>" . $item->getId() ."</th>";
            $this->view->listCollections .= "<td>" . $item->getName() . "</td>";
            $this->view->listCollections .= "<td>" . $item->getAuthor() . "</td>";
            $this->view->listCollections .= "<td>" . $item->getNote() . "</td></tr>";
        }
        return $this->view->render("collectionsList.phtml");
    }

    /**
     * @throws \Exception
     */
    private function checkPermissions()
    {
        if (!Auth::isUserLogged() && !$this->openCollection) {
            throw new \Exception(ERROR_NOT_LOGGED);
        } else {
            if (empty($this->colId)) {
                throw new \Exception(ERROR_EMPTY_ID);
            }
        }
    }
}
