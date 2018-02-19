#!/usr/bin/python
# Code created by Luis Villanueva for Pumilio.
# Automatic script to add files to the database

#########################################################################
# HEADER DECLARATIONS							#
#########################################################################

# Import modules
import commands
import os
import wave
import sys
import hashlib
import MySQLdb
import subprocess
import linecache
import time

#########################################################################
# COMMAND LINE ARGUMENTS						#
#########################################################################

# Place "global" variables in the namespace
logfile = "log.txt"

db_hostname=linecache.getline('configfile.php', 2)
db_database=linecache.getline('configfile.php', 3)
db_username=linecache.getline('configfile.php', 4)
db_password=linecache.getline('configfile.php', 5)
server_dir=linecache.getline('configfile.php', 6)

db_hostname=db_hostname.replace("\n", "")
db_database=db_database.replace("\n", "")
db_username=db_username.replace("\n", "")
db_password=db_password.replace("\n", "")
server_dir=server_dir.replace("\n", "")

#########################################################################
# FUNCTION DECLARATIONS							#
#########################################################################

#Extract wav from FLAC
def extractflac(item_flac, FileFormat):
	if FileFormat == 'flac':
		item_wav = item_flac[:-5] + '.wav'
		status, output = commands.getstatusoutput('flac -dFf ' + item_flac + ' -o ' + item_wav)
		if status != 0:
			print " "
			print "There was a problem processing " + item_flac + "!"
			print output
			updatefile(ToAddMemberID, str(9), '', "There was a problem processing the file with the flac decoder")
			item_wav = 2
	else:
		FileFormat_len = len(FileFormat)
		item_wav = item_flac[:-FileFormat_len] + 'wav'
		status, output = commands.getstatusoutput('sox ' + item_flac + ' ' + item_wav)
		if status != 0:
			updatefile(ToAddMemberID, str(9), '', "There was a problem converting the file to wav using SoX")
			item_wav = 0
	return item_wav


def fileExists(f):
	try:
		file = open(f)
	except IOError:
		exists = 0
	else:
		exists = 1
	return exists


def fileValid(file):
	#file = open(f)
	statinfo = os.stat(file)
	if statinfo.st_size>0:
		isvalid = 1
	else:
		isvalid = 0
	return isvalid

	
def cleanup(server_dir, ColID, DirID, FullPath, OriginalFilename, ToAddMemberID, SoundID):
	pathToSound = server_dir + 'sounds/sounds/' + ColID + '/' + DirID
	if os.path.exists(server_dir)==0:
		f = open(logfile, 'a')
		f.write("\n \n Could not find the sounds/ directory. Check your settings and try again.\n")
		f.close()
		updatefile(ToAddMemberID, str(9), SoundID, "Could not find the sounds/ directory. Check your settings and try again.")
		sys.exit(2)
	#	
	if os.path.exists(server_dir + 'sounds/sounds/' + ColID)==0:
		status, output = commands.getstatusoutput('mkdir ' + server_dir + 'sounds/sounds/' + ColID)
		if status != 0:
			f = open(logfile, 'a')
			f.write(" ERROR: Could not create necessary folder " + server_dir + 'sounds/sounds/' + ColID)
			f.close()
			updatefile(ToAddMemberID, str(9), SoundID, "ERROR: Could not create necessary folder " + server_dir + 'sounds/sounds/' + ColID)
			sys.exit(3)
	if os.path.exists(pathToSound)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToSound)
		if status != 0:
			f = open(logfile, 'a')
			f.write(" ERROR: Could not create necessary folder " + pathToSound)
			f.close()
			updatefile(ToAddMemberID, str(9), SoundID, "ERROR: Could not create necessary folder " + pathToSound)
			sys.exit(4)
	#
	#Move the already processed file to a done folder
	status, output = commands.getstatusoutput('cp ' + FullPath + ' ' + pathToSound + '/')
	if status != 0:
		f = open(logfile, 'a')
		f.write(" ERROR: Could not copy the file " + FullPath + " to the server directory")
		f.close()
		updatefile(ToAddMemberID, str(9), SoundID, "ERROR: Could not copy the file " + FullPath + " to the server directory " + pathToSound)
		sys.exit(5)
	#mark file as success
	updatefile(ToAddMemberID, str(0), '')
	return


