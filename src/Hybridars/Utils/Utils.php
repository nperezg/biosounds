<?php

namespace Hybridars\Utils;

class Utils 
{
	public static function cleanTmpDir()
	{
		self::deleteDirContents('tmp/');
	}
	
	public static function deleteOldTmpFiles()
	{
		self::deleteOldFiles('tmp/', 1);
	}
	
	static function getSoundExtensions(){
		$soxExtensions = exec('sox -h | grep "FILE FORMATS:"', $out, $retval);
		$soxExtensions = substr($soxExtensions, 20, strlen($soxExtensions)); 
		return str_replace(" ", "," , $soxExtensions);
	}
	
	static function getSetting($name){
		if(isset($_SESSION["settings"])){
			$settings = $_SESSION["settings"];
			if(!isset($settings[$name]))
				return NULL;
			return $settings[$name];	
		}
	}
	
	static function isUploadRunning() {
		exec("pgrep -f 'python add_file_db.py' | wc -l", $process);
		return (int)$process[0] > 1;
	}


	static function cleanme($string) {
		$string = stripslashes($string);
		$string = trim($string);
		return $string;
	}

	static function is_odd($number) {
		return $number & 1; // 0 = even, 1 = odd
	}


	static function run_sox($input_file, $output_file, $trim_start, $trim_length, $filter_low, $filter_high) {
		exec('sox ' . $input_file . ' ' . $output_file . ' trim ' . $trim_start . 's ' . $trim_length . 's filter ' . $filter_low . '-' . $filter_high . ' 512', $lastline, $retval);
		return $retval;
	}


	static function flac2wav($input_file, $output_file) {
		exec('flac -d ' . $input_file . ' -f -o ' . $output_file, $lastline, $retval);
		return $retval;
	}

	static function save_log($connection, $SoundID, $LogType, $LogText) {
		$username = $_COOKIE["username"];
		if ($username != ""){
			$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);
			}
		else{
			$UserID = 0;
			}
						
