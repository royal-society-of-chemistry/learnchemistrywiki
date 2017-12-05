<?php
# KetcherDrawer extension
 
# Usage:
# <KetcherDrawer/>
 
# To install it put this file in the extensions directory 
# To activate the extension, include it from your LocalSettings.php
# with: require("extensions/KetcherDrawer.php");
 
$wgExtensionCredits['other'][] = array(
        'name' => 'KetcherDrawer',
        'author' => 'Aileen Day',
        'version' => '1.0.1',
		'url'  => 'http://epsweb.rsc.org/iwiki/index.php?title=KetcherDrawer_Mediawiki_extension',
        'description' => 'Add Ketcher Drawing frame to a page. Requires KetcherSearchButton or KetcherQuizAnswerButton extensions to be added to the same page to do anything with the result.'
);

$wgExtensionFunctions[] = "wfKetcherDrawer";
 
function wfKetcherDrawer() {
global $wgParser;
	global $wgHooks;
# registers the <KetcherDrawer> extension with the WikiText parser
    $wgParser->setHook( "KetcherDrawer", "addKetcherToPage" );
	#$wgHooks['OutputPageBodyAttributes'][] = 'addKetcherOnload';
}

#function addKetcherOnload( $out, $sk, &$bodyAttrs ) {
# commented out for now until the hook is activated in Mediawiki v 1.17.0 
#		$bodyAttrs['onload'] .= "ketcher.init(); ketcher.setMolecule(parent.document.getElementById('molContentField').value)";
#		$this->getTitle()->getNamespace();
#		$sk->getSkinName();
#		$sk->addToBodyAttributes( $this, $bodyAttrs ); // Allow skins to add body attributes they need
#		wfRunHooks( 'OutputPageBodyAttributes', array( $this, $sk, &$bodyAttrs ) );
#		$ret .= Html::openElement( 'body', $bodyAttrs ) . "\n";
#		return $ret;
#	}
 
