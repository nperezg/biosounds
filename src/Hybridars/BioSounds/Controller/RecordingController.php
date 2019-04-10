<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Classes\BaseController;
use Hybridars\BioSounds\Entity\Recording;
use Hybridars\BioSounds\Entity\Species;
use Hybridars\BioSounds\Entity\Tag;
use Hybridars\BioSounds\Entity\UserPermission;
use Hybridars\BioSounds\Entity\Permission;
use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Presenter\FrequencyScalePresenter;
use Hybridars\BioSounds\Presenter\RecordingPresenter;
use Hybridars\BioSounds\Presenter\TagPresenter;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Service\RecordingService;
use Hybridars\BioSounds\Service\ImageService;
use Hybridars\BioSounds\Utils\Auth;
use Hybridars\BioSounds\Utils\Utils;

class RecordingController extends BaseController
{
    const DEFAULT_TAG_COLOR = '#FFFFFF';
	const SOUND_PATH = 'sounds/sounds/%s/%s/%s';
    const IMAGE_SOUND_PATH = 'sounds/images/%s/%s/%s';

    protected $detailsTpl = 'soundFileDetail.phtml';
    protected $view;

    private $recordingId;
    private $pageTitle;
    private $fftSize;
    private $spectrogramService;
    private $openSound = false;
    private $collectionPage;
    private $recordingService;

    /**
     * @var RecordingPresenter
     */
    private $recordingPresenter;

    /**
     * RecordingController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

//		if(!Auth::isUserLogged()){
//			throw new \Exception(ERROR_NOT_LOGGED);
//		}
		$this->view = new View();
		$this->spectrogramService = new ImageService();
		$this->recordingPresenter = new RecordingPresenter();
		$this->recordingService = new RecordingService();
		$this->recordingPresenter->setSpectrogramHeight(SPECTROGRAM_HEIGHT);
		$this->fftSize = Utils::getSetting('fft');

    }

    /**
     * @return string
     * @throws \Exception
     */
    public function create()
    {
		if (!Auth::isUserLogged() && !$this->openSound) {
            throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($this->recordingId)) {
			throw new \Exception(ERROR_EMPTY_ID);
		}

        return $this->render('recording.html.twig', [
            'player' => $this->recordingPresenter,
            'sound' => $this->recordingPresenter->getRecording(),
            'frequency_data' => $this->recordingPresenter->getFrequencyScaleData(),
            'base_url' => APP_URL,
            'collection_page' => $this->collectionPage,
        ]);
    }

