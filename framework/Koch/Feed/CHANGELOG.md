
Changelog:
3 May 2009 Martin Brampton (counterpoint)
	Converted to PHP5

13-04-2006 Neil Thompson (neilt)
	added functionality for Atom 1.0

v1.7.2-ppt	11-21-05
	added Atom 1.0 support
	added enclosure support for RSS 2.0/ATOM 1.0
	added docs for v1.7.2-ppt only!

v1.7.2-mod	03-12-05
	added output function outputFeed for on-the-fly feed generation

v1.7.2	10-11-04
	license changed to LGPL

v1.7.1
	fixed a syntax bug
	fixed left over debug code

v1.7	07-18-04
	added HTML and JavaScript feeds (configurable via CSS) (thanks to Pascal Van Hecke)
	added HTML descriptions for all feed formats (thanks to Pascal Van Hecke)
	added a switch to select an external stylesheet (thanks to Pascal Van Hecke)
	changed default content-type to application/xml
	added character encoding setting
	fixed numerous smaller bugs (thanks to Sören Fuhrmann of golem.de)
	improved changing ATOM versions handling (thanks to August Trometer)
	improved the UniversalFeedCreator's useCached method (thanks to Sören Fuhrmann of golem.de)
	added charset output in HTTP headers (thanks to Sören Fuhrmann of golem.de)
	added Slashdot namespace to RSS 1.0 (thanks to Sören Fuhrmann of golem.de)

v1.6	05-10-04
	added stylesheet to RSS 1.0 feeds
	fixed generator comment (thanks Kevin L. Papendick and Tanguy Pruvot)
	fixed RFC822 date bug (thanks Tanguy Pruvot)
	added TimeZone customization for RFC8601 (thanks Tanguy Pruvot)
	fixed Content-type could be empty (thanks Tanguy Pruvot)
	fixed author/creator in RSS1.0 (thanks Tanguy Pruvot)

v1.6 beta	02-28-04
	added Atom 0.3 support (not all features, though)
	improved OPML 1.0 support (hopefully - added more elements)
	added support for arbitrary additional elements (use with caution)
	code beautification :-)
	considered beta due to some internal changes

v1.5.1	01-27-04
	fixed some RSS 1.0 glitches (thanks to Stéphane Vanpoperynghe)
	fixed some inconsistencies between documentation and code (thanks to Timothy Martin)

v1.5	01-06-04
	added support for OPML 1.0
	added more documentation

v1.4	11-11-03
	optional feed saving and caching
	improved documentation
	minor improvements

v1.3    10-02-03
	renamed to FeedCreator, as it not only creates RSS anymore
	added support for mbox
	tentative support for echo/necho/atom/pie/???

v1.2    07-20-03
	intelligent auto-truncating of RSS 0.91 attributes
	don't create some attributes when they're not set
	documentation improved
	fixed a real and a possible bug with date conversions
	code cleanup

v1.1    06-29-03
	added images to feeds
	now includes most RSS 0.91 attributes
	added RSS 2.0 feeds

v1.0    06-24-03
	initial release