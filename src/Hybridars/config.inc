<?php

/**
 * Database login data
 */  
define("HOST", "localhost");   
define("USER", "upumilio");    
define("PASSWORD", "SsmxVx10+u");  
define("DATABASE", "mononoke"); 
define("DRIVER", "mysql");     
 
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");

#URL of the application, no trailing slash 
define("APP_URL", "http://localhost/biosounds");
define("APP_DIR", "/biosounds");
#Absolute directories in the server
define("ABSOLUTE_DIR", "/var/www/prueba.com/public_html/biosounds/");
define("IMAGES_URL", APP_URL."/assets/images/");
define("PROJECT_IMAGES_URL", IMAGES_URL."project/");

#Custom LOGO
define("LOGO", IMAGES_URL."biosounds_logo_small.png");

define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!

/* Spectrogram generation data*/
define("WINDOW_WIDTH", 940);   
define("SPECTROGRAM_LEFT", 0);    
define("SPECTROGRAM_RIGHT", 70);  
define("SPECTROGRAM_HEIGHT", 400); 

define("ERROR_NOT_LOGGED", "Please, log in.");
define("ERROR_EMPTY_ID", "Empty Item ID.");
define("ERROR_NOT_ALLOWED","User has no privileges to access this sound tag.");
define("ERROR_NO_ADMIN","User has not privileges to access this functionality.");
define("ERROR_UPLOAD_RUNNING", "There is an upload process running. Please try later.");

#Administrator email
# Who will be the admin, useful for login problems and others
//Kevin: updated to Kevin's email
$app_admin_email="nperezg@hybridars.com";
#Is the server configured to send email?
# The default is FALSE
$app_allow_email=FALSE; #FALSE or TRUE

#Use R?
# When true, the system can extract more data from the files by running 
# custom code that can be used for quality control. It also lets the R 
# plugin know where to find R. 
$useR = FALSE; #TRUE or FALSE
#If true, you can set the custom path for the R installation you want to
#  use and other Rscript arguments. Can be left as it is to use the 
#  default. Most users won't need to change this line.
$Rscript = "Rscript --vanilla ";
#ADI settings
$R_ADI_db_value = "-50"; #dBFS

##The spectrogram should be drawn from 0 to this frequency, in Hertz. Default is 10000
$R_ADI_max_freq = "10000"; #in Hz

##How wide is each frequency band
$R_ADI_freq_step = "1000"; #in Hz


#Special variables for rare cases
$special_wrapper=FALSE;
$special_noopen=FALSE;
$special_noprocess=FALSE;

$fft_default=1024;

?>