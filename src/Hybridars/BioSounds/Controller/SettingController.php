<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Setting;
use Hybridars\BioSounds\Utils\Auth;

class SettingController
{
    protected $template = 'settings.phtml';
    protected $view;

    /**
     * SettingController constructor.
     */
    public function __construct()
    {
		$this->view = new View();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function create()
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN);
		}
		$this->getContent();
        return $this->view->render($this->template);
    }

    /**
     * @return bool
     * @throws \Exception
     */
	public function save()
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN);
		}
		$setting = new Setting();
	    foreach ($_POST as $key => $value) {
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			$setting->update($key, $value);
		}

		$_SESSION['settings'] = $setting->getList();
		return true;
	}

    /**
     * @throws \Exception
     */
	private function getContent()
    {
		$setting = new Setting();
		$this->view->projectName = $setting->getByName('projectName');
		$this->view->projectDescription = $setting->getByName('projectDescription');
		$this->view->filesLicense = $setting->getByName('filesLicense');
		$this->view->filesLicenseDetail = $setting->getByName('filesLicenseDetail');
		
		$fft = $setting->getByName('fft');
		$fftOptions = '<option ' . ($fft == 4096 ? 'selected' : '') . '>4096</option>
		<option ' . ($fft == 2048 ? 'selected' : '') . '>2048</option>
		<option ' . ($fft == 1024 ? 'selected' : '') . '>1024</option>
		<option ' . ($fft == 512 ? 'selected' : '') . '>512</option>
		<option ' . ($fft == 256 ? 'selected' : '') . '>256</option>
		<option ' . ($fft == 128 ? 'selected' : '') . '>128</option>';
		$this->view->fftOptions = $fftOptions;
				
		$files_license = $setting->getByName('filesLicense');
		$this->view->filesLicense = '<option ' . ($files_license=='Copyright' ? 'selected' : '') . " value='Copyright'>&#169; Copyright</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY' ? 'selected' : '') . " value='CC BY'>CC BY</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY-SA' ? 'selected' : '') . " value='CC BY-SA'>CC BY-SA</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY-ND' ? 'selected' : '') . " value='CC BY-ND'>CC BY-ND</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY-NC' ? 'selected' : '') . " value='CC BY-NC'>CC BY-NC</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY-NC-SA' ? 'selected' : '') . " value='CC BY-NC-SA'>CC BY-NC-SA</option>";
		$this->view->filesLicense .= '<option ' . ($files_license=='CC BY-NC-ND' ? 'selected' : '') . " value='CC BY-NC-ND'>CC BY-NC-ND</option>";
	}	
}
