#! /usr/bin/env python
#
# Run tasks for covide offices like mailfetch
# funambol sync and rss fetch
#
# Copyright (c) 2007 Michiel van Baak <mvanbaak@users.sourceforge.net>
#
# This file is part of the Covide project

# Import the modules we need
import sys
import os
import time
import traceback
import signal
import popen2
import logging

class Daemon:
	"""
	Make the program detach from the console
	"""
	def __init__(self, pidfile):
		self.pidfile = pidfile

		# Check if we are already running
		if os.path.exists(self.pidfile):
			print "pidfile exists. We are already running?"
			sys.exit(1)

		# Do the double fork magic to detech
		try:
			pid = os.fork()
			if pid > 0:
				# Exit parent
				sys.exit(0)
		except OSError, e:
			print >>sys.stderr, "fork #1 failed: %d (%s)" % (e.errno, e.strerror)
			sys.exit(1)

		# decouple from parent env
		os.chdir("/")
		os.setsid()
		os.umask(0)

		# second fork
		try:
			pid = os.fork()
			if pid > 0:
				# Write pid to pidfile
				fh = open(self.pidfile, 'w')
				fh.write("%d" % pid)
				fh.close()

				# Exit parent
				sys.exit(0)
				# Here we are truly daemonized
		except OSError, e:
			print >>sys.stderr, "fork #2 failed: %d (%s)" % (e.errno, e.strerror)
			sys.exit(1)

	def cleanup(self):
		os.remove(self.pidfile)

# our worker class
class Covide:
	"""
	Class that does all the actual work
	"""
	# set the paths
	def __init__(self, covide_location, covide_bin_location):
		self.covide_location = covide_location
		self.covide_bin_location = covide_bin_location

	def run(self, timeout):
		"""
		Function to read the codes.txt and spawn subprocesses on them.
		Will sleep 'timeout' seconds after all offices are done.
		"""

		#print "[%s] - Starting cron run" % time.strftime('%d-%m-%Y %H:%M:%S')
		self.offices = [] # dict (think array) to hold all the configured offices
		while 1: # run forever
			officesfile = '%s/codes.txt' % self.covide_bin_location
			f = open(officesfile)
			# we put this in an try finally so we can close the file pointer when we have the content
			try:
				for line in f:
					line = line.strip() # strip newlines and spaces
					if line not in self.offices and len(line)>0: #only add to the list of offices to process when it's not already there
						self.offices.append(line)
			finally:
				f.close()

			for office in self.offices:
				print "[%s] - Processing office %s" % (time.strftime('%d-%m-%Y %H:%M:%S'), office)
				child = popen2.Popen4('cd '+self.covide_location+' && sudo -u www /usr/local/php5/bin/php index.php --host=localhost --no-output')
				output = [] # variable to buffer the output of the script
				counter = 0 # counter to terminate the process when it takes too long
				while 1:
					if (child.poll() == -1): # if still running
						counter += 1
						if counter > (900):
							os.kill(child.pid, 9)
						time.sleep(1)
					else:
						break
			#print "[%s] - Cron run done, sleeping for %s seconds" % (time.strftime('%d-%m-%Y %H:%M:%S'), timeout)
			time.sleep(timeout)

def handler(signum, frame):
	global daemon
	"""
	Signal handler. For now we terminate on every signal
	"""

	print "Got signal %s, Shutting down" % signum
	daemon.cleanup()
	sys.exit(0)

# Run program
if __name__ == "__main__":

	#logging.basicConfig(level=logging.DEBUG, format='%(asctime)s %(levelname)s %(message)s', filename='/tmp/covide_cron.log', filemode='w')
	# daemonize
	#daemon = Daemon('/var/run/covide.pid')

	# Handle signals
	for s in [signal.SIGABRT, signal.SIGINT, signal.SIGQUIT, signal.SIGTERM]:
		signal.signal(s, handler)
	
	covide = Covide('/Users/michiel/Sites/covide/covide-trunk', '/var/covide_files')
	covide.run(9)
