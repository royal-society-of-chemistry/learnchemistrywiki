# README #
The Learn Chemistry Wiki was a wiki (based on open-source MediaWiki software) of secondary school chemistry information (Substances, Experiments, Reactions and Quizzes) for teachers and students to view, interact with and contribute to. It was hosted by the [Royal Society of Chemistry](http://www.rsc.org/) at http://www.rsc.org/learn-chemistry/wiki/ between January 2012 until September 2017. It has since been shut down and most of its resources consolidated into the main [Learn Chemistry](http://www.rsc.org/learn-chemistry) site. 

The Learn Chemistry Wiki featured Mediawiki extensions were developed by the Royal Society of Chemistry to enhance the chemistry functionality of the Learn Chemistry Wiki. We are making them available here for anyone that they might be useful to, but would caution that we will not be maintaining it, which will become more of an issue as the MediaWiki and 3rd party software that they rely on move on (the current versions are already different from those that these extensions were developed with). 

The Royal Society of Chemistry MediaWiki extensions developed as part of the Learn Chemistry Wiki are:


- KetcherDrawer extension - add a [Ketcher](http://lifescience.opensource.epam.com/ketcher/) structure drawer to a page and accompanying extensions can use the drawn structure and the structure drawn in it can be used in conjunction with the following accompanying extensions:
2. KetcherSearchButton extension - adds a Search button to a page with a KetcherDrawer in it, and an empty div section which is populated with the output a wiki search for the InChIKey of the drawn molecule when the button is clicked
3. KetcherQuizAnswer extension - adds a "Submit Drawn Answer" button to a page with a KetcherDrawer in it and a div which is populated with feedback which results from a comparison of the InChI of the drawn structure with that of the correct structure (and some common wrong structures with warning messages specific to them)
4. KetcherGenerateInChIButton extension - adds a Generate InChI button to a page with a KetcherDrawer in it so that it is easier for a teacher to set up add the KetcherQuizAnswerButton to a page

There is more information about these extensions and how they work [here](http://www.sciencedirect.com/science/article/pii/B9781907568978500035). 

## Installation ##
1. Install MediaWiki - we were working with version 1.16.0, which was the latest stable version at the time
2. To install the KetcherDrawer extension:

 * copy the KetcherDrawer folder here into the extensions directory of your mediawiki installation on your server
 * separately [download](http://lifescience.opensource.epam.com/ketcher/) and copy the Ketcher code into the folder ```extensions\KetcherDrawer\ketcher``` (e.g. ```extensions\KetcherDrawer\ketcher\ketcher.js```). We used Ketcher 1.0 Beta4
 * separately [download](https://iupac.org/who-we-are/divisions/division-details/inchi/) and copy the InChI executable into the folder ```extensions\KetcherDrawer\processstructure\inchi```. We used version 1.03 of the InChI so the files in that directory were ```inchi-1.exe``` and ```winchi-1.exe```
 * separately [download](https://sourceforge.net/projects/sarissa/) and copy sarissa into ```extensions\KetcherDrawer\processstructure.sarissa.js```. We used version 0.9.9.5
 * set permissions for the user which the web application runs as to have permission to read, write and execute programs in ```extensions\KetcherDrawer\ketcher\processstructure```, otherwise the inchi code won't run
 * edit your MediaWiki LocalSettings.php file to add the following line to enable the extension: 

```require_once "extensions/Ketcher/KetcherDrawer.php";```
```require_once "extensions/Ketcher/KetcherSearchButton.php";```
```require_once "extensions/Ketcher/KetcherQuizAnswerButton.php";```
```require_once "extensions/Ketcher/KetcherGenerateInChIButton.php";```

See [MediaWiki Extension documentation](https://www.mediawiki.org/wiki/Extensions_FAQ) for more information about installing MediaWiki extensions
## What the extensions do and usage ##
###KetcherDrawer###
To incorporate a Ketcher drawing frame into a html page it was simply necessary to reference the javascript and css files which comprise the Ketcher code in the head section of the html of a wiki, add the Ketcher frame, table and buttons to the body of the html, and add an onload attribute to the page to the initialise the Ketcher frame. The only part of these steps which was not immediately straightforward for a MediaWiki extension to do to the webpage in which it was called, was the step of adding an onload attribute to the page which we solved by adding a javascript function to the html head which was called at the window’s onload event.

To use the extension when installed add the following to a MediaWiki page:
```<KetcherDrawer/>```

###KetcherSearchButton###
This adds a Search button to a page with a KetcherDrawer in it, and an empty div section (to be populated with the output of the search) to the body of the web page. When the button is clicked, it performs several actions:

* takes the MOL depiction of the molecule that has been drawn (retrieved via a call to the Ketcher javascript functions) and convert it into an InChIKey so that this can be searched on using the [IUPAC InChI code](https://iupac.org/who-we-are/divisions/division-details/inchi/ "IUPAC InChI code") 
* any warnings that are returned are displayed in the wiki page, for example if stereochemistry is undefined or any atom has an unusual valence
* this InChIKey is posted into a search of the wiki – this was done by using the MediaWiki API to silently retrieve the results of this search
* if one matching substance page was found then the page would redirect to view it
* if no match for the full InChIKey is found then a second search is submitted to the MediaWiki API to find if there are any matches for just the first half of the InChIKey. This roughly equates to broadening the search to find matches for the molecule’s skeleton
.* any results from this search are listed in the wiki page itself, with a warning that no exact match could be found for the molecule but that these are similar molecules

To use the extension when installed add the following to a MediaWiki page 
```<KetcherSearchButton/>```

###KetcherSearchButton###
Similary, this adds a Submit button to a page with a KetcherDrawer in it, and an empty div section (to be populated with the output when the drawn structure is compared with some specified InChIs of structures) to the body of the web page. The correct and incorrect InChIs with their warning messages are specified when invoking the extension's parameters).
 
To use the extension when installed add the following to a MediaWiki page:

* simplest usage e.g.: ```<KetcherQuizAnswerButton correctstdinchi="InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H"/>```
* adding common wrong structures: ```<KetcherQuizAnswerButton correctstdinchi="InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H" wrongstdinchi1="InChI=1S/C6H12/c1-2-4-6-5-3-1/h1-6H2" warningmessage1="You have drawn cyclohexane - try adding some double bonds" wrongstdinchi2="InChI=1S/C5H10/c1-2-4-5-3-1/h1-5H2" warningmessage2="There are not enough carbons in the ring that you have drawn - try again!" wrongstdinchi3="InChI=1S/C3H6/c1-2-3-1/h1-3H2" warningmessage3="You have lost half of your carbons somewhere - try again..."/>```
* it is also possible to add a correctmessage parameter e.g. by adding the text below to the wiki text of a page: ```<KetcherQuizAnswerButton correctstdinchi="InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H" correctmessage="Correct – the markovnikov reaction involves addition of an acid HX to an alkene" />``` which when filled in will show that message when a correct acnswer is submitted.
* It is also possible to add a "preloadstdinchi" parameter e.g. by adding the text below to the wiki text of a page: ```<KetcherQuizAnswerButton correctstdinchi="InChI=1S/C5H10/c1-2-4-5-3-1/h1-5H2" preloadstdinchi="InChI=1S/C3H6/c1-2-3-1/h1-3H2"/>```. When filled in will add a link which says "Load initial compund" under the "Submit Drawn Answer" button (if it can generate a mol from the InChI from ChemSpider). When you click it, it will load the mol that corresponds to this initial compound into the Ketcher frame (so that students can start with this structure and not from scratch)

###KetcherGenerateInChIButton###
This adds a Search button to a page with a KetcherDrawer in it, and an empty div section (to be populated with the InChI output of the drawn structure) to the body of the web page. generates the InChI of the structure shown so that it is easier for a teacher to set up add the KetcherQuizAnswerButton to a page and write their own questions. In terms of functionality it's pretty well the same as the first half of the KetcherSearchButton extension except it outputs the InChI instead of doing a search for the InChIKey.
