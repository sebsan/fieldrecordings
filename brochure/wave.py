#!/usr/bin/python


import sys
try:
	import scribus
except ImportError:
	print "This script only works from within Scribus"
	sys.exit(1)

obj = scribus.getSelectedObject(0)

sdir = 1
# options
fs = 8
fsmax = 14
fsmin = 8
inc = 0.0005
###

tl = scribus.getTextLength()
for c in range(tl - 1):
	scribus.selectText(c, 1, obj)
	if(sdir == 1):
		fs += inc
		if(fs > fsmax):
			sdir = 0
	else:
		fs -= inc
		if(fs < fsmin):
			sdir = 1
	scribus.setFontSize(fs,obj)