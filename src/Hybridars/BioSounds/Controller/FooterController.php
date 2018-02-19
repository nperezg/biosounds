<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Utils\Utils;
use Hybridars\BioSounds\Entity\Settings;

class FooterController
{
    protected $template = 'footer.phtml';
    protected $vars = array();
    protected $view;

    public function __construct() {
		$this->view = new View();		
        $this->view->projectName = Utils::getSetting(Settings::PROJECT_NAME);
        $this->view->filesLicense = Utils::getSetting(Settings::FILES_LICENSE);      
        $this->view->filesLicenseDetail = Utils::getSetting(Settings::FILES_LICENSE_DETAIL);  
    }
    
    public function getContent(){
		#License
		/*$files_license = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license'", $connection);
		$files_license_detail = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license_detail'", $connection);
		if ($files_license != ""){
			if ($files_license == "Copyright"){
				echo "&#169; Copyright: ";
				}
			else {
				$files_license_img = str_replace(" ", "", $files_license);
				$files_license_link = strtolower(str_replace("CC ", "", $files_license));
				echo "<a href=\"http://creativecommons.org/licenses/$files_license_link/3.0/\" target=_blank><img src=\"images/cc/$files_license_img.png\" alt=\"License\"></a> $files_license license: ";
				}
			
			if ($files_license_detail != ""){
				echo "\n$files_license_detail\n";
				}
			}
		echo "<br><br>";
		
		require("include/version.php");
		

		//Name of site
		$website_title="Pumilio";

		//Version
		#$website_version="2.2.0";
		$website_version = file_get_contents($absolute_dir . '/include/version.txt', true); */
		$this->view->content = "footer";

	}
	
    public function create() {
		$this->getContent();
        return $this->view->render($this->template);
    }
}
