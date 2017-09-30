#!/usr/bin/env python
 
# Script to get basic info on the file
#uses soxi or metaflac instead of audiolab

import sys, commands

# Test command line variables
if len(sys.argv) != 2:
	print " "
	print "Incorrect number of arguments given."
	print "Call the program as: " + sys.argv[0] + " <file>"
	print " "
	print "Exiting program."
	print " "
	sys.exit(1)

# Place "global" variables in the namespace
file_2_check = sys.argv[1]


#get values
status, sampling_rate = commands.getstatusoutput('soxi -r ' + file_2_check)
if status != 0:
	sys.exit(1)
#if above worked, the rest should
status, fileformat = commands.getstatusoutput('soxi -t ' + file_2_check)
status, channels = commands.getstatusoutput('soxi -c ' + file_2_check)
status, bitrate = commands.getstatusoutput('soxi -b ' + file_2_check)
status, duration = commands.getstatusoutput('soxi -D ' + file_2_check)

print str.lower(str(fileformat)) + " " + str(sampling_rate) + " " + str(channels) + " " + str(bitrate) + " " + str(duration)
sys.exit(0)