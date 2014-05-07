from setuptools import setup

PACKAGE = 'CommitTemplate'
VERSION = '0.1'

setup(
    name = PACKAGE,
    version = VERSION,
    
    author = "Michiel van Baak",
    author_email = "michiel@vanbaak.info",
	url = "http://www.covide.nl",
    
    description = "Adds commit template to tickets ",
    keywords = "trac plugins",
    license = "GPLv2",
    
    install_requires = [
        'Trac>=0.11',
    ],
    
    packages = ['committemplate'],
                                     
    entry_points = { 'trac.plugins': '%s = committemplate.web_ui' % PACKAGE },
)
