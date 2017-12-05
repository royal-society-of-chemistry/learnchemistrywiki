<?php
# KetcherDrawer extension
 
# Usage:
# <KetcherDrawer/>
 
# To install it put this file in the extensions directory 
# To activate the extension, include it from your LocalSettings.php
# with: require("extensions/KetcherQuizAnswerButton.php");
 
$wgExtensionCredits['other'][] = array(
        'name' => 'KetcherQuizAnswerButton',
        'author' => 'Aileen Day',
        'version' => '1.0.1',
		'url'  => 'http://epsweb.rsc.org/iwiki/index.php?title=KetcherDrawer_Mediawiki_extension',
        'description' => 'Adds Submit button to page with Ketcher Drawing frame and compares the InChI of the drawn molecule with that specified by the correctstdinchi parameter. Requires KetcherDrawer extension to be in the same page for it to work.'
);
 
$wgExtensionFunctions[] = "wfKetcherQuizAnswerButton";

function wfKetcherQuizAnswerButton() {
   global $wgParser;
   global $wgHooks;
   # registers the <KetcherQuizAnswerButton> extension with the WikiText parser
   $wgParser->setHook( "KetcherQuizAnswerButton", "addSubmitButtonToPage" );
}
 
# The callback function for converting the input text to HTML output
function addSubmitButtonToPage( $input, $argv, &$parser = null) {
   #output will be added to page body and #script will be added to page head
   global $wgScriptPath;
   $thisguid = uniqid();
   $output = '<div id=\'buttondiv\'>
<form name="SubmitForm" method="post" class="ajaxify'.$thisguid.'">
<input type="submit" value="Submit Drawn Answer"/>
<label id="SubmitFormLabel"></label>
</form>
</div>
<div id=\'outputdiv'.$thisguid.'\'>
</div>
';
   $correctstdinchi = '';
   $correctmessage = '';
   $wrongstdinchi1 = '';
   $warningmessage1 = '';
   $wrongstdinchi2 = '';
   $warningmessage2 = '';
   $wrongstdinchi3 = '';
   $warningmessage3 = '';
   if(isset($argv["correctstdinchi"])) 
      {
      if(substr($argv["correctstdinchi"],0,9)=="InChI=1S/")		
         $correctstdinchi = $argv["correctstdinchi"];
      else
         $output=$output.'<div class="floatleft"><p><span style="color: red;">Error calling KetcherQuizAnswerButton extension - correctinchi parameter must contain valid standard inchi (begin with "InChI=1S/")</span></p></div>';
      } 
   else 
      { 
      $output=$output.'<div class="floatleft"><p><span style="color: red;">Error calling KetcherQuizAnswerButton extension - must specify correctinchi parameter</span></p></div>';
      $correctstdinchi = '';
      }
   if(isset($argv["correctmessage"]))
         {
         $correctmessage = $argv["correctmessage"];
         }
   if(isset($argv["wrongstdinchi1"])) 
      {
      if(isset($argv["warningmessage1"]))
         {
         $wrongstdinchi1 = $argv["wrongstdinchi1"];
         $warningmessage1 = $argv["warningmessage1"];
         }
      else
          $output=$output.'<div class="floatleft"><p><span style="color: red;">Error calling KetcherQuizAnswerButton extension - if specifying a wrongstdinchi1 parameter a corresponding warningmessage1 parameter should also be specified.</span></p></div>';
      }
   if(isset($argv["wrongstdinchi2"])) 
      {
      if(isset($argv["warningmessage2"]))
         {
         $wrongstdinchi2 = $argv["wrongstdinchi2"];
         $warningmessage2 = $argv["warningmessage2"];
         }
      else
         $output=$output.'<div class="floatleft"><p><span style="color: red;">Error calling KetcherQuizAnswerButton extension - if specifying a wrongstdinchi2 parameter a corresponding warningmessage2 parameter should also be specified.</span></p></div>';
      }
   if(isset($argv["wrongstdinchi3"])) 
      {
      if(isset($argv["warningmessage3"]))
         {
         $wrongstdinchi3 = $argv["wrongstdinchi3"];
         $warningmessage3 = $argv["warningmessage3"];
         }
      else
         $output=$output.'<div class="floatleft"><p><span style="color: red;">Error calling KetcherQuizAnswerButton extension - if specifying a wrongstdinchi3 parameter a corresponding warningmessage3 parameter should also be specified.</span></p></div>';
      }
	$mol=getInChIToMol("http://www.chemspider.com/InChI.asmx/InChIToMol?inchi=".urlencode($correctstdinchi));
   $processstructuredirectory = $wgScriptPath."/extensions/KetcherDrawer/processstructure/";
   $scripttoaddonce = '<script type="text/javascript" src="'.$processstructuredirectory.'sarissa.js"></script>
<script type="text/javascript" src="'.$processstructuredirectory.'KetcherFunctions.js"></script>
<script type="text/javascript" src="'.$processstructuredirectory.'KetcherQuizAnswerButtonFunctions.js"></script>
<script type="text/javascript">
	function submitClick(e, inputparameters){
	  submitid=inputparameters[0];
	  clearOutput("outputdiv"+submitid);
      /* Cancel the submit event, and find out which form was submitted */
	  var molsubmitted = ketcher.getMolfile();
	  if (molsubmitted.indexOf("  0  0  0     0  0            999 V2000\nM  END")!=-1)
		outputWarning("outputdiv"+submitid, "No structure has been drawn!");
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
			   processInChICodeWarnings(xmlhttp.responseXML.getElementsByTagName(\'warning\'), "outputdiv"+submitid);
			   var inchifirstChild = xmlhttp.responseXML.getElementsByTagName(\'inchi\')[0].firstChild;
			   if (inchifirstChild == null) {
			      outputWarning("outputdiv"+submitid, "There was an error generating the InChI for this structure.");
			   } else {
				  var inchi = inchifirstChild.data;
			      if (inchi != "")
				     doesInChIMatch("outputdiv"+submitid, inchi, inputparameters[1], inputparameters[2], inputparameters[3], inputparameters[4], inputparameters[5], inputparameters[6], inputparameters[7]);
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
   $parser->mOutput->addHeadItem($scripttoaddonce, "true");
   $scriptoaddeverytime = '<script type="text/javascript">
   addEvent(window, \'load\', init'.$thisguid.', false);

   function init'.$thisguid.'() {
      if (!Sarissa || !document.getElementsByTagName) return;
         var formElements = document.getElementsByTagName(\'form\');
         for (var i = 0; i < formElements.length; i++) {
            if (formElements[i].className.match(/\bajaxify'.$thisguid.'\b/)) {
				var inputparameters=new Array("'.$thisguid.'", "'.$correctstdinchi.'", "'.$correctmessage.'", "'.$wrongstdinchi1.'", "'.$warningmessage1.'", "'.$wrongstdinchi2.'", "'.$warningmessage2.'", "'.$wrongstdinchi3.'", "'.$warningmessage3.'");
				addEvent(formElements[i], \'submit\', submitClick, false, inputparameters);
            }
         }
     }
	 function showrightmol'.$thisguid.'() {';
	 
	if ($mol=='') {
		$scriptoaddeverytime = $scriptoaddeverytime.'
		alert(\'Currently unable to load correct structure into drawing frame - ChemSpider may be unavailable. Please try again later\');';
	} else {
		$scriptoaddeverytime = $scriptoaddeverytime.'
		ketcher.setMolecule(\''.$mol.'\');';
	}
	$scriptoaddeverytime = $scriptoaddeverytime.'
	}
</script>
';	 

   $parser->mOutput->addHeadItem($scriptoaddeverytime);
   return $output;
}

function getInChIToMol($url) {
	global $parserMemc;
	$contents = '';
	# uses shortened form of url to store (cache key can't be longer than 255 characters)
	$cacheKey = wfMemcKey('ws',str_replace('http://www.chemspider.com/InChI.asmx/InChIToMol?inchi=', 'InChIToMol', $url));
	$cache = $parserMemc;
	$contents = $cache->get($cacheKey);
	if ($contents == '') {
	    $ch = getCurlConnection_KetcherQuizAnswer($url);
        $contents = curl_exec ( $ch );
        $errno = curl_errno($ch);
        curl_close($ch);
		if ($errno == 0 && $contents!==false) {
			$serviceunavailablepos = strpos($contents, 'ChemSpider - Service Unavailable');
			$serviceinterruptionpos = strpos($contents, 'Service Interruption');
			if (($serviceunavailablepos === false)&&($serviceinterruptionpos === false)) {
				$pos = strrpos($contents, "http://www.chemspider.com");
				if ($pos === false)
					return '';
				else {
					$contents = substr($contents, strpos($contents,"http://www.chemspider.com"));
					$contents = substr($contents, strpos($contents,">")+ 1);
					$pos = strrpos($contents, "<");
					if ($pos === false)
						return '';
					else
						$contents = substr($contents, 0, strpos($contents,"<"));	
					$contents = str_replace("\n"," \\n",$contents);
				}
				# doesn't use cache if url is longer than 255 characters long
				if (strlen($cacheKey)<=255) {
					$cache->delete($cacheKey);
					$cache->set( $cacheKey, $contents, option_cache(null));
				}
			} else {
				return '';
			}
			
		} else
			return '';
	}
	return $contents ;
}

// Return a CURL connection object with all of the appropriate settings
function getCurlConnection_KetcherQuizAnswer($url) {
        global $wgHTTPProxy;
 
        $ch = curl_init($url);
 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);
        // Handle https connections
        if (stripos($url, 'HTTPS:') !== false) {
               curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
  
        // Add proxy information, if needed
        if ($wgHTTPProxy !== false && isProxiedURL_KetcherQuizAnswer($url)) {
               curl_setopt($ch, CURLOPT_PROXY, $wgHTTPProxy);
        }
 
        return $ch;
}
 
//Determine whether or not the given URL should go through the proxy, based on the array of 
//proxy exceptions.  Might want to bake this into Mediawiki itself later.
function isProxiedURL_KetcherQuizAnswer($url) {
        global $wgHTTPProxyExceptions;
 
        if (is_array($wgHTTPProxyExceptions)) {
               foreach($wgHTTPProxyExceptions as $ex) {
                       if (preg_match($ex, $url)) {
                               // URL matches a proxy exception, so it's not proxied
                               return false;
                       }
               }
        }
 
        // URL didn't match any of the proxy exceptions, so it is proxied
        return true;
}

?>