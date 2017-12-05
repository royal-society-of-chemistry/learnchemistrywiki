<?php
header('Content-type: text/xml');
$mol = $_POST['mol'];
$filestem="inchifiles\\".uniqid();
$inputFileName = $filestem.".mol";
$inputFile = fopen($inputFileName, 'w');
fwrite($inputFile, $mol);
fclose($inputFile);
$command='inchi\\inchi-1.exe '.$filestem.'.mol '.$filestem.'.out '.$filestem.'.log '.$filestem.'.prb /Key';
exec($command);
$outfile = file_get_contents($filestem.'.out');
$lines=explode("\r", $outfile);
$count = count($lines);
$inchikey="";
$inchi="";
for ($i = 0; $i < $count; $i++) {
  $inchikeypos = strpos($lines[$i], "InChIKey=");
  if ($inchikeypos !== false) {
		$inchikey=substr(ltrim(rtrim($lines[$i])),9);
  }
  $inchipos = strpos($lines[$i], "InChI=");
  if ($inchipos !== false) {
		$inchi=ltrim(rtrim($lines[$i]));
  }
}
$logfile = file_get_contents($filestem.'.log');
$loglines=explode("\r", $logfile);
$warningwithwarningtags="";
for ($j = 0; $j < count($loglines); $j++) {
  $warningpos = strpos($loglines[$j], "Warning (");
  if ($warningpos !== false) {
		$warnings="";
		$warnings=substr($loglines[$j], strrpos($loglines[$j], "Warning (")+strlen("Warning ("));
		$warnings=substr($warnings, 0, strrpos($warnings, ") structure #"));
		$warningarray=explode(";", $warnings);
		for ($k = 0; $k < count($warningarray); $k++) {
			$thiswarning=ltrim(rtrim($warningarray[$k]));
			if ($thiswarning!="") {
				$NInInchipos = strpos($inchi, "N");
				$OInInchipos = strpos($inchi, "O");
				if (($NInInchipos !== false)&&($OInInchipos !== false)){
					# does not write out warning "Charges were rearranged" for structures that contain N and O molecules (shouldn't write these out for NO2 groups)
					$chargewarningpos = strpos($thiswarning, "Charges were rearranged");
					if ($chargewarningpos === false)
						$warningwithwarningtags=$warningwithwarningtags."<warning>".$thiswarning."</warning>";
				} else
					$warningwithwarningtags=$warningwithwarningtags."<warning>".$thiswarning."</warning>";
			}
		}
  }
}
unlink($inputFileName);
unlink($filestem.".out");
unlink($filestem.".log");
unlink($filestem.".prb");
echo("<xmlreponse>");
//echo("<mol>".$mol."</mol>");
echo("<inchikey>".$inchikey."</inchikey>");
echo("<inchi>".$inchi."</inchi>");
echo("$warningwithwarningtags");
echo("</xmlreponse>");
?>