def getmd5(flac_file):
	"""
	Getting the MD5 hash for the file.
	"""
	f1 = file(flac_file ,'rb')
	m = hashlib.md5()
	while True:
		t = f1.read(1024)
		if len(t) == 0: break
		m.update(t)
	return m.hexdigest()


def open_wave(file_wav):
	"""
	Open the wave file specified in the command line or elsewhere for processing.
	"""
	wave_pointer = wave.open(file_wav,'rb')
	return wave_pointer


def find_values(wave_pointer):
	"""
	Read the values to fill the "wave_vars" array from the sound file.
	"""
	wave_vars = {}
	#wave_vars['samp_rate'] = wave_pointer.getframerate()
	#wave_vars['num_samps'] = wave_pointer.getnframes()
	#wave_vars['samp_width'] = wave_pointer.getsampwidth()
	#ave_vars['no_channels'] = wave_pointer.getnchannels()
	# if wave_vars['samp_width'] == 1:
	# 	# The data are 8 bit unsigned
	# 	wave_vars['bit_code'] = 'B'
	# 	wave_vars['bits'] = '8'
	# elif wave_vars['samp_width'] == 2:
	# 	# The data are 16 bit signed
	# 	wave_vars['bit_code'] = 'h'
	# 	wave_vars['bits'] = '16'
	# elif wave_vars['samp_width'] == 4:
	# 	# The data are 32 bit signed
	# 	wave_vars['bit_code'] = 'i'
	# 	wave_vars['bits'] = '32'
	# else:
	# 	# I don't know what the hell it is
	# 	print "I don't know what the hell bit width you're using."
	# 	updatefile(ToAddMemberID, str(9), "Weird file, could not determine bits")
	# 	sys.exit(6)
	# wave_vars['max_time'] = wave_vars['num_samps'] / wave_vars['samp_rate']
	# # Print wave file values, mostly to debug
	#print "Wave values: "
	#for item in wave_vars.iteritems():
	#	print item
	#check valid file
	p = subprocess.Popen(['python', 'soundcheck.py', FullPath],stdout=subprocess.PIPE,stderr=subprocess.PIPE)
	output, errors = p.communicate()
	wav_vals = output.split()
	wave_vars['no_channels'] = wav_vals[2]
	wave_vars['samp_rate'] = wav_vals[1]
	wave_vars['bits'] = wav_vals[3]
	wave_vars['max_time'] = wav_vals[4]
	return wave_vars


#Insert data to MySQL
def tomysql(item_wav, OriginalFilename, FullPath, FileFormat, file_md5, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID):
	filesize=os.path.getsize(FullPath)
	#wave_pointer = open_wave(item_wav)
	wave_vars = find_values(FullPath)
	SoundID=insert(OriginalFilename, FileFormat, wave_vars['no_channels'], wave_vars['samp_rate'], wave_vars['bits'], wave_vars['max_time'], file_md5, str(filesize), ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID)
	#print "\n  MySQL Insert was successful"
	return SoundID


