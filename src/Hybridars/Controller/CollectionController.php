<?php

namespace Hybridars\Controller;

use Hybridars\Entity\Collection;
use Hybridars\Entity\Sound;
use Hybridars\Utils\Auth;

class CollectionController
{
    protected $template = 'collection.phtml';
    protected $view;

    private $page;
    private $colID;
    private $numSounds;
    
    const ITEMS_PAGE = 9;

    public function __construct() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->view = new View();  
    }
    
    public function create() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($this->colID)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		$this->getContent();
        return $this->view->render($this->template);
    }
    
    public function getList(){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
	
		$collection = new Collection();
		$colsList = $collection->getFullListOrderBy(Collection::PRIMARY_KEY);
		
		$this->view->listCollections = "";
				
		foreach($colsList as $colData){
			$this->view->listCollections .= "<tr><th scope='row'>".$colData['ColID']."</th>";
			$this->view->listCollections .= "<td>".$colData['CollectionName']."</td>";
			$this->view->listCollections .= "<td>".$colData['Author']."</td>";
			$this->view->listCollections .= "<td>".$colData['Notes']."</td></tr>";
		}
		return $this->view->render("collectionsList.phtml");
	}
    
    public function show($id, $page = 1){
		if(!Auth::isUserLogged()){ 
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($id)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		
		$this->colID = $id;
		$this->page = $page;
		
		$collection = new Collection();
		$colData = $collection->getObject($this->colID);
		
		$this->view->collectionName = $colData["CollectionName"];
		$this->view->notes = $colData["Notes"];
		
		$sound = new Sound();
		$this->numSounds = $sound->countSoundsCollection($this->colID);		
		$this->view->numSounds = $this->numSounds;
		
		$this->getPaginator();
		
		/* In case we change page by AJAX - Form
		 * if(isset($_POST["selectedPage"]) && $_POST["selectedPage"] > 0)
			$this->page = filter_var($_POST["selectedPage"], FILTER_SANITIZE_INT);*/
			
		$pageFirstItemID = 	self::ITEMS_PAGE * ($this->page - 1);	
		$soundData = $sound->getSoundsPagByCollection($this->colID, self::ITEMS_PAGE, $pageFirstItemID);
		if($soundData != NULL)
			$this->setSoundsList($soundData);
	}
	
	private function getContent(){
		if(Auth::isUserAdmin()){
			$this->view->extraTop = "<div>
			<form action='collection/add' method='POST'>
			<input type='hidden' name='ColID' value='$this->colID'>
			<input type=submit value=' Add files '></form></div>";
		}
	}
	
	private function getPaginator(){
		if($this->numSounds <= 0)
			return;

		$this->numPages = ceil($this->numSounds / self::ITEMS_PAGE);
		
		if($this->page > $this->numPages) 
			$this->page = 1;

		for ($i = 1; $i <= $this->numPages; $i++) {
			$active = $this->page == $i ? "class='active'" : "";
			$this->view->paginator .= "<li $active><a href='collection/show/$this->colID/$i'>$i <span class='sr-only'></span></a></li>";
		}
	}	
	
	private function setSoundsList($soundData){
		foreach($soundData as $row) {
			$siteID = $row["SiteID"];
			$dirID = $row["DirID"];
			$soundName = $row["SoundName"];
			$date = $row["Date"];
			$soundID = $row["SoundID"];
			$imagePath = "sounds/images/$this->colID/$dirID/".$row["ImageFile"];	

			$this->view->soundList .= "<div class='col-lg-4 sound-list-item'>";
			
			if (!is_file($imagePath))	{
				$imagePath = "assets/images/notready-small.png";
			}

			$this->view->soundList .= "<a href='sound/show/$soundID' title='Click for file details and more options'>
				<img src='$imagePath'></a><span>$soundName</span><span>$date</span></div>"; 
		}
	}	
}
