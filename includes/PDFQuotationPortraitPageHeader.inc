<?php
/*	$Id: PDFQuotationPortraitPageHeader.inc 6822 2014-08-15 20:24:57Z rchacon $*/

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/

// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
$XPos = $Page_Width/2 - 140;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos-120,780,0,30);

// Prints 'Quotation' title:
$pdf->addTextWrap(230, $Page_Height-$Top_Margin-25, $Page_Width, 14, _('Quotation'), 'center');

$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x+35, $Page_Height - $FormDesign->HeaderRectangle2->y-257,$FormDesign->HeaderRectangle2->width+525, $FormDesign->HeaderRectangle2->height+15, $FormDesign->HeaderRectangle2->radius+1,$FormDesign->HeaderRectangle2->radius+1,'1111','DF',"",  array(220, 220, 220));

// Prints company info:
$XPos = $Page_Width/2+$Left_Margin;
$YPos = 720;
$FontSize = 12;
$pdf->addText($XPos-295, $YPos+40, $FontSize-3, $_SESSION['CompanyRecord']['coyname']);
$YPos = 720;
$FontSize = 10;
$pdf->addText($XPos-295, $YPos+28, $FontSize-2, $_SESSION['CompanyRecord']['regoffice1'] . ', ' . $_SESSION['CompanyRecord']['regoffice2']);
//$pdf->addText($XPos-300, $YPos-$FontSize*1, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($XPos-295, $YPos+20, $FontSize-2, $_SESSION['CompanyRecord']['regoffice3']. ', ' . $_SESSION['CompanyRecord']['regoffice4']);
//$pdf->addText($XPos-300, $YPos-$FontSize*3, $FontSize, $_SESSION['CompanyRecord']['regoffice4']);
$pdf->addText($XPos-295, $YPos+12, $FontSize-2, $_SESSION['CompanyRecord']['regoffice5'] .
	', ' . $_SESSION['CompanyRecord']['regoffice6']);
$pdf->addText($XPos-295, $YPos+4, $FontSize-2,  _('Tel') . ': ' . $_SESSION['CompanyRecord']['telephone']);
	//' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos-295, $YPos-4, $FontSize-2, $_SESSION['CompanyRecord']['email']);

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 770;
$FontSize=10;
$myrow = array_map(html_entity_decode, $myrow);
$pdf->addText($XPos+210, $YPos-80,$FontSize-2, _('Delivery To').':' );
$pdf->addText($XPos+210, $YPos-95,$FontSize-2, $myrow['deliverto']);
$pdf->addText($XPos+210, $YPos-105,$FontSize-2, $myrow['deladd1']);
$pdf->addText($XPos+210, $YPos-115,$FontSize-2, $myrow['deladd2']);
$pdf->addText($XPos+210, $YPos-125,$FontSize-2, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);

// Prints 'Quotation For' info:
$YPos -= 80;
$pdf->addText($XPos-3, $YPos,$FontSize-2, _('Quotation For').':');
$pdf->addText($XPos-3, $YPos-15,$FontSize-2, $myrow['name']);
$pdf->addText($XPos-3, $YPos-25,$FontSize-2, $myrow['address1']);
$pdf->addText($XPos-3, $YPos-35,$FontSize-2, $myrow['address2']);
$pdf->addText($XPos-3, $YPos-45,$FontSize-2, $myrow['address3'] . ' ' . $myrow['address4'] . ' ' . $myrow['address5']);

// // Draws a box with round corners around 'Delivery To' info:
// $XPos = 50;
// $YPos += 25;
// $pdf->RoundRectangle(
// 	$XPos-10,// RoundRectangle $XPos.
// 	$YPos+60+10,// RoundRectangle $YPos.
// 	200+10+10,// RoundRectangle $Width.
// 	60+10+10,// RoundRectangle $Height.
// 	1,// RoundRectangle $RadiusX.
// 	1);// RoundRectangle $RadiusY.

// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 90;
$pdf->RoundRectangle(
	$XPos-11,// RoundRectangle $XPos.
	$YPos+95,// RoundRectangle $YPos.
	525,// RoundRectangle $Width.
	60+10+10,// RoundRectangle $Height.
	1,// RoundRectangle $RadiusX.
	1);// RoundRectangle $RadiusY.

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-205, $Page_Height-$Top_Margin-$FontSize*1-123, 200, $FontSize-2, _('Number'). ': '.$_GET['QuotationNo'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-205, $Page_Height-$Top_Margin-$FontSize*2-124, 200, $FontSize-2, _('Your Ref'). ': '.$myrow['customerref'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-205, $Page_Height-$Top_Margin-$FontSize*3-125, 200, $FontSize-2, _('Date'). ': '.ConvertSQLDate($myrow['quotedate']), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-205, $Page_Height-$Top_Margin-$FontSize*4+35, 200, $FontSize-2, _('Page').': '.$PageNumber, 'right');

$FontSize=10;

// Prints the currency name:
require_once('includes/CurrenciesArray.php');// To get the currency name from the currency code.
$pdf->addText($Page_Width/2+$Left_Margin+20, $YPos, $FontSize-1,
	_('All amounts stated in') . ' ' . $myrow['currcode'] . ' - ' . $CurrencyName[$myrow['currcode']]);

// Prints table header:
$YPos -= 45;
$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+18,100,$FontSize, _('Item Code'));
$LeftOvers = $pdf->addTextWrap(120,$YPos+18,235,$FontSize, _('Item Description'));
$LeftOvers = $pdf->addTextWrap(180,$YPos+18,85,$FontSize, _('Quantity'),'right');
$LeftOvers = $pdf->addTextWrap(230,$YPos+18,85,$FontSize,_('Price'),'right');
$LeftOvers = $pdf->addTextWrap(280,$YPos+18,85,$FontSize, _('Discount'),'right');
$LeftOvers = $pdf->addTextWrap(330,$YPos+18,85,$FontSize, _('Tax Class'),'right');
$LeftOvers = $pdf->addTextWrap(400,$YPos+18,85,$FontSize, _('Tax Amount'),'right');
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-100, $YPos+18, 90, $FontSize, _('Total'),'right');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin-5,// RoundRectangle $XPos.
	$YPos+$FontSize+20,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin-50,// RoundRectangle $Height.
	1,// RoundRectangle $RadiusX.
	1);// RoundRectangle $RadiusY.

// Line under table headings:
// $LineYPos = $YPos - $FontSize -1;
// $pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