		if (is_array($LogText)==TRUE){
			$LogText = implode(".", $LogText);
			}
		$query = "INSERT INTO PumilioLog (UserID, LogType, SoundID, LogText) VALUES
				('$UserID',  '$LogType',  '$SoundID',  '$LogText')";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
	}

		
	static function formatSize($size){
		switch (true){
			case ($size > 1099511627776):
			$size /= 1099511627776;
			$suffix = ' TB';
			break;
			case ($size > 1073741824):
			$size /= 1073741824;
			$suffix = ' GB';
			break;
			case ($size > 1048576):
			$size /= 1048576;
			$suffix = ' MB';   
			break;
			case ($size > 1024):
			$size /= 1024;
			$suffix = ' KB';
			break;
			default:
			$suffix = ' B';
			}
		return round($size, 2).$suffix;
	}

	/*
	 * Encode Sound File 
	 *
	 */
	static function player_file_mp3($originalFilePath, $samplingrate, $fileOutName, $tempPath) {
		$retval = 0;
		$samplingrate = 44100;
		$sampledFilePath = $originalFilePath;
		
		if ($samplingrate != 44100) {
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
			
			if (empty($randomID)){
				throw new \Exception("Random ID is empty");
			}
			
			$sampledFilePath = self::changeFileSampRate($originalFilePath, $to_SamplingRate, $tempPath);
		}
		exec('lame --noreplaygain -f -b 128 ' . $sampledFilePath . ' ' . $tempPath . $fileOutName, $lastline3, $retval);	
		
		return $retval;
	}

	static function changeFileSampRate($originalFilePath, $samplingRate, $tempPath){
		$fileName = "1" . substr($originalFilePath, strrpos($originalFilePath, "/")); 
		$finalFilePath = $tempPath . $fileName;
		
		exec('sox '. $originalFilePath . ' -r ' . $samplingRate . $finalFilePath, $lastline, $retval);
		if ($retval != 0) {
			throw new \Exception("There was a problem with SoX... Please contact your administrator.");
		}
		
		return $finalFilePath;			
	}

	static function dbfile_mp3($filename, $file_format, $ColID, $DirID, $SamplingRate) {
		#Function to make an mp3 file from a file in the Database
		$mp3_name = "";

		#New: sampling rate can be any of accepted Flash values: 11025,22050,44100

		#Check if file is an mp3 already
		if ($file_format == "mp3" && ($SamplingRate == 44100 || $SamplingRate == 22050 || $SamplingRate == 11025)) {
			#OK to use file
			$mp3_name = $filename;
			}
		else {
			$random_value = mt_rand();
			mkdir("tmp/$random_value", 0777);

			if ($SamplingRate > 44100) {
				$to_SamplingRate = 44100;
				}
			elseif ($SamplingRate < 44100 && $SamplingRate > 22050) {
				$to_SamplingRate=44100;
				}
			elseif ($SamplingRate < 22050 && $SamplingRate > 11025) {
				$to_SamplingRate = 22050;
				}
			elseif ($SamplingRate < 11025) {
				$to_SamplingRate = 11025;
				}
			else {
				$to_SamplingRate = $SamplingRate;
				}

			#If a flac, extract
			if ($file_format == "flac") {
				exec('flac -fd sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' -o tmp/' . $random_value . '/temp1.wav', $lastline, $retval);
				if ($retval != 0) {
					error_log("functions.php:dbfile_mp3: There was a problem with the FLAC decoder.");
					return null;
					}

				if ($SamplingRate!=44100 || $SamplingRate!=22050 || $SamplingRate!=11025) {
					exec('sox tmp/' . $random_value . '/temp1.wav tmp/' . $random_value . '/temp2.wav rate ' . $to_SamplingRate, $lastline, $retval);
					if ($retval!=0) {
						error_log("functions.php:dbfile_mp3: There was a problem with SoX.");
						return null;
						}
						
					unlink("tmp/$random_value/temp1.wav");
					rename("tmp/$random_value/temp2.wav", "tmp/$random_value/temp1.wav");
					}
				}
			else {
				exec('sox sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' tmp/' . $random_value . '/temp1.wav rate ' . $to_SamplingRate, $lastline, $retval);
				if ($retval!=0) {
					error_log("functions.php:dbfile_mp3: There was a problem with SoX.");
					return null;
					}
				}

			$fileName_exp = explode(".", $filename);
			$mp3_name = $fileName_exp[0] . ".autopreview.mp3";
			
			exec('lame --noreplaygain -f -b 128 tmp/' . $random_value . '/temp1.wav sounds/previewsounds/' . $ColID . '/' . $DirID . '/' . $mp3_name, $lastline3, $final_retval);
			#delete the temp folder
			self::deleteFolder('tmp/' . $random_value . '/');

			}
		return $mp3_name;
	}


	static function dbfile_ogg($filename, $file_format, $ColID, $DirID, $SamplingRate) {
		#Function to make an ogg file from a file in the Database
		$ogg_name = "";

		#Check if file is an ogg already
		if ($file_format == "ogg" && ($SamplingRate == 44100 || $SamplingRate == 22050 || $SamplingRate == 11025)) {
			#OK to use file
			$ogg_name = $filename;
			}
		else {
			$random_value = mt_rand();
			mkdir("tmp/$random_value", 0777);

			if ($SamplingRate > 44100) {
				$to_SamplingRate = 44100;
				}
			elseif ($SamplingRate < 44100 && $SamplingRate > 22050) {
				$to_SamplingRate = 44100;
				}
			elseif ($SamplingRate < 22050 && $SamplingRate > 11025) {
				$to_SamplingRate = 22050;
				}
			elseif ($SamplingRate < 11025) {
				$to_SamplingRate = 11025;
				}
			else {
				$to_SamplingRate = $SamplingRate;
				}

			#If a flac, extract
			if ($file_format == "flac") {
				exec('flac -fd sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' -o tmp/' . $random_value . '/temp1.wav', $lastline, $retval);
				if ($retval != 0) {
					save_log($connection, $SoundID, "70", "FLAC had a problem with sounds/sounds/$ColID/$DirID/$filename.\n" . $lastline);
					exit("<p class=\"error\">There was a problem with the FLAC decoder...</div>");
					}

				if ($SamplingRate != 44100 || $SamplingRate != 22050 || $SamplingRate != 11025) {
					exec('sox tmp/' . $random_value . '/temp1.wav tmp/' . $random_value . '/temp2.wav rate ' . $to_SamplingRate, $lastline, $retval);
					if ($retval != 0) {
						save_log($connection, $SoundID, "80", "SoX had a problem with sounds/sounds/$ColID/$DirID/$filename." . $lastline);
						exit("<p class=\"error\">There was a problem with SoX...</div>");
						}
						
					unlink("tmp/$random_value/temp1.wav");
					rename("tmp/$random_value/temp2.wav", "tmp/$random_value/temp1.wav");
					}
				}
			else {
				exec('sox sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' tmp/' . $random_value . '/temp1.wav rate ' . $to_SamplingRate, $lastline, $retval);
				if ($retval != 0) {
					save_log($connection, $SoundID, "80", "SoX had a problem with sounds/sounds/$ColID/$DirID/$filename." . $lastline);
					exit("<p class=\"error\">There was a problem with SoX...</div>");
					}
				}

			$fileName_exp = explode(".", $filename);
			$ogg_name = $fileName_exp[0] . ".autopreview.ogg";
			
			exec('dir2ogg tmp/' . $random_value . '/temp1.wav sounds/previewsounds/' . $ColID . '/' . $DirID . '/' . $ogg_name, $lastline3, $final_retval);
			#delete the temp folder
			self::deleteFolder('tmp/' . $random_value . '/');
			}
		return $ogg_name;
		}

		
	static function delSubTree($dir) {
		#delete everything but the dir
		$files = glob( $dir . '*', GLOB_MARK );
		foreach( $files as $file ){
			if( substr( $file, -1 ) == '/' ){
				self::deleteFolder( $file );
				}
			else {
				unlink( $file );
				}
			}
		#if (is_dir($dir)) rmdir( $dir );
		} 


	static function get_closest_weather($connection, $Lat, $Lon, $Date, $Time) {
		$weather_data_id = 0;
		$query = "SELECT WeatherSiteID, ((ACOS((SIN( '$Lat' /57.2958) * SIN( WeatherSiteLat /57.2958)) + (COS( '$Lat' /57.2958) * COS( WeatherSiteLat /57.2958) * COS( WeatherSiteLon /57.2958 - '$Lon' /57.2958)))) * 6378.7) AS Distance FROM WeatherSites ORDER BY Distance";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		for ($i = 0; $i < $nrows; $i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			
			#Is close enough? 20km
			$datetime = $Date . " " . $Time;
			$query_dq = "SELECT WeatherDataID, ABS(UNIX_TIMESTAMP('$datetime') - UNIX_TIMESTAMP(TIMESTAMP(WeatherDate, WeatherTime))) AS TimeDifference  FROM WeatherData ORDER BY TimeDifference LIMIT 1";
			$result_dq = mysqli_query($connection, $query_dq)
				or die (mysqli_error($connection));
			$nrows_dq = mysqli_num_rows($result_dq);
			if ($nrows_dq > 0) {
				$row_dq = mysqli_fetch_array($result_dq);
				extract($row_dq);
				break;
				}
			}

		if (!isset($WeatherDataID)){
			$WeatherDataID = 0;
			$TimeDifference = 0;
			$Distance = 0;
			}

		$to_return = $WeatherDataID . "," . $TimeDifference . "," . $Distance;
		return $to_return;
		}


	static function convertExifToTimestamp($exifString, $dateFormat) {
		$exifPieces = explode(":", $exifString);
		return date($dateFormat, strtotime($exifPieces[0] . "-" . $exifPieces[1] .
			"-" . $exifPieces[2] . ":" . $exifPieces[3] . ":" . $exifPieces[4]));
		}


	// Original PHP code by Chirp Internet: www.chirp.com.au 
	// Please acknowledge use of this code by including this header. 

	static function truncate2($string, $limit, $break = " ", $pad = "...") { 
		// return with no change if string is shorter than $limit  
		if(strlen($string) <= $limit) return $string; 
		
		$string = substr($string, 0, $limit); 
		if(false !== ($breakpoint = strrpos($string, $break))) { 
			$string = substr($string, 0, $breakpoint); 
			} 
		return $string . $pad; 
	}


	#From http://www.weberdev.com/get_example-3307.html
	#Usage :
	 # echo timeDiff("2002-04-16 10:00:00","2002-03-16 18:56:32");
	static function timeDiff($firstTime, $lastTime) {
		// convert to unix timestamps
		$firstTime = strtotime($firstTime);
		$lastTime = strtotime($lastTime);

		// perform subtraction to get the difference (in seconds) between times
		$timeDiff = $lastTime-$firstTime;

		// return the difference
		return $timeDiff;
		}


	#Function to find the number of cores available to do jobs
	# from http://www.theunixtips.com/how-to-find-number-of-cpus-on-unix-system
	static function nocores() {
		$no_cores = exec("grep processor /proc/cpuinfo | wc -l", $lastline, $return);
		return $lastline[0];
		}

	/* Execute command in background */
	static function execInBackground($command) {
		$command = $command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $pid = (int)$op[0];
		return($pid);
	}

	static function isProcessRunning($pid) {
		exec("ps $pid", $ProcessState);
		return(count($ProcessState) >= 2);
	}
	
	static function bgHowMany() {
		#Get how many background processed are running
		exec("pgrep add_to_pumiliodb* | wc -l", $bg_processes1);
		exec("pgrep stats_pumiliodb* | wc -l", $bg_processes2);
		$bg_processes = $bg_processes1[0] + $bg_processes2[0];
		return $bg_processes;
	}

	function bgHowManyAdd_PID() {
		#Get the PID of background processed are running
		exec("pgrep add_to_pumiliodb*", $bg_processes);
		return $bg_processes;
	}

	static function bgProcess_howlong($PID) {
		#Get the time running of background process
		# adapted from http://stackoverflow.com/questions/6134/how-do-you-find-the-age-of-a-long-running-linux-process
		exec("ps $PID | awk '{print $4}'", $bg_processes);
		return $bg_processes[1];
		}


	static function processFiles($absoluteDir, $uploadDir) {
		//$cores_to_use = query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
		//if ($cores_to_use == "" || $cores_to_use == "0"){
			$cores_to_use = 1;
			//}
		#$bg_processes = bgHowMany();
		$bg_processes = 0;//query_one("SELECT COUNT(*) from FilesToAddMembers WHERE ReturnCode='2'", $connection);

		if($bg_processes < $cores_to_use) {
			$pid = self::execInBackground("cd bin; python add_file_db.py");
		}
	}


	static function bgHowManyCheck() {
		#Get how many background processed are running
		exec("ps aux|grep check_auxfiles_pumiliodb | wc -l", $bg_processes);
		return $bg_processes[0];
		}


	static function check_in_background($absolute_dir, $connection) {
		require("config.php");
		$bg_processes = bgHowManyCheck();

		if($bg_processes < 3) {
			$random_value = mt_rand();
			$tmp_dir = 'tmp/' . $random_value;
			mkdir($tmp_dir, 0777);

			#make htaccess to protect files
				$myFile = $tmp_dir . '/.htaccess';
				$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
				fwrite($fh, "order allow,deny" . PHP_EOL);
				fwrite($fh, "deny from all" . PHP_EOL);
				fclose($fh);

			#write config file
				$myFile = $tmp_dir . '/configfile.php';
				$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
				fwrite($fh, "<?php" . PHP_EOL);
				fwrite($fh, "$host" . PHP_EOL);
				fwrite($fh, "$database" . PHP_EOL);
				fwrite($fh, "$user" . PHP_EOL);
				fwrite($fh, "$password" . PHP_EOL);
				fwrite($fh, "$absolute_dir/" . PHP_EOL);
				fwrite($fh, "?>");
				fclose($fh);
			
			copy('include/check_auxfiles/check_auxfiles_pumiliodb.py', $tmp_dir . '/check_auxfiles_pumiliodb.py');
			copy('include/check_auxfiles/svt.py', $tmp_dir . '/svt.py');

			exec('chmod -R 777 ' . $tmp_dir . ';cd ' . $tmp_dir . '; ./check_auxfiles_pumiliodb.py > /dev/null 2> /dev/null & echo $!', $out, $retval);

			}
		}


	static function stats_in_background($absolute_dir, $connection) {

		$cores_to_use = query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
		if ($cores_to_use == "" || $cores_to_use == "0"){
			$cores_to_use = 1;
			}
		require("config.php");
		$bg_processes = bgHowMany();
		if($bg_processes < $cores_to_use) {
			$random_value = mt_rand();
			$tmp_dir = 'tmp/' . $random_value;
			mkdir($tmp_dir, 0777);
		
			#make htaccess to protect files
				$myFile = $tmp_dir . '/.htaccess';
				$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
				fwrite($fh, "order allow,deny" . PHP_EOL);
				fwrite($fh, "deny from all" . PHP_EOL);
				fclose($fh);

			#write config file
				$myFile = $tmp_dir . '/configfile.php';
				$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
				fwrite($fh, "<?php" . PHP_EOL);
				fwrite($fh, "$host" . PHP_EOL);
				fwrite($fh, "$database" . PHP_EOL);
				fwrite($fh, "$user" . PHP_EOL);
				fwrite($fh, "$password" . PHP_EOL);
				fwrite($fh, "$absolute_dir/" . PHP_EOL);
				fwrite($fh, "$random_value" . PHP_EOL);
				fwrite($fh, "$R_ADI_db_value" . PHP_EOL);
				fwrite($fh, "$R_ADI_max_freq" . PHP_EOL);
				fwrite($fh, "$R_ADI_freq_step" . PHP_EOL);
				fwrite($fh, "$R_H_segment_length" . PHP_EOL);
				fwrite($fh, "?>");
				fclose($fh);
			
			copy('include/R/stats_pumiliodb.py', $tmp_dir . '/stats_pumiliodb.py');
			copy('include/R/getstats.R', $tmp_dir . '/getstats.R');
			exec('chmod +x ' . $tmp_dir . '/*', $out, $retval);
			exec('chmod -R 777 ' . $tmp_dir . '', $out, $retval);
			exec('cd ' . $tmp_dir . '; ./stats_pumiliodb.py > /dev/null 2> /dev/null & echo $!', $out, $retval);
			}
		}


	static function bgHowManyStats() {
		#Get how many background processed are running
		exec("pgrep stats_pumiliodb* | wc -l", $bg_processes);
		return $bg_processes[0];
		}


	static function bgHowManyStats_PID() {
		#Get the PID of background processed are running
		exec("pgrep stats_pumiliodb*", $bg_processes);
		return $bg_processes;
		}
				

	#From http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions	
	static function endsWith($haystack, $needle, $case = true){
		$expectedPosition = strlen($haystack) - strlen($needle);

		if($case)
		return strrpos($haystack, $needle, 0) === $expectedPosition;

		return strripos($haystack, $needle, 0) === $expectedPosition;
	}

	static function generateThumbnail($samplingRate, $freqMin, $freqMax, $timeMin, $timeMax, $wavFilePath, $channel, $duration) {	
		$specWidth = WINDOW_WIDTH - (SPECTROGRAM_LEFT + SPECTROGRAM_RIGHT);
		$randomID = $_SESSION["random_id"];
		
		$fftSize = self::getSetting("fft"); 
	    $palette = self::getSetting("spectrogram_palette"); 
		
		$viewport_black_width = 150;
		$viewport_black_height = round((SPECTROGRAM_HEIGHT / $specWidth) * $viewport_black_width);
			
		$nyquist = $samplingRate / 2;

		//Measurements for drawing the actual selection red box 
		$viewport_box_low = round($viewport_black_height - ((($freqMin - 10) / $nyquist) * $viewport_black_height));
		$viewport_box_high = round((($nyquist - $freqMax) / $nyquist) * $viewport_black_height);
		$viewport_box_left = round(($timeMin / $duration) * $viewport_black_width);
		$viewport_box_right = round(($timeMax / $duration) * $viewport_black_width);

		$viewport_box_width = round($viewport_box_right-$viewport_box_left);
		$viewport_box_height = round($viewport_box_low-$viewport_box_high);		

		$fileName = substr($wavFilePath, strrpos($wavFilePath, "/") + 1, strlen($wavFilePath));
		$fileName = explode(".", $fileName);

		$viewport_blackbox = $fileName[0] . "_" . $viewport_box_low . '_' . $viewport_box_high . "_" . $viewport_box_left . "_" . $viewport_box_right . "_" . $channel . ".png";
		if (!file_exists('tmp/' . $randomID . '/' . $viewport_blackbox)) {
			exec('bin/svt.py -s tmp/' . $randomID . '/' . $viewport_blackbox . ' -w ' . $viewport_black_width . ' -h ' . $viewport_black_height . ' -m ' . $nyquist . ' -f ' . $fftSize . ' -c ' . $channel . ' -p ' . $palette . ' ' . $wavFilePath, $lastline, $retval);
			exec("convert -stroke red -fill none -draw \"rectangle " . $viewport_box_left . "," . $viewport_box_high. " " . $viewport_box_right . "," . $viewport_box_low . "\" tmp/" . $randomID . "/" . $viewport_blackbox . " tmp/" . $randomID . "/" . $viewport_blackbox, $lastline, $retval);
			if ($retval != 0) {
				throw new \Exception("There was a problem with ImageMagick while generating the thumbnail.");
			}
		}
		
		return "tmp/$randomID/$viewport_blackbox";
	}
	
	static function encodePasswordHash($password){
		return base64_encode(password_hash($password, PASSWORD_BCRYPT));
	}		
	
	static function decodePasswordHash($password){
		return base64_decode($password);
	}
	
	static function checkPasswords($plainPwd, $encodedPassword){
		if($encodedPassword != NULL && password_verify($plainPwd, self::decodePasswordHash($encodedPassword)))
			return true;
		return false;	
	}
	
	private static function deleteDirectory($dir) 
	{
		self::deleteDirContents($dir);
		return rmdir($dir);
	} 
	
	private static function deleteDirContents($dir) 
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		  (is_dir("$dir/$file")) ? self::deleteDirContents("$dir/$file") : unlink("$dir/$file");
		}
	} 
	
	private static function deleteOldFiles($dir, $days) {
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