    /**
     * @param int $id
     * @param int $collectionPage
     * @throws \Exception
     */
    public function show(int $id, int $collectionPage = 1)
    {
        $this->recordingId = $id;
        $this->collectionPage = $collectionPage;

        $recordingData = (new RecordingProvider())->get($this->recordingId);

        $collectionId = $recordingData[Recording::COL_ID];

        //TODO: Take the hardcoded open collections out. Implement open collections management
        if ($collectionId == 1 || $collectionId == 3 || $collectionId == 18 || $collectionId == 31) {
            $this->openSound = true;
        }

        if (!Auth::isUserLogged() && !$this->openSound) {
            throw new \Exception(ERROR_NOT_LOGGED);
        } else if (empty($id)) {
            throw new \Exception(ERROR_EMPTY_ID);
        }

		$this->recordingPresenter->setRecording($recordingData);
	    $this->pageTitle = $recordingData[Recording::NAME];

        if(isset($_POST['showTags'])) {
            $this->recordingPresenter->setShowTags(filter_var($_POST['showTags'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($_POST['continuous_play'])) {
            $this->recordingPresenter->setContinuousPlay(
                filter_var($_POST['continuous_play'], FILTER_VALIDATE_BOOLEAN)
            );
        }
        if (isset($_POST['estimateDistID'])) {
            $this->recordingPresenter->setEstimateDistID(filter_var($_POST['estimateDistID'], FILTER_VALIDATE_INT));
        }
	    $this->setCanvas($recordingData);
	}

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
	public function details($id)
    {
        $data = (new RecordingProvider())->get($id);
        $collectionId = $data[Recording::COL_ID];

        if ($collectionId == 1 || $collectionId == 3) {
            $this->openSound = true;
        }
        if(!Auth::isUserLogged() && !$this->openSound){
            throw new \Exception(ERROR_NOT_LOGGED);
        } else if(empty($id)){
            throw new \Exception(ERROR_EMPTY_ID);
        }

		//$this->view->format = $data[Recording::FORMAT];
		$this->view->fileSize = $data[Recording::FILE_SIZE];
	    $this->view->duration = $data[Recording::DURATION];
		$this->view->numChannels = $data[Recording::CHANNEL_NUM];
	    $this->view->soundName = $data[Recording::NAME];
	    $this->view->samplingRate = $data[Recording::SAMPLING_RATE];
	    return $this->view->render($this->detailsTpl);
	}

    /**
     * @return mixed
     */
	public function getTitle()
    {
		return $this->pageTitle;
	}

    /**
     * @param $recordingData
     * @throws \Exception
     */
	private function setCanvas($recordingData)
    {
        $fileName = explode('.', $recordingData[Recording::FILENAME]);
        $imageFilePath = null;

		$originalMp3FilePath = sprintf(
            $this::SOUND_PATH,
            $recordingData[Recording::COL_ID],
            $recordingData[Recording::DIRECTORY],
            $fileName[0] . '.mp3'
        );
		$originalSoundFilePath = sprintf(
		    $this::SOUND_PATH,
            $recordingData[Recording::COL_ID],
            $recordingData[Recording::DIRECTORY],
            $recordingData[Recording::FILENAME]
        );
        $originalWavFilePath = sprintf(
            $this::SOUND_PATH,
            $recordingData[Recording::COL_ID],
            $recordingData[Recording::DIRECTORY],
            $fileName[0] . '.wav'
        );

		if (!empty($recordingData['ImageFile'])) {
            $imageFilePath = sprintf(
                $this::IMAGE_SOUND_PATH,
                $recordingData[Recording::COL_ID],
                $recordingData[Recording::DIRECTORY],
                $recordingData['ImageFile']
            );
        }
		$duration = $recordingData[Recording::DURATION];
		$samplingRate = $recordingData[Recording::SAMPLING_RATE];
		
		$this->recordingPresenter->setChannel(
		    isset($_POST['channel']) ? filter_var($_POST['channel'], FILTER_SANITIZE_NUMBER_INT) : 1
        );

	    $filter = false;
        $minFrequency = 1;
        $maxFrequency = $samplingRate / 2;
        $minTime = 0;
        $maxTime = $duration;

		/* Get the spectrogram selection values to generate zoom and filter */
		if (isset($_POST['t_min']) && isset($_POST['t_max']) && isset($_POST['f_min']) && isset($_POST['f_max'])) {
			$minTime = filter_var($_POST['t_min'], FILTER_SANITIZE_STRING);
			$maxTime = filter_var($_POST['t_max'], FILTER_SANITIZE_STRING);
			$minFrequency = filter_var($_POST['f_min'], FILTER_SANITIZE_STRING);
			$maxFrequency = filter_var($_POST['f_max'], FILTER_SANITIZE_STRING);
			if (isset($_POST['filter'])) {
                $filter = filter_var($_POST['filter'], FILTER_VALIDATE_BOOLEAN);
            }
		}
	    	    
	    // Spectrogram Image Width
		$spectrogramWidth = WINDOW_WIDTH - (SPECTROGRAM_LEFT + SPECTROGRAM_RIGHT);
		$this->recordingPresenter->setSpectrogramWidth($spectrogramWidth);

		$selectedFileName = $fileName[0] . '_' . $minFrequency . '-' . $maxFrequency . '_' . $minTime . '-'
            . $maxTime . '_' . $this->fftSize . '_' . $this->recordingPresenter->getChannel();

		if (!file_exists($originalWavFilePath)) {
		    Utils::generateWavFile($originalSoundFilePath);
		}
				
		#Generate a random number and store in session
		if (!isset($_SESSION['random_id'])) {
			$randomID = mt_rand();
			$_SESSION['random_id'] = $randomID;
		} 
		else {
            $randomID = $_SESSION['random_id'];
        }
			
		if (!file_exists("tmp/$randomID"))	{
            @mkdir("tmp/$randomID");
        }
			
		$spectrogramImagePath = 'tmp/' . $randomID . '/' . $selectedFileName.'.png';
		//$soundFileView =  'tmp/' . $randomID .'/'. $selectedFileName . '.mp3';
        $zoomedFilePlayer =  'tmp/' . $randomID .'/'. $selectedFileName . '.ogg';
		$zoomedFilePath = 'tmp/' . $randomID .'/'. $selectedFileName . '.' . $fileName[1];

		/* If spectrogram doesn't exist, generate */
		if (!file_exists($zoomedFilePlayer)) {
			$durationTime = round(($maxTime - $minTime) * $samplingRate); //Set to number of samples
			$startTime = round($minTime * $samplingRate); //Set to number of samples
			//$tempPath = 'tmp/' . $randomID .'/';
			$selectionFilePath = $filter ? 'tmp/1.' . $selectedFileName . '.' . $fileName[1] : $zoomedFilePath;

            if ($minTime != 0 || $maxTime != $duration) {
                Utils::generateSoundFileSelection(
                    $originalSoundFilePath,
                    $selectionFilePath,
                    $startTime,
                    $durationTime
                );
                if ($filter) {
                    Utils::filterFrequenciesSound(
                        $selectionFilePath,
                        $zoomedFilePath,
                        $minFrequency,
                        ($maxFrequency == $samplingRate / 2) ? $maxFrequency - 1 : $maxFrequency
                    );
                }
            } else {
//  				if (is_file($originalMp3FilePath)) {
//					copy($originalMp3FilePath, $soundFileView);
//				}
				if ($this->recordingPresenter->getChannel() == 1 && is_file($imageFilePath)) {
  					copy($imageFilePath, $spectrogramImagePath);
				}
               // copy($originalWavFilePath, $wavFilePath);
                copy($originalSoundFilePath, $zoomedFilePath);
			}

			/* Generation MP3 File */
			if(!file_exists($zoomedFilePlayer)) {
                $zoomedFilePlayer = $zoomedFilePath;

                if ($samplingRate <= 44100) {
                    $zoomedFilePlayer = Utils::convertToMp3($zoomedFilePath);
                }
                if ($samplingRate > 44100 && $samplingRate <= 192000) {
                    $zoomedFilePlayer = Utils::convertToOgg($zoomedFilePath);
                }
			}

			$this->recordingService->generateSpectrogramImage(
                $spectrogramImagePath,
                Utils::generateWavFile($zoomedFilePath),
                $maxFrequency,
                $this->recordingPresenter->getChannel(),
                $minFrequency
            );
		}

		$this->recordingPresenter->setMinTime(round($minTime, 1));
        $this->recordingPresenter->setMaxTime(round($maxTime, 1));
        $this->recordingPresenter->setMinFrequency($minFrequency);
        $this->recordingPresenter->setMaxFrequency($maxFrequency);
        $this->recordingPresenter->setDuration($duration);
        $this->recordingPresenter->setFileFreqMax($samplingRate / 2);
        $this->recordingPresenter->setFilePath(APP_URL . '/' . $zoomedFilePlayer);
        $this->recordingPresenter->setImageFilePath(APP_URL . '/' . $spectrogramImagePath);
        $this->recordingPresenter->setUser(empty(Auth::getUserID()) ? 0 : Auth::getUserID());

		$this->generateFrequenciesScale($maxFrequency, $minFrequency);
		$this->setTags($minTime, $maxTime, $minFrequency, $maxFrequency, $spectrogramWidth);
		$this->setTime($maxTime, $minTime);
        $this->recordingService->setViewPort(
            $this->recordingPresenter,
            $samplingRate,
            $this->recordingPresenter->getChannel(),
            $originalWavFilePath
        );
		//$this->setViewPort($samplingRate, $this->recordingPresenter->getChannel(), $originalWavFilePath);
	}

    /**
     * @param int $maxFrequency
     * @param int $minFrequency
     */
	private function generateFrequenciesScale(int $maxFrequency, int $minFrequency)
    {
        $range = $maxFrequency - $minFrequency;
        $steps = round($range / 4);
        $freqDigits = strlen((string)$steps);
        $freqMid1 = round($minFrequency + $steps, -$freqDigits + 1);
        $freqMid2 = round($minFrequency + ($steps * 2), -$freqDigits + 1);
        $freqMaxHeight = SPECTROGRAM_HEIGHT * ((($maxFrequency - $freqMid2) / $range) / 2);
        $freqMid2Height = $freqMaxHeight / 2 + SPECTROGRAM_HEIGHT * ((($freqMid2 - $freqMid1) / $range) / 2);
        $freqMid1Height = $freqMid2Height / 2 + SPECTROGRAM_HEIGHT * ((($freqMid1 - $minFrequency) / $range) / 2);
        $freqMinHeight =  $freqMid1Height / 2 + SPECTROGRAM_HEIGHT * ((($freqMid1 - $minFrequency) / $range) / 2);

        $this->recordingPresenter->setFrequencyScaleData(
            (new FrequencyScalePresenter())
                ->setFreqMaxHeight($freqMaxHeight)
                ->setFreqMax($maxFrequency)
                ->setFreqMid1($freqMid1)
                ->setFreqMid1Height($freqMid1Height)
                ->setFreqMid2($freqMid2)
                ->setFreqMid2Height($freqMid2Height)
                ->setFreqMin($minFrequency)
                ->setFreqMinHeight($freqMinHeight)
        );
    }

    /**
     * @param $maxTime
     * @param $minTime
     */
	private function setTime($maxTime, $minTime)
    {
		$step = 10.85;
		$dur = $maxTime - $minTime;
		$dur_ea = $dur / $step;
		$second1 = $minTime;

		for($i = 0; $i < 11; $i++){
			if($i > 0)
				$second1 = $second1 + $dur_ea;
			$second = round($second1, 0);
			if ($dur_ea < 1){
				$second = round($second1, 1);
			}
			$this->recordingPresenter->addTimeScaleSecond($second);
		}
	}

    /**
     * @param $viewTimeMin
     * @param $viewTimeMax
     * @param $viewFreqMin
     * @param $viewFreqMax
     * @param $specWidth
     * @throws \Exception
     */
	private function setTags($viewTimeMin, $viewTimeMax, $viewFreqMin, $viewFreqMax, $specWidth)
    {
		$soundTagModel = new Tag();
		$viewPermission = false;  
		$reviewPermission = false;
		
		if (!Auth::isUserAdmin()) {
			$userPerm = new UserPermission(); 
			$perm = empty(Auth::getUserLoggedID()) ? null : $userPerm->getUserColPermission(
			    Auth::getUserLoggedID(),
                $this->recordingPresenter->getRecording()[Recording::COL_ID]
            );

			if (empty($perm)) {
                $perm = -1;
            }

			$_SESSION['user_col_permission'] = $perm;
						
			$permission = new Permission();		
			$reviewPermission = $permission->isReviewPermission($perm);
			$viewPermission = $permission->isViewPermission($perm);
		}  	

		if (Auth::isUserAdmin() || $reviewPermission || $viewPermission) {
		    $tags = $soundTagModel->getList($this->recordingId);
        } else {
            $tags = $soundTagModel->getList($this->recordingId, Auth::getUserLoggedID());
        }

		if (!empty($tags)) {
			$viewTotalTime = $viewTimeMax - $viewTimeMin;
			$viewFreqRange = $viewFreqMax - $viewFreqMin;
			
			$i = count($tags);	
			$user = new User();

			$listTags = [];
			foreach($tags as $key => $value) {
				$tagID = $value[Tag::ID];
				$tagTimeMax = $value[Tag::MAX_TIME];
				$tagTimeMin = $value[Tag::MIN_TIME];
				$tagFreqMin = $value[Tag::MIN_FREQ];
				$tagFreqMax = $value[Tag::MAX_FREQ];
				$tagUser = $value[Tag::USER_ID];
				$tagStyle = !isset($value[Tag::CALL_DISTANCE]) && empty($value[Tag::DISTANCE_NOT_ESTIMABLE]) ? 'tag-orange' : '';
				$tagStyle = empty($value['reviews']) ? $tagStyle .' tag-dashed' : $tagStyle;
				
				if(empty($userTagColor = $user->getTagColor($tagUser)))
					$userTagColor = self::DEFAULT_TAG_COLOR;

				#Only show if some part of the mark is inside the current window
				if ($tagTimeMin < $viewTimeMax && $tagTimeMax > $viewTimeMin
                    && $tagFreqMin < $viewFreqMax && $tagFreqMax > $viewFreqMin) {
					//Time and freq calculations to draw the boxes of tags

					if ($tagTimeMax > $viewTimeMax) 
						$tagTimeMax = $viewTimeMax;

					if ($tagTimeMin < $viewTimeMin) {
						$time_i = 0;
						$tagTimeMin = $viewTimeMin;
					} else 
						$time_i=(($tagTimeMin-$viewTimeMin)/$viewTotalTime) * $specWidth;

					if ($tagFreqMax > $viewFreqMax) {
						$freq_i = 0;
						$tagFreqMax = $viewFreqMax;
					} else
						$freq_i = ((($viewFreqRange + $viewFreqMin)-$tagFreqMax)/$viewFreqRange) * SPECTROGRAM_HEIGHT;

					if ($tagFreqMin < $viewFreqMin) {
						$freq_w = SPECTROGRAM_HEIGHT - $freq_i;
					} else {
                        $freq_w = (($tagFreqMax-$tagFreqMin)/$viewFreqRange) * SPECTROGRAM_HEIGHT;
                    }

                    $time_w = (($tagTimeMax-$tagTimeMin)/$viewTotalTime) * $specWidth;
			
					$pos = $i + 800;
                    $listTags[] = (new TagPresenter())
                        ->setId($tagID)
                        ->setPosition($pos)
                        ->setTitle($value[Species::BINOMIAL])
                        ->setHeight($freq_w)
                        ->setWidth($time_w)
                        ->setLeft($time_i)
                        ->setTop($freq_i)
                        ->setStyle($tagStyle)
                        ->setColor($userTagColor);
					$i--;
				}
			}
			$this->recordingPresenter->setTags($listTags);
		}	
	}
}