# The callback function for converting the input text to HTML output
function addKetcherToPage( $input, $argv, &$parser) {
	#output will be added to page body and #script will be added to page head
    global $wgScriptPath;
	# code to remove more menu from RSC header for these pages (since it doesn't work)
	 $rscscript='<style>#masthead-morebutton{display: none !important; }</style>';
	# end of RSC header code
    $ketcherdirectory = $wgScriptPath."/extensions/KetcherDrawer/ketcher/";
	$output = '<div>
<table id="ketcher_window">
<tr align="center" id="main_toolbar">
<td><img class="sideButton" id="select_simple" src="'.$ketcherdirectory.'png/arrow.png" width="16px" height="16px" alt="" title="Element Selection (Esc)" /></td>
<td class="toolDelimiter"></td>
<td class="toolButton" id="new"><img src="'.$ketcherdirectory.'png/document-new.png" width="16px" height="16px" alt="New" title="New" /></td>
<td class="toolButton" id="open"><img src="'.$ketcherdirectory.'png/document-open.png" width="16px" height="16px" alt="Open..." title="Open..." /></td>
<td class="toolButton" id="save"><img src="'.$ketcherdirectory.'png/document-save-as.png" width="16px" height="16px" alt="Save As..." title="Save As..." /></td>
<td class="toolDelimiter"></td>
<td class="toolButton buttonDisabled" id="undo"><img src="'.$ketcherdirectory.'png/edit-undo.png" width="16px" height="16px" alt="Undo" title="Undo" /></td>
<td class="toolButton buttonDisabled" id="redo"><img src="'.$ketcherdirectory.'png/edit-redo.png" width="16px" height="16px" alt="Redo" title="Redo" /></td>
<td class="toolButton buttonDisabled" id="copy"><img src="'.$ketcherdirectory.'png/edit-copy.png" width="16px" height="16px" alt="Copy" title="Copy" /></td>
<td class="toolButton buttonDisabled" id="cut"><img src="'.$ketcherdirectory.'png/edit-cut.png" width="16px" height="16px" alt="Cut" title="Cut" /></td>
<td class="toolButton buttonDisabled" id="paste"><img src="'.$ketcherdirectory.'png/edit-paste.png" width="16px" height="16px" alt="Paste" title="Paste" /></td>
<td class="toolDelimiter"></td>
<td class="toolButton" id="zoom_in"><img src="'.$ketcherdirectory.'png/view-zoom-in.png" width="16px" height="16px" alt="Zoom In (+)" title="Zoom In (+)" /></td>
<td class="toolButton" id="zoom_out"><img src="'.$ketcherdirectory.'png/view-zoom-out.png" width="16px" height="16px" alt="Zoom Out (-)" title="Zoom Out (-)" /></td>
<td class="toolDelimiter"></td>
<td class="toolButton serverRequired" id="clean_up"><img src="'.$ketcherdirectory.'png/layout.png" width="16px" height="16px" alt="Clean Up" title="Clean Up" /></td>
<!--<td style="width:100%"></td>-->
<td colspan=2><a href="http://www.ggasoftware.com/" target="_blank"><img width="32px" height="32px" src="'.$ketcherdirectory.'png/logo.png" style="border:0px" alt="GGA Software Services" title="GGA Software Services" /></a></td>
</tr>
<tr align="center" style="height:17px">
<!--td><object class="sideButton" type="image/svg+xml" width="16px" height="16px" data="svg/anybond.svg"></object></td-->
<td><img class="sideButton" id="select_erase" src="'.$ketcherdirectory.'png/edit-clear.png" width="16px" height="16px" alt="Erase" title="Erase" /></td>
<td 
colspan="16" rowspan="14"><div id="client_area"></div></td>
<td></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_any" src="'.$ketcherdirectory.'png/anybond.png" width="16px" height="16px" alt="Any Bond (0)" title="Any Bond (0)" /></td>
<td><img class="sideButton" id="atom_any" src="'.$ketcherdirectory.'png/anyatom.png" width="16px" height="16px" align=right alt="Any Atom (A)" title="Any Atom (A)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_single" src="'.$ketcherdirectory.'png/single.png" width="16px" height="16px" alt="Single Bond (1)" title="Single Bond (1)" /></td>
<td><img class="sideButton" id="atom_h" src="'.$ketcherdirectory.'png/h.png" width="16px" height="16px" align=right alt="H Atom (H)" title="H Atom (H)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_up" src="'.$ketcherdirectory.'png/up.png" width="16px" height="16px" alt="Single Up Bond (1)" title="Single Up Bond (1)" /></td>
<td><img class="sideButton" id="atom_c" src="'.$ketcherdirectory.'png/c.png" width="16px" height="16px" align=right alt="C Atom (C)" title="C Atom (C)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_down" src="'.$ketcherdirectory.'png/down.png" width="16px" height="16px" alt="Single Down Bond (1)" title="Single Down Bond (1)" /></td>
<td><img class="sideButton" id="atom_n" src="'.$ketcherdirectory.'png/n.png" width="16px" height="16px" align=right alt="N Atom (N)" title="N Atom (N)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_double" src="'.$ketcherdirectory.'png/double.png" width="16px" height="16px" alt="Double Bond (2)" title="Double Bond (2)" /></td>
<td><img class="sideButton" id="atom_o" src="'.$ketcherdirectory.'png/o.png" width="16px" height="16px" align=right alt="O Atom (O)" title="O Atom (O)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_triple" src="'.$ketcherdirectory.'png/triple.png" width="16px" height="16px" alt="Triple Bond (3)" title="Triple Bond (3)" /></td>
<td><img class="sideButton" id="atom_s" src="'.$ketcherdirectory.'png/s.png" width="16px" height="16px" align=right alt="S Atom (S)" title="S Atom (S)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="bond_aromatic" src="'.$ketcherdirectory.'png/aromatic.png" width="16px" height="16px" alt="Aromatic Bond (4)" title="Aromatic Bond (4)" /></td>
<td><img class="sideButton" id="atom_f" src="'.$ketcherdirectory.'png/f.png" width="16px" height="16px" align=right alt="F Atom (F)" title="F Atom (F)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="pattern_six1" src="'.$ketcherdirectory.'png/hexa1.png" width="16px" height="16px" alt="Benzene (R)" title="Benzene (R)" /></td>
<td><img class="sideButton" id="atom_p" src="'.$ketcherdirectory.'png/p.png" width="16px" height="16px" align=right alt="P Atom (P)" title="P Atom (P)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="pattern_six2" src="'.$ketcherdirectory.'png/hexa2.png" width="16px" height="16px" alt="Cyclohexane (R)" title="Cyclohexane (R)" /></td>
<td><img class="sideButton" id="atom_cl" src="'.$ketcherdirectory.'png/cl.png" width="16px" height="16px" align=right alt="Cl Atom (Shift+C)" title="Cl Atom (Shift+C)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="pattern_sixa" src="'.$ketcherdirectory.'png/hexaa.png" width="16px" height="16px" alt="Aromatic ring (R)" title="Aromatic ring (R)" /></td>
<td><img class="sideButton" id="atom_br" src="'.$ketcherdirectory.'png/br.png" width="16px" height="16px" align=right alt="Br Atom (Shift+B)" title="Br Atom (Shift+B)" /></td>
</tr>
<tr align="center" style="height:17px">
<td><img class="sideButton" id="pattern_five" src="'.$ketcherdirectory.'png/penta.png" width="16px" height="16px" alt="Cyclopentane (R)" title="Cyclopentane (R)" /></td>
<td><img class="sideButton" id="atom_i" src="'.$ketcherdirectory.'png/i.png" width="16px" height="16px" align=right alt="I Atom (I)" title="I Atom (I)" /></td>
</tr>
<!--tr align="center" style="height:17px">
<td><img class="sideButton buttonDisabled" id="pattern_naphthalene" src="'.$ketcherdirectory.'png/naphthalene.png" width="24px" height="16px" alt="Naphthalene" title="Naphthalene" /></td>
<td></td>
</tr-->
<tr>
<td style="height:100%"></td>
</tr>
<tr>
<td colspan="19"></td>
</tr>
<tr id="console_row" style="display:none;">
<td colspan="19">
<div id="console"></div>
</td>
</tr>
</table>  
<input id="input_label" type="text" maxlength="4" size="4" style="display:none;" />   
<div id="window_cover" style="display:none;"></div>
<div class="dialogWindow fileDialog" id="open_file" style="display:none;">
<div style="width:100%">
<div>
Open File
</div>
<div style="height:0.5em"></div>
<div class="serverRequired" style="font-size:small">
<input type="radio" id="radio_open_from_input" name="input_source" checked>Input</input>
<input type="radio" id="radio_open_from_file" name="input_source">File</input>
</div>
<div class="serverRequired" id="open_from_file">
<form id="upload_mol" style="margin-top:4px" action="open" enctype="multipart/form-data" target="buffer_frame" method="post">
<input type="file" name="filedata" id="molfile_path" />
<div style="margin-top:0.5em;text-align:center">
<input id="upload_cancel" type="button" value="Cancel" />
<input type="submit" value="OK" />
</div>
</form>
</div>
<div style="margin:4px;" id="open_from_input">
<textarea class="chemicalText" id="input_mol"></textarea>
<div style="margin-top:0.5em;text-align:center">
<input id="read_cancel" type="button" value="Cancel" />
<input id="read_ok" type="submit" value="OK" />
</div>
</div>
</div>
</div>
<div class="dialogWindow fileDialog" id="save_file" style="display:none;">
<div style="width:100%">
<div>
Save File
</div>
<div style="height:0.5em"></div>
<div>
<label>Format:</label>
<select id="file_format">Format:
<option value="mol">MDL/Symyx Molfile</option>
<option value="smi">Daylight SMILES</option>
<!--option value="png">Portable Network Graphics PNG</option>
<option value="svg">Scalable Vector Graphics SVG</option-->
</select>
</div>
<div style="margin:4px;">
<textarea class="chemicalText" id="output_mol" readonly></textarea>
<form  id="download_mol" style="margin-top:0.5em;text-align:center" action="save" enctype="multipart/form-data" target="_self" method="post">
<input type="hidden" id="mol_data" name="filedata" />
<input type="submit" class="serverRequired" value="Save..." />
<input id="save_ok" type="button" value="Close" />
</form>
</div>
</div>
</div>
<div class="dialogWindow propDialog" id="atom_properties" style="display:none;">
<div style="width:100%">
<div>
Atom Properties
</div>
<div style="height:0.5em"></div>
<table style="text-align:left">
<tr>
<td>
<label>Label:</label>
</td>
<td>
<input id="atom_label" type="text" maxlength="2" size="3" />
</td>
<td>
<label>Number:</label>
</td>
<td>
<label id="atom_number"></label>
</td>
</tr>
<tr>
<td>
<label>Charge:</label>
</td>
<td>
<select id="atom_charge">
<option value="3">+3</option>
<option value="2">+2</option>
<option value="1">+1</option>
<option value="0">0</option>
<option value="-1">-1</option>
<option value="-2">-2</option>
<option value="-3">-3</option>
</select>
</td>
<td>
<label>Isotope:</label>
</td>
<td>
<input id="atom_isotope" type="text" maxlength="3" size="3" />
</td>
</tr>
<tr>
<td>
<label>Valence:</label>
</td>
<td>
<input id="atom_valence" type="text" maxlength="1" size="3" />
</td>
<td>
<label>Radical:</label>
</td>
<td>
<select id="atom_radical">
<option value="0"></option>
<option value="1">Singlet</option>
<option value="2">Doublet</option>
<option value="3">Triplet</option>
</select>
</td>
</tr>
</table>
<div style="margin-top:0.5em"><input id="atom_prop_cancel" type="button" value="Cancel" /><input id="atom_prop_ok"type="button" value="OK" /></div></div></div><iframe name="buffer_frame" id="buffer_frame" src="about:blank" style="display:none"></iframe></div>
<div><img alt="Blankketcherbackground.jpg" src="/learn-chemistry/wiki/images/b/b6/Blankketcherbackground.jpg" width="390" height="340" /></div>
<div>
<table>
<tr>
<td style="width:95px">
</td>
<td valign=top>
<a style="font-size:10pt;font-weight: bold" href="/learn-chemistry/wiki/Help:Structure_Drawer_Tips" title="Structure Drawer Tips" target="_new">Structure Drawer Tips</a>
<br/>
<a style="font-size:10pt;font-weight: bold" href="/learn-chemistry/wiki/GGA_Conditions_of_Use" title="GGA Conditions of Use" target="_new">GGA Conditions of Use</a>
</td>
<td>
<a href="http://www.ggasoftware.com/" class="image" target="_new"><img alt="Powered By GGA" src="/learn-chemistry/wiki/images/7/77/PoweredByGGA.png" width="112" height="55" /></a>
</td>
</tr>
</table>
</div>
';

  $ketcherdirectory = $wgScriptPath."/extensions/KetcherDrawer/ketcher/";
  $script = $rscscript.'<link rel="stylesheet" type="text/css" href="'.$ketcherdirectory.'ketcher.css" />
<script type="text/javascript" src="'.$ketcherdirectory.'prototype-min.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'raphael-min.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/common.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/vec2.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/map.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/pool.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/element.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/molecule.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/molfile.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/dfs.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/cis_trans.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/stereocenters.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'chem/smiles.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/events.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/visel.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/moldata.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/moldata_valence.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/drawing.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'rnd/render.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'ui/log.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'ui/ui.js"></script>
<script type="text/javascript" src="'.$ketcherdirectory.'ketcher.js"></script>
<script type="text/javascript">
addLoadEvent(start);
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != \'function\') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}
function start() 
{

ketcher.init();
//ketcher.setMolecule(parent.document.getElementById(\'molContentField\').value);

}
</script>
';
	$parser->mOutput->addHeadItem($script);
	return $output;
}

?>