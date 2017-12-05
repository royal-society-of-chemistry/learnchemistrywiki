<?php
# KetcherDrawer extension
 
# Usage:
# <KetcherDrawer/>
 
# To install it put this file in the extensions directory 
# To activate the extension, include it from your LocalSettings.php
# with: require("extensions/KetcherGenerateInChIButton.php");
 
$wgExtensionCredits['other'][] = array(
        'name' => 'KetcherGenerateInChIButton',
        'author' => 'Aileen Day',
        'version' => '1.0.1',
		'url'  => 'http://epsweb.rsc.org/iwiki/index.php?title=KetcherDrawer_Mediawiki_extension',
        'description' => 'Adds Submit button to page with Ketcher Drawing frame to generate the InChI of the structure drawn. Requires KetcherDrawer extension to be in the same page for it to work.'
);
 
$wgExtensionFunctions[] = "wfKetcherGenerateInChIButton";

function wfKetcherGenerateInChIButton() {
global $wgParser;
	global $wgHooks;
# registers the <KetcherSearchButton> extension with the WikiText parser
    $wgParser->setHook( "KetcherGenerateInChIButton", "addGenerateInChIButtonToPage" );
}
 
# The callback function for converting the input text to HTML output
function addGenerateInChIButtonToPage( $input, $argv, &$parser ) {
	#output will be added to page body and #script will be added to page head
    global $wgScriptPath;
	$output = '<div id=\'buttondiv\'>
<form name="SearchForm" method="post" class="ajaxify">
<input type="submit" value="Generate InChI"/>
<label id="SearchFormLabel"></label>
</form>
</div>
<div id=\'outputdiv\'>
</div>
';
  $processstructuredirectory = $wgScriptPath."/extensions/KetcherDrawer/processstructure/";
  $script = '<script type="text/javascript" src="'.$processstructuredirectory.'sarissa.js"></script>
  <script type="text/javascript" src="'.$processstructuredirectory.'KetcherFunctions.js"></script>
  <script type="text/javascript" src="'.$processstructuredirectory.'KetcherSearchButtonFunctions.js"></script>
<script type="text/javascript">
   addEvent(window, \'load\', init, false);
   
   function init() {
      if (!Sarissa || !document.getElementsByTagName) return;
         var formElements = document.getElementsByTagName(\'form\');
         for (var i = 0; i < formElements.length; i++) {
            if (formElements[i].className.match(/\bajaxify\b/)) {
               addEvent(formElements[i], \'submit\', submitClick, false);
            }
         }
     }	
	 
   function submitClick(e){
	  clearOutput("outputdiv");
      /* Cancel the submit event, and find out which form was submitted */
	  var molsubmitted = ketcher.getMolfile();
	  if (molsubmitted.indexOf("  0  0  0     0  0            999 V2000\nM  END")!=-1)
		outputWarning("outputdiv", "No structure has been drawn!");
      killEvent(e);
      var target = window.event ? window.event.srcElement : e ? e.target : null;
      if (!target) return;
      /* Check if this form is already in the process of being submitted. */
      /* If so, don\'t allow it to be submitted again. */
      if (target.ajaxInProgress) return;
      /* Set up the request */
      var xmlhttp = new XMLHttpRequest();   
      xmlhttp.open(\'POST\', \'extensions/KetcherDrawer/processstructure/runInChICode.php\', true);
      /* The callback function */
      xmlhttp.onreadystatechange = function() {
         if (xmlhttp.readyState == 4) {
            if(xmlhttp.status == 200) {
			   processInChICodeWarnings(xmlhttp.responseXML.getElementsByTagName(\'warning\'), "outputdiv");
			   var searchtermfirstChild = xmlhttp.responseXML.getElementsByTagName(\'inchi\')[0].firstChild;
			   if (searchtermfirstChild == null) {
			      outputWarning("outputdiv", "There was an error generating the InChI for this structure.");
               } else {
				  var searchterm = searchtermfirstChild.data;
				  if (searchterm != "")
				     outputText("outputdiv", "<KetcherQuizAnswerButton correctstdinchi=\""+searchterm+"\"/>");
			   }
			} else
               target.submit();
		 }
      }
      /* Send the POST request */
	  xmlhttp.setRequestHeader(\'Content-Type\',\'application/x-www-form-urlencoded\');
	  xmlhttp.send(\'mol=\'+molsubmitted);
	}
	
</script>
';
	$parser->mOutput->addHeadItem($script);
	return $output;
}

?>