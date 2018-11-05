<?php

namespace Hybridars\BioSounds\Utils;

use Hybridars\BioSounds\Exception\File\WavProcessingException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Utils 
{
    /**
     * @param string $filePath
     * @return string
     */
    public static function getFileFormat(string $filePath): string
    {
        return trim(self::executeCommand('soxi -t ' . $filePath));
    }

    /**
     * @param string $filePath
     * @return null|string
     */
    public static function getFileChannels(string $filePath)
    {
        return self::executeCommand('soxi -c ' . $filePath);
    }

    /**
     * @param string $filePath
     * @return null|string
     */
    public static function getFileDuration(string $filePath)
    {
        return self::executeCommand('soxi -D ' . $filePath);
    }

    /**
     * @param string $filePath
     * @return null|string
     */
    public static function getFileBitRate(string $filePath)
    {
        return self::executeCommand('soxi -b ' . $filePath);
    }

    /**
     * @param string $filePath
     * @return null|string
     */
    public static function getFileSamplingRate(string $filePath)
    {
        return self::executeCommand('soxi -r ' . $filePath);
    }

    /**
     * @param string $originalFilePath
     * @param string $destinationFilePath
     * @param int $startTime
     * @param int $durationTime
     */
    public static function generateSoundFileSelection(
        string $originalFilePath,
        string $destinationFilePath,
        int $startTime,
        int $durationTime
    ) {
        $command = 'sox ' . $originalFilePath . ' ' . $destinationFilePath . ' ';
        $command .= 'trim ' . $startTime . 's ' . $durationTime . 's';
        self::executeCommand($command);
    }

    /**
     * @param string $originalFilePath
     * @param string $destinationFilePath
     * @param int $minimumFrequency
     * @param int $maximumFrequency
     */
    public static function filterFrequenciesSound(
        string $originalFilePath,
        string $destinationFilePath,
        int $minimumFrequency,
        int $maximumFrequency
    ) {
        $command = 'sox ' . $originalFilePath . ' ' . $destinationFilePath . ' ';
        $command .= 'sinc ' . $minimumFrequency . '-' . $maximumFrequency;
        self::executeCommand($command);
    }

    /**
     * @param string $filePath
     * @return null|string
     * @throws WavProcessingException
     */
    public static function generateWavFile(string $filePath): ?string
    {
        $pathInfo = pathinfo($filePath);
        $wavFilePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.wav';

        try {
            $process = new Process("sox $filePath $wavFilePath");
            if (self::getFileFormat($filePath) === 'flac') {
                $process = new Process("flac -dFf  $filePath -o $wavFilePath");
            }
            $process->mustRun();
            return $wavFilePath;
        } catch (ProcessFailedException $exception) {
            throw new WavProcessingException($filePath, $exception->getMessage());
        }
    }

    /**
     * @param string $command
     * @return string
     */
    public static function executeCommand(string $command)
    {
       return (new Process($command))
            ->mustRun()
            ->getOutput();
    }

	public static function deleteOldTmpFiles()
	{
		self::deleteOldFiles('tmp/', 1);
	}
	
	public static function getSoundExtensions()
    {
		$soxExtensions = exec('sox -h | grep "FILE FORMATS:"', $out, $retval);
		$soxExtensions = substr($soxExtensions, 20, strlen($soxExtensions)); 
		return str_replace(" ", "," , $soxExtensions);
	}
	
	public static function getSetting($name)
    {
		if(isset($_SESSION["settings"])){
			$settings = $_SESSION["settings"];
			if(!isset($settings[$name]))
				return null;
			return $settings[$name];	
		}
	}

    /**
     * @param $originalFilePath
     * @param $samplingrate
     * @param $fileOutName
     * @param $tempPath
     * @return int
     * @throws \Exception
     */
	public static function generateMp3File($originalFilePath, $samplingrate, $fileOutName, $tempPath)
    {
		//$samplingrate = 44100;
		$sampledFilePath = $originalFilePath;
		
		/*if ($samplingrate != 44100) {
			#Safe sampling rates for mp3 files
			if ($samplingrate > 44100) {
				$to_SamplingRate = 44100;
				$nyquist_freq = $to_SamplingRate/2;
				}
			elseif ($samplingrate < 44100 && $samplingrate > 22050) {
				$to_SamplingRate = 44100;
				$nyquist_freq = $samplingrate/2;
				}
			elseif ($samplingrate < 22050 && $samplingrate > 11025) {
				$to_SamplingRate = 22050;
				$nyquist_freq = $samplingrate/2;
				}
			elseif ($samplingrate < 11025) {
				$to_SamplingRate = 11025;
				$nyquist_freq = $samplingrate/2;
				}
			else {
				$to_SamplingRate = $samplingrate;
				$nyquist_freq = $samplingrate/2;
				}

			$sampledFilePath = self::changeFileSamplingRate($originalFilePath, $to_SamplingRate, $tempPath);
		}*/
		return self::executeCommand('lame --noreplaygain -f -b 128 ' . $sampledFilePath . ' ' . $tempPath . $fileOutName);
	}

	static function changeFileSamplingRate($originalFilePath, $samplingRate, $tempPath)
    {
		//$fileName = "1" . substr($originalFilePath, strrpos($originalFilePath, "/"));
        $pathInfo = pathinfo($originalFilePath);
		$finalFilePath = $tempPath . $pathInfo['filename'] . '.wav';
		
		exec('sox '. $originalFilePath . ' -r ' . $samplingRate . ' ' . $finalFilePath, $lastline, $retval);
		if ($retval != 0) {
			throw new \Exception("Error changing the file sampling rate.");
		}
		
		return $finalFilePath;			
	}

	public static function encodePasswordHash($password)
    {
		return base64_encode(password_hash($password, PASSWORD_BCRYPT));
	}		
	
	public static function decodePasswordHash($password)
    {
		return base64_decode($password);
	}
	
	public static function checkPasswords($plainPwd, $encodedPassword)
    {
		if($encodedPassword != null && password_verify($plainPwd, self::decodePasswordHash($encodedPassword)))
			return true;
		return false;	
	}

	public static function deleteDirContents($dir)
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		  (is_dir("$dir/$file")) ? self::deleteDirContents("$dir/$file") : unlink("$dir/$file");
		}
	} 
	
	private static function deleteOldFiles($dir, $days)
    {
		$files = array_diff(scandir($dir), array('.','..'));
		$now = time();

		foreach ($files as $file)
		{
			if (is_dir($dir.$file)) {	
				self::deleteOldFiles($dir.$file."/", $days);	
				if ($now - filemtime($dir.$file) >= 60 * 60 * 24 * $days)
					rmdir($dir.$file);	
			}
			else {
			  if ($now - filemtime($dir.$file) >= 60 * 60 * 24 * $days)
				unlink($dir.$file);
			}
		}
	}
}