def insert(soundname, soundformat, no_channels, samplingrate, bitres, soundlength, file_md5, filesize, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (7)
	cursor = con.cursor()
	sounddate = Date[0:4] + '-' + Date[4:6] + '-' + Date[6:8]
	if len(Time)==5:
		Time = "0" + Time
	soundtime = Time[0:2] + ':' + Time[2:4] + ':' + Time[4:6]
	query = "INSERT INTO Sounds (ColID, SoundStatus, SoundName, OriginalFilename, SoundFormat, Channels, SamplingRate, BitRate, Duration, Date, Time, MD5_hash, FileSize, SiteID, DirID, SensorID) \
         VALUES (" + \
	`ColID` + ', 2, ' + `soundname` + ', ' + `soundname` + ', ' + `soundformat` + ', ' + `no_channels` + ', ' + `samplingrate` + ', ' + `bitres` + ', ' + `soundlength` + ', ' + `sounddate` + ', ' + `soundtime` + ', ' + `file_md5` +  ', ' + `filesize` + ', ' + `SiteID` + ', ' + `DirID` + ', ' + `SensorID` + ')'
	#print "Query: " + query + "\n"
	cursor.execute (query)
	SoundID=con.insert_id()
	#Close MySQL
	cursor.close ()
	con.close ()
	return str(SoundID)



def delrow(SoundID):
	#Open MySQL
	if int(SoundID) > 0:
		try:
			con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
		except MySQLdb.Error, e:
			print "Error %d: %s" % (e.args[0], e.args[1])
			sys.exit (7)
		cursor = con.cursor()
		query = "DELETE FROM Sounds WHERE SoundID=" + SoundID
		cursor.execute (query)
		#Close MySQL
		cursor.close ()
		con.close ()
	return



def updatefile(ToAddMemberID, Status, SoundID, message=""):
	Status = str(Status)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (8)
	cursor = con.cursor()
	con.autocommit(True)
	query = "UPDATE FilesToAddMembers SET ReturnCode=" + `Status` + ", ErrorCode=" + `message` + " WHERE ToAddMemberID=" + `ToAddMemberID` + " LIMIT 1"
	cursor.execute (query)
	#Close MySQL
	cursor.close ()
	con.close ()
	if Status == 9:
		delrow(SoundID)
	return


def getallsounds():
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT ToAddMemberID, FullPath, OriginalFilename, DATE_FORMAT(Date, '%Y%m%d') AS Date, DATE_FORMAT(Time, '%H%i%s') AS Time, SiteID, ColID, DirID, SensorID FROM FilesToAddMembers WHERE ReturnCode='1'"
	cursor.execute (query)
	if cursor.rowcount == 0:
		print "0 results"
		cursor.close ()
		con.close ()
		sys.exit(0)
	else:
		results = cursor.fetchall()
	cursor.close ()
	con.close ()
	return results


def checkfile(soundname):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT * FROM Sounds WHERE SoundName='" + soundname + "'"
	cursor.execute (query)
	how_many = cursor.rowcount
	cursor.close ()
	con.close ()
	return str(how_many)


def getrunningprocs():
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT COUNT(*) FROM FilesToAddMembers WHERE ReturnCode='2'"
	cursor.execute (query)
	if cursor.rowcount == 1:
		result = cursor.fetchone()
		result = result[0]
		if isinstance(result, (int, long, float, complex)) == False:
			result = 0
	else:
		result = 0
	cursor.close ()
	con.close ()
	return int(result)

	
def nocores():
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT Value from Settings WHERE Name='cores_to_use'"
	cursor.execute (query)
	if cursor.rowcount == 1:
		result = cursor.fetchone()
		result = result[0]
		if isinstance(result, (int, long, float, complex)) == False:
			result = 1
	else:
		result = 1
	if result == 0:
		result = 1
	cursor.close ()
	con.close ()
	return int(result)



def countrows(md5hash):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT * FROM Sounds WHERE MD5_hash='" + md5hash + "'"
	cursor.execute (query)
	cursor.rowcount
	cursor.close ()
	con.close ()
	return cursor.rowcount



#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

#Check if more than the allowed processed are running
nocores = nocores()
runningprocs = getrunningprocs()
#if runningprocs >= nocores:
	#Don't run over the allowed parallel processes
#	print "Too many processes running"
#	sys.exit (0)


#current path
this_path = os.getcwd()

#Get all soundfiles
results=getallsounds()
OriginalFullPath = ""

try:
	for row in results:
		#extract the variables for each row
		ToAddMemberID = row[0]
		ToAddMemberID = str(int(ToAddMemberID))
		OriginalFullPath = row[1]
		OriginalFilename = row[2]
		Date = row[3]
		Date = str(int(Date))
		Time = row[4]
		Time = str(int(Time))
		SiteID = row[5]
		SiteID = str(int(SiteID))
		ColID = row[6]
		ColID = str(int(ColID))
		DirID = row[7]
		DirID = str(int(DirID))
		SensorID = row[8]
		SensorID = str(int(SensorID))

		#check if filename already exists, don't add if it does
		file_check = checkfile(OriginalFilename)
		if file_check == 1:
			updatefile(ToAddMemberID, str(9), '', "File already exists in archive")
			continue
		
		#check if the file can be found
		if fileExists(OriginalFullPath)==0:
			updatefile(ToAddMemberID, str(9), '', "Could not find file " + str(OriginalFullPath))
			continue

		#check if the file is not empty
		if fileValid(OriginalFullPath)==0:
			updatefile(ToAddMemberID, str(9), '', "Empty or invalid file")
			continue

		#update record, set as in progress
		updatefile(ToAddMemberID, str(2), '')

		#copy to tmp folder
		status, output = commands.getstatusoutput('cp ' + OriginalFullPath + ' ' + this_path + '/' +  OriginalFilename)
		FullPath = this_path + '/' + OriginalFilename
		
		#check valid file
		p = subprocess.Popen(['python', 'soundcheck.py', OriginalFullPath],stdout=subprocess.PIPE,stderr=subprocess.PIPE)
		output, errors = p.communicate()
		wav_vals = output.split()
		FileFormat = wav_vals[0]
		
		if FileFormat != 'wav':
			item_wav = extractflac(OriginalFullPath, FileFormat)
			tmp_file1 = OriginalFullPath
			tmp_file2 = item_wav
		else:
			item_wav = OriginalFullPath
			tmp_file1 = OriginalFullPath
			tmp_file2 = item_wav

		if item_wav == '0' or item_wav == '1':
			continue

		file_md5=getmd5(FullPath)
		checkrows = int(countrows(file_md5))
		########CHECK FILE IS ALREADY IN THE DATABASE 
		if checkrows > 0:
			print file_md5
			print "File exist already"
			updatefile(ToAddMemberID, str(9), '', "File is already in the database")
			status, output = commands.getstatusoutput('rm ' + OriginalFullPath)	
			continue

		SoundID=tomysql(item_wav, OriginalFilename, OriginalFullPath, FileFormat, file_md5, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID)
		cleanup(server_dir, ColID, DirID, OriginalFullPath, OriginalFilename, ToAddMemberID, SoundID)
		#Wait when remote storage to allow for file to appear in NAS
		time.sleep(2)
		
		if fileValid(server_dir + 'sounds/sounds/' + ColID + '/' + DirID + '/' + OriginalFilename)==0:
			updatefile(ToAddMemberID, str(9), SoundID, "File could not be copied")
 			
		#remove tmp files
		status, output = commands.getstatusoutput('rm ' + OriginalFullPath)			
		if os.path.isfile(tmp_file1):
		        os.remove(tmp_file1)
		if os.path.isfile(tmp_file2):
		        os.remove(tmp_file2)

except Exception as inst: #Don't know what happened
	commands.getstatusoutput('rm *.flac')
	commands.getstatusoutput('rm *.wav')
	print "AN ERROR OCCURRED", inst
	sys.exit (10) #Exit with error

#check valid file
p = subprocess.Popen(['python', 'generate_files.py'],stdout=subprocess.PIPE,stderr=subprocess.PIPE)
output, errors = p.communicate()	
print errors;

commands.getstatusoutput('rm *.flac')
commands.getstatusoutput('rm *.wav')	
#commands.getstatusoutput('rm *.pyc')
print "FINISHED WITHOUT ERRORS"
sys.exit (0)
