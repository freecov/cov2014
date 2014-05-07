<?php
function createPdf($htmlFile, $defaultDomain, $pdfFile, $pos = "",$footer = ""){

		// require the class
		require_once('HTML_ToPDF.php');
		//require_once('PDFEncryptor.php');

		// full path to the file to be converted (this time a webpage)
		// change this to your own domain
		#$htmlFile = $htmlfile; #'http://lh/info.php';
		#$defaultDomain = $domain;
		#$pdfFile = $pdffile; #'/var/www/tmp/test.pdf';
		// remove old one, just to make sure we are making it afresh
		@unlink($pdfFile);

		$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain, $pdfFile);
		$pdf->footers = array($pos => $footer);

		$pdf->setDebug(false);
		// set that we do not want to use the page's css
		$pdf->setUseCSS(true);

		// give it our own css, in this case it will make it so
		// the lines are double spaced
		/*
		$pdf->setAdditionalCSS('
		p, body, td {
			font-size: 10pt;
		}');
		*/
		// we want to underline links
		$pdf->setUnderlineLinks(false);
		// scale the page down slightly
		$pdf->setScaleFactor('1');
		// make the page black and light
		$pdf->setUseColor(false);
		// convert the file
		$result = $pdf->convert();

		// check if the result was an error
		if (PEAR::isError($result)) {
				die($result->getMessage());
		}
		else {
			#nothing to do
		}

}
?>