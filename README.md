# README #
The Learn Chemistry Wiki was a wiki (based on open-source MediaWiki software) of secondary school chemistry information (Substances, Experiments, Reactions and Quizzes) for teachers and students to view, interact with and contribute to. It was hosted by the Royal Society of Chemistry at http://www.rsc.org/learn-chemistry/wiki/ between January 2012 until September 2017. It has since been shut down and most of its resources consolidated into the main Learn Chemistry (http://www.rsc.org/learn-chemistry) site. 
The Learn Chemistry Wiki featured 2 Mediawiki extensions that were developed by the Royal Society of Chemistry to enhance the chemistry functionality of the Learn Chemistry Wiki. We are making them available here for anyone that they might be useful to, but would caution that we will not be maintaining it, which will become more of an issue as the MediaWiki and 3rd party software that they rely on move on (the current versions are already different from those that these extensions were developed with). 
The Chemistry MediaWiki extensions developed as part of the Learn Chemistry Wiki are:
 - The KetcherDrawer extension will add a Ketcher (http://lifescience.opensource.epam.com/ketcher/ ) structure drawer to a page and accompanying extensions can use the drawn structure
 - DisplaySpectrum extension to add interactive spectra to a page using a ChemDoodle (https://www.chemdoodle.com/ ) widget, and JSpecView (http://jspecview.sourceforge.net/  ) applet
There is more information about these extensions and how they work at: http://www.sciencedirect.com/science/article/pii/B9781907568978500035 
### Set up ###
* Install MediaWiki - we were working with version 1.16.0, which was the latest stable version at the time
* To install the KetcherDrawer extension:
** copy the KetcherDrawer folder here into the extensions directory of your mediawiki installation on your server
** copy the Ketcher code into the extensions/KetcherDrawer/ketcher folder (e.g. extensions/KetcherDrawer/ketcher/ketcher.js). We used 
** edit your MediaWiki LocalSettings.php file to add the following line to enable the extension: require_once "extensions/ExtensionName/ExtensionName.php";
* To install the DisplaySpectrum extension:
**...
* see https://www.mediawiki.org/wiki/Extensions_FAQ for more information about installing MediaWiki extensions
