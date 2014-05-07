#
# Simple Makefile for repo tools
#

PREFIX?=/usr/bin
INSTALL?=/usr/bin/install
LN?=/bin/ln
RM?=/bin/rm
SED?=sed

LINK=$(LN) -sf
RMF=$(RM) -f

all: install

install: install-mergetool
	$(INSTALL) -m 755 svnmerge $(PREFIX)/svnmerge.real
	for suffix in trunkdev stabletrunk trunk from; do \
		for prefix in cvdmerge cvdblock cvdunblock cvdavail; do \
			$(LINK) $(PREFIX)/svnmerge $(PREFIX)/$$prefix$$suffix ; \
		done; \
	done
	$(LINK) $(PREFIX)/svnmerge $(PREFIX)/mergeinit
	$(LINK) $(PREFIX)/svnmerge $(PREFIX)/autoinit

install-mergetool:
	$(SED) -e 's#INSTALL_PREFIX#$(PREFIX)#g' mergetool.sh > /tmp/mergetool.install
	$(INSTALL) -m 755 /tmp/mergetool.install $(PREFIX)/svnmerge
	$(RMF) /tmp/mergetool.install

uninstall:
	$(RMF) $(PREFIX)/svnmerge.real
	$(RMF) $(PREFIX)/svnmerge
	for suffix in trunkdev stabletrunk trunk from; do \
		for prefix in cvdmerge cvdblock cvdunblock cvdavail; do \
			$(RMF) $(PREFIX)/$$prefix$$suffix ; \
		done; \
	done
	$(RMF) $(PREFIX)/mergeinit
	$(RMF) $(PREFIX)/autoinit
