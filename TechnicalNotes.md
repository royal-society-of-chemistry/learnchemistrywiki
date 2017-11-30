# Technical Notes about the KetcherDrawer extensions #
## Overview of how KetcherDrawer extension works ##
This was an in-house extension written pretty well from scratch. To add a Ketcher drawing frame to an html page requires the addition of the following to various parts of the page:

* add many many css and js link and script references to the ```<head>``` section of the html (before the body)
* add table with all the various buttons and drawing space etc. to the ```<body>``` of the page and calls to the javascript functions to do something with the Ketcher output e.g. ketcher.getMol
* adding an onload attribute adding to the body element:
```<body onload="ketcher.init();ketcher.setMolecule(parent.document.getElementById('molContentField').value)">```

In the php file for the extension (extensions/KetcherDrawer/KetcherSearchButton.php) a hook is set up so that when the extension is loaded the main function for the code (addSearchButtonToPage) will be called e.g. with:

```$wgParser->setHook( "KetcherSearchButton", "addSearchButtonToPage" );```

The code for an extension hook (e.g. addSearchButtonToPage) is defined with parameters below ($argv are the input arguments for the extension):

```function addKetcherToPage( $input, $argv, &$parser)```

In this function we can output the above 3 things to a page which calls the KetcherDrawer extension in the addKetcherToPage function:

