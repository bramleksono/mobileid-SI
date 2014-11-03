<?php
//require_once('./tcpdf_min/tcpdf.php');

function getpdfpage($document) {
	$im = new Imagick();
	$im->pingImage($document);
	return $im->getNumberImages();
}

function pdftoimage($file,$page) {
	$filepart = pathinfo($file);
	$save_to = $filepart['filename'].".jpg";

	$im = new imagick($file[$page-1]);
	$im->thumbnailImage(800, 0);
	$im->setImageFormat('jpg');
	$im = $im->flattenImages();
	file_put_contents($save_to, $im);
}

function signpdfwithPortableSigner($user_id,$unsignedpdf,$passphrase) {
	$programpath = "./documents/portablesigner/PortableSigner.jar";
	
	$signedpdf = "signed.".$unsignedpdf;
	
	$filepath = "./documents/";
	$inputpath = $filepath.$unsignedpdf;
	$outputpath = $filepath.$signedpdf;
	
	$cert = $user_id.".pdfsign.pfx";
	$certpath = "./documents/cert/".$cert;
	
	$signcmd = "java -jar ".$programpath." -n -t ".$inputpath." -o ".$outputpath." -s ".$certpath." -p ".$passphrase;
	//echo $signcmd;
	
	exec($signcmd);
	
	if (file_exists($outputpath)) {
		return 1;
	} else return 0;
}

function createsignpage ($text,$signatureimagepath,$outputpath) {
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}
	
	// ---------------------------------------------------------
	
	// set font
	$pdf->SetFont('helvetica', '', 12);
	
	// add a page
	$pdf->AddPage();
	
	// print a line of text
	$pdf->writeHTML($text, true, 0, true, 0);
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// *** set signature appearance ***
	
	// create content for signature (image and/rm or text)
	$pdf->Image($signatureimagepath, 150, '', 30, '', 'JPG');
	
	$pdf->Output($outputpath, 'F');
	
	if (file_exists($outputpath)) {
		return 1;
	} else return 0;
}

function createsignedpdf($sourcepdfpath,$signedpdfpath,$outputpath) {
	$pdf = new PDFMerger;
	
	$pdf->addPDF($sourcepdfpath, 'all')
		->addPDF($signedpdfpath, '1')
		->merge('file', $outputpath);
		
	if (file_exists($outputpath)) {
		return 1;
	} else return 0;
}

?>