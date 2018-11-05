<?php

namespace Hybridars\BioSounds\Service;

use Hybridars\BioSounds\Entity\File;
use Hybridars\BioSounds\Entity\Recording;
use Hybridars\BioSounds\Entity\Sound;
use Hybridars\BioSounds\Exception\File\ExtensionInvalidException;
use Hybridars\BioSounds\Exception\File\FileCopyException;
use Hybridars\BioSounds\Exception\File\FileExistsException;
use Hybridars\BioSounds\Exception\File\FileInvalidException;
use Hybridars\BioSounds\Exception\File\FileNotFoundException;
use Hybridars\BioSounds\Exception\File\FileQueueNotFoundException;
use Hybridars\BioSounds\Exception\File\FolderCreationException;
use Hybridars\BioSounds\Provider\FileProvider;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Provider\SoundProvider;
use Hybridars\BioSounds\Service\Queue\RabbitQueueService;
use Hybridars\BioSounds\Utils\Auth;
use Hybridars\BioSounds\Utils\Utils;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class FileService
 * @package Hybridars\BioSounds\Service
 */
class FileService
{
    const SUCCESS_MESSAGE = 'Files successfully sent to upload.';
    const DATETIME_INVALID_MESSAGE = 'File %s has no valid date/time pattern. Inserted without date or time.';
    const FILE_EXISTS_MESSAGE = 'File %s not inserted. It already exists in the system.';
    const DATE_FORMAT = '%d-%d-%d';
    const TIME_FORMAT = '%d:%d:%d';
    const DATE_TIME_PATTERN = '/(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})/';

    /**
     * @var Recording
     */
    private $recordingProvider;

    /**
     * @var FileProvider
     */
    private $fileProvider;

    /**
     * @var RabbitQueueService
     */
    private $queueService;

    /**
     * FileService constructor.
     */
    public function __construct()
    {
        $this->recordingProvider = new RecordingProvider();
        $this->fileProvider = new FileProvider();
        $this->queueService = new RabbitQueueService();
    }

    /**
     * @param array $request
     * @param string $uploadPath
     * @return array
     * @throws \Exception
     */
    public function upload(array $request, string $uploadPath): array
    {
        $message = $this::SUCCESS_MESSAGE;

        $colID = filter_var($request['colId'], FILTER_SANITIZE_NUMBER_INT);
        $site = filter_var($request['site'], FILTER_SANITIZE_NUMBER_INT);
        $sensor = filter_var($request['sensor'], FILTER_SANITIZE_NUMBER_INT);
        $dateFromFile = isset($request['dateFromFile'])
            ? filter_var($request['dateFromFile'], FILTER_VALIDATE_BOOLEAN) : false;
        $time = isset($request['time']) ? filter_var($request['time']) : null;
        $date = isset($request['date'])
            ? filter_var($request['date'], FILTER_SANITIZE_NUMBER_INT) : null;

        $reference = isset($request['reference'])
            ? filter_var($request['reference'], FILTER_VALIDATE_BOOLEAN) : false;
        $species = isset($request['species'])
            ? filter_var($request['species'], FILTER_SANITIZE_NUMBER_INT) : null;
        $soundType = isset($request['sound-type'])
            ? filter_var($request['sound-type'], FILTER_SANITIZE_NUMBER_INT) : null;
        $soundSubtype = isset($request['subtype']) && !empty($request['subtype'])
            ? filter_var($request['subtype'], FILTER_SANITIZE_STRING) : null;
        $rating = isset($request['rating']) && !empty($request['rating'])
            ? filter_var($request['rating'], FILTER_SANITIZE_STRING) : null;

        if (!is_dir($uploadPath) || !$handle = opendir($uploadPath)) {
            throw new FileNotFoundException($uploadPath);
        }

        try {
            while ($fileName = readdir($handle))
            {
                $fileDate = $date;
                $fileTime = $time;

                if ($fileName == '.' || $fileName == '..') {
                    continue;
                }

                if (!empty($this->recordingProvider->getByHash(hash_file('md5', $uploadPath . $fileName)))) {
                    $message = sprintf($message . ' ' . $this::FILE_EXISTS_MESSAGE, $fileName);
                    continue;
                }

                //Check that the extension is valid
                if (substr_count(Utils::getSoundExtensions(), strtolower(pathinfo($fileName, PATHINFO_EXTENSION))) < 1) {
                    throw new ExtensionInvalidException(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $fileName);
                }

                if ($dateFromFile) {
                    if (!preg_match($this::DATE_TIME_PATTERN, $fileName, $dateTime)) {
                        $message = sprintf($message . ' ' . $this::DATETIME_INVALID_MESSAGE, $fileName);
                    } else {
                        $fileDate = sprintf($this::DATE_FORMAT, $dateTime[1], $dateTime[2], $dateTime[3]);
                        $fileTime = sprintf($this::TIME_FORMAT, $dateTime[4], $dateTime[5], $dateTime[6]);
                    }
                }

                $file = (new File())
                    ->setPath($uploadPath . $fileName)
                    ->setDate($fileDate)
                    ->setTime($fileTime)
                    ->setCollection($colID)
                    ->setDirectory(rand(1,100))
                    ->setSensor($sensor)
                    ->setSite($site)
                    ->setName($fileName)
                    ->setUser(Auth::getUserID());

                if ($reference) {
                    $file->setSpecies($species)
                         ->setSoundType($soundType)
                         ->setSubtype($soundSubtype)
                         ->setRating($rating);
                }

                $this->queueService->add($this->fileProvider->insert($file));
            }
        } catch(\Exception $exception) {
            Utils::deleteDirContents($uploadPath);
            throw $exception;
        } finally {
            closedir($handle);
            $this->queueService->closeConnection();
        }
        return ['message' => $message];
    }