* adding things to the body can be done by simply writing these as a string to the $output variable which is returned by this function (note that $output is added to the contents of the wikipage rather than overwriting it):
```$wgParser->setHook( "KetcherDrawer", "addKetcherToPage" );```
* adding things to the head can be done by writing everything to be added to a $script variable, then adding the line below to the addSearchButtonToPage function:
```$parser->mOutput->addHeadItem($script);```
* there will be a hook (#$wgHooks['OutputPageBodyAttributes'][]) to add things to the body onload attribute in the next version of mediawiki (1.17.0) but until that is activated it's pretty difficult to do that - there may be a way using skins, but an easier way for now was just to add the following to the head section which is apparently equivalent:
```<script type="text/javascript">```
```addLoadEvent(start);```
```function addLoadEvent(func) {```
```  var oldonload = window.onload;```
```  if (typeof window.onload != \'function\') {```
```    window.onload = func;```
```  } else {```
```    window.onload = function() {```
```      oldonload();```
```      func();```
```    }```
```  }```
```}```
```function start() ```
```{```
```ketcher.init();```
```ketcher.setMolecule(parent.document.getElementById(\'molContentField\').value);```
```}```
```</script>```
```
* note that we are not using the adding things to the BeforePageDisplay hook to add the scripts to the page headers e.g. by adding the hook ```$wgHooks['BeforePageDisplay'][]  = 'addKetcherToPageHeaders';```
then putting everything to be added to the header in the $script attribute of the addKetcherToPageHeaders function, and then putting $out->addScript( $script ); at the end of the function. This is because this method will add the specified $script to ALL pages on the wiki not just the one which you call the extension from. I did try a work around by defining a global variable which is set in the extension hook and then checking that this is set before adding anything to the page header but it only worked when the ?action=purge option was set on a page but not otherwise (the order of calling BeforePageDisplay and the extension hook was wrong normally).

Other tweaks to note:

* if you just put the structure drawer into a mediawiki page for some reason the Ketcher drawer goes on top of the page rather than a space being left for it in the page so that for example if you put a submit button in the page beneath it it will appear under the Ketcher drawer so you can't click on it. For this reason I added a blank white image (about the same size as the Ketcher frame) to the wiki in the usual manner and then the following to the $output: 
```<img alt="Blankketcherbackground.jpg" src="/education/wiki/images/b/b6/Blankketcherbackground.jpg" width="390" height="340" />``` 
and that sorts out the page
* Next to the "Powered by GGA" logo we've added link to a "Structure Drawer Tips" page which is just a stub for now
* Ketcher takes over key strokes, which means that if you look at a page with the structure drawer in it and try typing something into the Search box which involves certain letters e.g. a capital C it just activates shortcut keys in the Ketcher Drawer frame for various buttons... This means that for example you can’t type in the search term "Carbon" into a page with the structure drawer in it, which will be a problem when we load up the structure drawer on the main page of the wiki... For this reason I commented out the line in the extensions```\KetcherDrawer\ketcher\ui\ui.js``` where this behaviour is called:
```document.observe('keypress', ui.onKeyPress_Ketcher);```
* Commented out ketcher.css stylesheet styles that change things outside the Ketcher area (the body, and its first child) since these were affecting the mediawiki skin

## the processstructure directory ##
The ```extensions/KetcherDrawer/processstructure``` directory contains files and code which are shared between the different extensions which process the Ketcher mol file after it's been generated, or output results in a similar way.

Unfortunately, you can only get a mol file, or a smiles file directly from Ketcher (using ```ketcher.getMolfile()``` and ```ketcher.getSmiles()``` respectively) but neither of these are in the wiki substance pages so we can't pass them directly into a search. The InChIKey is in them though, and other uses of the structure drawer involve generation of the InChI so it was decided to generate the InChI code from the mol. I did experiment a bit with using ChemSpider webservices (an alternative would be to pass the mol into a MolToCSID, MolToInChI MolToInChiKey web service), but went for the InChI code option in the end because the extra output from the InChI code was thought to be useful to inform about errors in the structures drawn.

* the php file extensions/KetcherDrawer/processstructure/runInChICode.php runs the InChI code. Note that the InChI code runs on an input file and produces output files, so we need to save the mol file from Ketcher into an input file and then pull the results from the output files and then delete all of the files for the job. All of these files are saved in the extensions/KetcherDrawer/processstructure/inchifiles directory, and all files relating to the same job (the input (.mol), output (.out), log (.log), problem (.prb) files all have the same stem as each other, and it is a randomly generated guid (in case more than one person click the submit button at the same time. The output of the php file are an xml file with the following format
```<xmlreponse>
<inchikey>the inchikey goes in here</inchikey>
<inchi>the inchi goes in here</inchi>
<warning>the warning goes here</warning>
</xmlreponse>```
* There can be multiple warning lines - these correspond to any line from the .log file (generated by the InChI code) with this format: Warning (WARNING GRABBED FROM HERE) structure#? Multiple warnings in the log file in a single line which are semi-colon delimited e.g. Warning (Accepted unusual valence(s): C(5); Not chiral) structure #1. are split into separate warnings tags
* the inchicode is stored in the folder ```extensions\KetcherDrawer\processstructure\inchi```
* for the InChiCode to run correctly the user which the web application runs as needs to have permissions to read, write and execute programs in ```extensions\KetcherDrawer\ketcher\processstructure```, otherwise it won't work

Useful javascript libraries:

* sarissa.js is an external library which can be used to make a call to other webpages using a XMLHttpRequest (this library is used because this is a bit fiddly to do otherwise with all the different browsers) - for example to make a call to the php file ```processstructure/runInChICode.php```
* KetcherFunctions.js - contains functions:
 * ```clearOutput``` goes through all child elements in the div named in the input parameter and deletes them all (useful if some output has already been written previously but is not relevant anymore)
 * ```outputText``` - adds plain text (specified by input parameter warningstring) to the div specified by input parameter divname, and then a line break
 * ```outputWarning``` - as above, but outputs the text in red (in a span element)
  - A html link can be added to the resulting text by using the following syntax in the input string: ```"View |href:http://www.chemspider.com:a:ChemSpider|"``` so that the text within the ```|``` characters after the ```"href:"``` and before ```":a:"``` is taken as the href attribute of the ```<a>``` tag and the text marked up with the link follows ```":a:"```
  - Similarly, a html link which starts a javascript function when clicked (rather than loading a href) can be added to the resulting text by using the following syntax in the input string: ```"View |onclick:showrightmol:a:correct answer|"``` so that the text within the ```|``` characters after the ```"onclick:"``` and before ```":a:"``` is taken as the name of the function to set the ```"onclick"``` attribute of the the ```<a>``` tag. Note that it only allows a function without any input parameters to be called.
 * ```outputSearchResult``` - adds a href link (the address of which is specified by the input parameter linkname) to the div specified by input parameter divname, and then a line break. Note that it currently just works on internal links within the wiki.
 * processInChICodeWarnings - this takes the any warning tags from the xml returned from ```processstructure/runInChICode.php``` and outputs them as a warning in red. This function should be called from all the KetcherDrawer extensions to highlight big errors

The ```processstructure``` directory also contain any javascript code that is specific to a certain extension, but if separated out into a separate javascript file will reduce the size of the html page which contains the extensions (which are getting pretty big):

* KetcherSearchButtonFunctions.js
* KetcherQuizAnswerButtonFunctions.js

## KetcherSearchButton extension ##
The main file for this extension is ```extensions/KetcherDrawer/KetcherSearchButton.php```. We described above how to convert the Ketcher mol file into a standard inchi or inchikey using the InChI code and invoke it from a php page, but we needed to implement this to:

* be able to run an executable on the server from a loaded html page when you click on a submit button
* be able to refresh a part of the page without reloading the whole thing

The answer was to use [Ajax](http://www.yourhtmlsource.com/javascript/ajax.html "Ajax"). 

So this is what the ```extensions/KetcherDrawer/KetcherSearchButton.php``` extension used to add a submit button to the page, and when you click on it convert the mol file into an InChIKey and return search results of pages in the wiki which have that InChIKey in it (and warnings as appropriate):

* adds this to the body of the page (makes the search button)
```<div id='buttondiv'>```
```<form name="SearchForm" method="post" class="ajaxify">```
```<input type="submit" value="Submit"/>```
```<label id="SearchFormLabel"></label>```
```</form>```
```</div>```
* adds this to the body of the page (specifies a div in which any output (e.g. warning text, links to results etc.) can be inserted into:
```<div id=\'outputdiv\' class="floatleft">```
```</div>```
* adds a lot of javascript to the <head> element (not the body) which defines the following functions:
 * the ```init``` function does something with any forms in the page with class ajaxify to set it up (including the one which the submit button is in)
 * ```submitStructure``` is the main function which is called when the submit button is clicked. This does the following:
  * clears any previous output text from under the structure drawer frame using the ```clearOutput``` function of ```processstructure/KetcherFunctions.js``` to clear anything already in the outputdiv div.
  *  uses ```processstructure/sarissa.js``` to make a call to the php file ```processstructure/runInChICode.php``` using a ```XMLHttpRequest``` (this library is used because this is a bit fiddly to do otherwise with all the different browsers) which basically accepts the mol file as input, and returns a standard InChI, InChIKey and any warnings generated by the conversion in an xml file (see processstructure section for more details)
   *  the results from ```runInChICode.php``` are retrieved, and:
   *  the contents of the warning tags are output as a warning (using the function ```processInChICodeWarnings``` in the file ```processstructure/KetcherFunctions.js```) into the outputdiv div (under the submit button)
   *  if no InChIKey is returned then an error message is displayed under the Submit button (using the function ```outputWarning``` in the file ```processstructure/KetcherFunctions.js```)
   *  if an InChIKey is returned then it is passed into the ```processResults``` function
  *  the ```processResults``` function then retrieves xml from the mediawiki api which returns the search results for the input search term e.g. from a url of them form e.g. ```http://edu-wiki1.rsc-dev.org/education/wiki/api.php?action=query&format=xml&list=search&srprop=&srnamespace=108&srwhat=text&srredirects=1&srsearch="UHOVQNZJYSORNB-UHFFFAOYSA-N"```
  *  Note that that only searches the Substance namespace (with number 108), and needs ```srwhat``` to be set to text or it only searches the page titles, not their contents. The quotation marks are necessary otherwise it splits the InChIKey at the hyphens in it and returns search results which contains only one part or the other of the InChIKey.
  *  It returns a results page such as:
```<?xml version="1.0" ?>``` 
```<api>```
```  <query>```
```    <search>```
```      <p ns="108" title="Substance:Benzene" />```
```    </search>```
```  </query>```
```</api>```
  *  Unfortunately, for some reason this result won't parse as xml (which would be nicest) so ```xmlhttp2.responseXML``` won't return anything meaningful, so we have to use a slight hack and parse it as text (```xmlhttp2.responseText```), and extract the substance hits with a regular expression rather than xpath
  *  If it finds one search result from this full InChIKey search then it just diverts the current page to the subatance page returned by the search
  *  If it finds more than one search result from this full InChIKey (which it shouldn't really do) then it outputs a message (using the ```outputText``` function in ```processstructure/KetcherFunctions.js``` file) that there are multiple results and lists links to all of them under the Submit button (using the ```outputSearchResult``` function in ```processstructure/KetcherFunctions.js``` file).
  *  If no search terms are returned then it truncates the InChIKey at the first hyphen, and passes the first part of it (representing the molecule skeleton) into the processResults function again to obtain the api search results for just the molecule skeleton. If one or more substance pages are returned then links to them are listed under the submit button (using the ```outputSearchResult``` function in ```processstructure/KetcherFunctions.js``` file) with a note above them that explains that they are similar molecules rather than an exact match (using the ```outputText``` function in ```processstructure/KetcherFunctions.js``` file).
 
## KetcherQuizAnswerButton extension ##
The same basic approach of KetcherSearchButton was used here to convert the mol file from Ketcher to an InChI. New or different features include:

* This extension needs parameters to be specified by the person setting up the page:
 * mandatory parameters: correctstdinchi
 * optional parameters: correctmessage, wrongstdinchi1, warningmessage1 (although mandatory if wrongstdinchi1 is specified), wrongstdinchi2, warningmessage2 (although mandatory if wrongstdinchi2 is specified), wrongstdinchi3, warningmessage3 (although mandatory if wrongstdinchi3 is specified).
* These values can be retrieved easily from the main extension function, just by adding the ```$argv``` parameter to where the function is defined (although in the code itself there are various other checks done on the inputs and warnings output if for example it is not a standard InChI which is drawn:
```function addSubmitButtonToPage( $input, $argv) {```
```    if(isset($argv["correctstdinchi"]))```
```       $correctstdinchi = $argv["correctstdinchi"];```
```    etc.```
* This time it is the InChI that is used for comparison from the xml returned from processstructure/runInChICode.php rather than the InChIKey (but the same general mechanism is used for its generation and retrieval.
* It is possible that there will be multiple quiz answer buttons in the same page (but a maximum of one ketcherdrawer frame) so a guid is generated when the extension is run and this is appended to the outputdiv and submit button that are generated, and passed into the code called when the submit button is invoked so that only the appropriate outputdiv with a matching guid is updated.
* Another problem of the multiple quiz answer buttons per page is that some of the functions to add to the page head should only be added once per page (not once per time the extension is called). To do this the text to add to the page head is split into 2 variables: ```$scripttoaddonce``` and and ```$scriptoaddeverytime```. ```$scripttoaddonce``` is added to the page head with the code below, and adding the ```$tag``` flag to "true" when "addHeadItem" (as defined in ```wiki\includes\parser\ParserOutput.php```) is called means that the tag will only be included once in a given page:
```$parser->mOutput->addHeadItem($scripttoaddonce, "true");```
*  If warnings are generated by the InChI code these are output using the ```processInChICodeWarnings``` function in ```processstructure/KetcherFunctions.js```
*  To add the functionality where a link to load the correct answer in the structure drawer appears in the warning message when a wrong structure is drawn:
 *  when the extension is loaded it sets the variable ```$mol``` to the string retrieved from the ```http://www.ChemSpider.com/InChI.asmx?op=InChIToMol``` webservice (with line endings cleaned up and the value returned cached to minimise the hit on ChemSpider) using code similar to the WebService extension (which retrieves the page using Curl). This ChemSpider webservice had to be used because the InChICode (and OpenBabel) produce mol files from InChIs without any coordinates on its atoms. And we can't retrieve external urls via javascript (only things on the same server) so we had to use curl to retrive these mols at the start.
 *  For each time the extension is called (each Submit button in the page) a javascript function is added to the page with the name "showrightmol" appended with the matching guid for this time that the extension is called. When this function is called. the mol file defined by ```$mol``` is loaded into the KetcherDrawer frame to be displayed (using the ```ketcher.setMolecule``` function)
 *  when the warning text is added to the page for wrong answers, a ```<a>``` tag is added with an onclick attribute which calls the appropriate showrightmol javascript function (appended with the appropriate guid for the mol).
 *  Note that if you try to save something to the cache with a cachekey which is longer than 255 characters long it gives the error message below, so the cachekey was shortened a bit from the full InChIToMol webservice url (ommitting some of the unnecessary stuff off the front), and for long InChIs that do result in a cachekey that would be longer than 255 characters long, it doesn't try to save them to the cache:
 ```  Database error```
 ```From E-learn```
 ```Jump to: navigation, search```
 ```A database query syntax error has occurred. This may indicate a bug in the software. The last attempted database query was: ```
 ```(SQL query hidden)```
``` from within function "SqlBagOStuff::set". Database returned error "1062: Duplicate entry 'educationwiki:ws:http://www.chemspider.com/InChI.asmx/InChIToMol' for key 'PRIMARY' (localhost)".”```

## KetcherGenerateInChIButton ##
This is an extension to be added to a page (using the source ```<KetcherGenerateInChIButton/>```) with the KetcherDrawer extension also in it to generate the InChI of the structure shown so that it is easier for a teacher to set up add the KetcherQuizAnswerButton to a page and write their own questions. In terms of functionality it's pretty well the same as the first half of the KetcherSearchButton extension except it outputs the InChI instead of doing a search for the InChIKey.