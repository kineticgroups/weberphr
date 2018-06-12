<?php
/*	$Id: PDFQuotationPageHeader.inc 7551 2016-06-06 15:28:31Z rchacon $*/
/* pdf-php by R&OS code to set up a new sales order page */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/

// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
/*$pdf->addJpegFromFile($_SESSION['LogoFile'], 301, 520, 0, 60);// Old function. See ~/includes/class.pdf.php.*/
$pdf->Image(
	$_SESSION['LogoFile'],// Name of the file containing the image.
	35,// Abscissa from left border to the upper-left corner (LTR).
	$Page_Height -(520) -(40),// Ordinate from top border to the upper-left corner (LTR).
	0,// Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
	35,// Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
	''// Image format. If not specified, the type is inferred from the file extension.
);// Public function Image() in ~/includes/tcpdf/tcpdf.php.

$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x+662, $Page_Height - $FormDesign->HeaderRectangle2->y-61,$FormDesign->HeaderRectangle2->width+150, $FormDesign->HeaderRectangle2->height+20, $FormDesign->HeaderRectangle2->radius+1,$FormDesign->HeaderRectangle2->radius+1,'1111','DF',"",  array(220, 220, 220));
$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x+40, $Page_Height - $FormDesign->HeaderRectangle2->y-181,$FormDesign->HeaderRectangle2->width+772, $FormDesign->HeaderRectangle2->height+23, $FormDesign->HeaderRectangle2->radius+1,$FormDesign->HeaderRectangle2->radius+1,'1111','DF',"",  array(220, 220, 220));

// Prints 'Quotation' title:
$pdf->addTextWrap(315, $Page_Height-$Top_Margin-45, $Page_Width, 12, _('Quotation'), 'center');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-65, 200, $FontSize-2, _('Number'). ': '.$_GET['QuotationNo'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-80, 200, $FontSize-2, _('Your Ref'). ': '.$myrow['customerref'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-95, 200, $FontSize-2, _('Date'). ': '.ConvertSQLDate($myrow['quotedate']), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-10, 200, $FontSize-4, _('Page').': '.$PageNumber, 'right');

// Prints company info:
$XPos = $Page_Width/2+$Left_Margin;
$YPos = 512;
$FontSize = 10;
$pdf->addText($XPos-420, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']);
$YPos -= $FontSize;
$FontSize = 10;
$pdf->addText($XPos-420, $YPos, $FontSize, $_SESSION['CompanyRecord']['regoffice1'] . ', ' . $_SESSION['CompanyRecord']['regoffice2']);
//$pdf->addText($XPos-420, $YPos-$FontSize*1, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($XPos-420, $YPos-10, $FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4']);
//$pdf->addText($XPos-420, $YPos-$FontSize*3, $FontSize, $_SESSION['CompanyRecord']['regoffice4']);
$pdf->addText($XPos-420, $YPos-20, $FontSize, $_SESSION['CompanyRecord']['regoffice5'] .
	', ' . $_SESSION['CompanyRecord']['regoffice6']);
$pdf->addText($XPos-420, $YPos-30, $FontSize,  _('Tel:') . ': ' . $_SESSION['CompanyRecord']['telephone']);
	//' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos-420, $YPos-40, $FontSize, $_SESSION['CompanyRecord']['email']);

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 566;
$FontSize=10;
$myrow = array_map(html_entity_decode, $myrow);
$line_height = 15;
$pdf->addText($XPos+450, $YPos-55, $FontSize+1, _('Delivery To').':' );
$pdf->addText($XPos+450, $YPos-75, $FontSize, $myrow['deliverto']);
$pdf->addText($XPos+450, $YPos-90, $FontSize, $myrow['deladd1']);
$pdf->addText($XPos+450, $YPos-105, $FontSize, $myrow['deladd2']);
$pdf->addText($XPos+450, $YPos-120, $FontSize, $myrow['deladd3'] . ', ' . $myrow['deladd4'] . ', ' . $myrow['deladd5']);

// Prints 'Quotation For' info:
$YPos -= 80;
$line_height = 15;
$pdf->addText($XPos+275, $YPos+25, $FontSize+1, _('Quotation For').':');
$pdf->addText($XPos+275, $YPos+5, $FontSize, $myrow['name']);
$pdf->addText($XPos+275, $YPos-10, $FontSize, $myrow['address1']);
$pdf->addText($XPos+275, $YPos-25, $FontSize, $myrow['address2']);
$pdf->addText($XPos+275, $YPos-40, $FontSize, $myrow['address3'] . ', ' . $myrow['address4'] . ', ' . $myrow['address5']);



// Draws a box with round corners around 'Delivery To' info:
// $XPos= 50;
// $YPos += 12;
// $pdf->RoundRectangle(
// 	$XPos+610,// RoundRectangle $XPos.
// 	$YPos+50,// RoundRectangle $YPos.
// 	150,// RoundRectangle $Width.
// 	25,// RoundRectangle $Height.
// 	0.5,// RoundRectangle $RadiusX.
// 	0.5);// RoundRectangle $RadiusY.

// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 82;
$pdf->RoundRectangle(
	$XPos+266,// RoundRectangle $XPos.
	$YPos+110,// RoundRectangle $YPos.
	500,// RoundRectangle $Width.
	100,// RoundRectangle $Height.
	1,// RoundRectangle $RadiusX.
	1);// RoundRectangle $RadiusY.

$FontSize=10;

// Prints the currency name:
require_once('includes/CurrenciesArray.php');// To get the currency name from the currency code.
$pdf->addText($Page_Width/2+$Left_Margin-420, $YPos+25, $FontSize,
	_('All amounts stated in') . ' ' . $myrow['currcode'] . ' - ' . $CurrencyName[$myrow['currcode']]);

// Prints table header:
$XPos = 40;
$YPos -= 37;
$LeftOvers = $pdf->addTextWrap($Left_Margin+05, $YPos+32,103, $FontSize, _('Item Code'));
	if(strlen($LeftOvers) > 0) { // If translated text is greater than 103, prints remainder
		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos-$FontSize, 65, $FontSize, $LeftOvers);
	}
$LeftOvers = $pdf->addTextWrap(165, $YPos+32,250, $FontSize, _('Item Description'));
$LeftOvers = $pdf->addTextWrap(420, $YPos+32, 85, $FontSize, _('Quantity'),'right');
$LeftOvers = $pdf->addTextWrap(485, $YPos+32, 85, $FontSize, _('Price'),'right');
$LeftOvers = $pdf->addTextWrap(535, $YPos+32, 85, $FontSize, _('Discount'),'right');
$LeftOvers = $pdf->addTextWrap(615, $YPos+32, 55, $FontSize, _('Tax Class'),'right');
	if(strlen($LeftOvers) > 0) { // If translated text is greater than 55, prints remainder
		$LeftOvers = $pdf->addTextWrap(615,$YPos-$FontSize,55, $FontSize, $LeftOvers,'right');
	}
$LeftOvers = $pdf->addTextWrap(665, $YPos+32, 70, $FontSize, _('Tax Amount'),'right');
	if(strlen($LeftOvers) > 0) { // If translated text is greater than 70, prints remainder
		$LeftOvers = $pdf->addTextWrap(665, $YPos-$FontSize+32, 70, $FontSize, $LeftOvers,'right');
	}
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos+32, 90, $FontSize, _('Total'),'right');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+24,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin-40,// RoundRectangle $Height.
	1,// RoundRectangle $RadiusX.
	1);// RoundRectangle $RadiusY.

// Line under table headings:
//$LineYPos = $YPos - $FontSize -1;
//$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