    /**
     * @param int $fileId
     * @throws \Exception
     */
    public function process(int $fileId)
    {
        $soundId = null;

        try {
            if (empty($file = $this->fileProvider->get($fileId))) {
                throw new FileQueueNotFoundException($fileId);
            }

            $file->setStatus(File::STATUS_IN_PROGRESS);
            $this->fileProvider->update($file);

            if (!file_exists($file->getPath())) {
                throw new FileNotFoundException($file->getPath());
            }

            if (!is_file($file->getPath())) {
                throw new FileInvalidException($file->getPath());
            }

            $fileHash = hash_file('md5', $file->getPath());
            if (!empty($this->recordingProvider->getByHash($fileHash))) {
                throw new FileExistsException($file->getName());
            }

            if (empty($fileFormat = Utils::getFileFormat($file->getPath()))) {
                return;
            }

            $wavFilePath = $file->getPath();
            if ($fileFormat !== 'wav') {
                $wavFilePath = Utils::generateWavFile($file->getPath());
            }

            $sound = [
                Recording::COL_ID => $file->getCollection(),
                Recording::FILE_DATE => $file->getDate(),
                Recording::DIRECTORY => $file->getDirectory(),
                Recording::FILE_TIME => $file->getTime(),
                Recording::SITE_ID => $file->getSite(),
                Recording::SENSOR_ID => $file->getSensor(),
                Recording::FILENAME => $file->getName(),
                Recording::CHANNEL_NUM =>  Utils::getFileChannels($wavFilePath),
                Recording::FILE_SIZE => filesize($wavFilePath),
                Recording::SAMPLING_RATE => Utils::getFileSamplingRate($wavFilePath),
                Recording::BITRATE => Utils::getFileBitRate($wavFilePath),
                Recording::NAME => $file->getName(),
                Recording::DURATION => floatval(Utils::getFileDuration($wavFilePath)),
                Recording::MD5_HASH => $fileHash
            ];

            if (!empty($file->getSpecies())) {
                $sound[Recording::SOUND_ID] = (new SoundProvider())->insert(
                    (new Sound())
                        ->setSpecies($file->getSpecies())
                        ->setType($file->getSoundType())
                        ->setSubtype($file->getSubtype())
                        ->setRating($file->getRating())
                );
            }

            $sound[Recording::ID] = (new RecordingProvider())->insert($sound);
            $soundId = $sound[Recording::ID];

            $path = ABSOLUTE_DIR . 'sounds/sounds/' . $file->getCollection() . '/' . $file->getDirectory();
            if (!is_dir(ABSOLUTE_DIR)) {
                throw new FileNotFoundException(ABSOLUTE_DIR);
            }

            if (!is_dir(ABSOLUTE_DIR . 'sounds/sounds/' . $file->getCollection()) &&
                !mkdir(ABSOLUTE_DIR . 'sounds/sounds/' . $file->getCollection())
            ) {
                throw new FolderCreationException(ABSOLUTE_DIR . 'sounds/sounds/' . $file->getCollection());
            }

            if (!is_dir($path) && !mkdir($path)) {
                throw new FolderCreationException($path);
            }

            if (!rename($file->getPath(), $path . '/' . $file->getName())) {
                throw new FileCopyException($file->getPath(), $path);
            }

            if ($fileFormat !== 'wav') {
                $pathInfo = pathinfo($wavFilePath);
                if (!rename($wavFilePath, $path . '/' . $pathInfo['filename'] . '.wav')) {
                    throw new FileCopyException($wavFilePath, $path);
                }
            }

            (new SpectrogramService())->generateImages($sound);

            $this->updateFileStatus($file, File::STATUS_SUCCESS, $sound[Recording::ID]);

        } catch (FileQueueNotFoundException $exception) {
            error_log($exception);
        } catch (ProcessFailedException $exception) {
            error_log($exception);
            if (!empty($file)) {
                $this->updateFileStatus(
                    $file,
                    File::STATUS_ERROR,
                    $soundId,
                    'Command failed : ' . $exception->getProcess()->getCommandLine());
            }
        } catch (\Exception $exception) {
            error_log($exception);
            if (!empty($file)) {
                $this->updateFileStatus($file, File::STATUS_ERROR, $soundId, $exception->getMessage());
            }
        }
    }

    /**
     * @param File $file
     * @param int $status
     * @param int|null $recordingId
     * @param string|null $errorMessage
     * @throws \Exception
     */
    private function updateFileStatus(
        File $file, int $status,
        int $recordingId = null,
        string $errorMessage = null
    ) {
        $file->setErrorMessage(empty($errorMessage) ? '' : filter_var($errorMessage, FILTER_SANITIZE_STRING));
        $file->setRecording($recordingId);
        $file->setStatus($status);
        $this->fileProvider->update($file);
    }
}