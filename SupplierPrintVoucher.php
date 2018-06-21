<?php

/* $Id: PDFGrn.php 7751 2017-04-13 16:34:26Z rchacon $*/

include('includes/session.php');

if (isset($_GET['VoucherID'])) {
	$VoucherID=$_GET['VoucherID'];
} else {
	$VoucherID='';
}

$FormDesign = simplexml_load_file($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/FormDesigns/PaymentVoucher.xml');

// Set the paper size/orintation
$PaperSize = $FormDesign->PaperSize;
$line_height=$FormDesign->LineHeight;
include('includes/PDFStarter.php');
$PageNumber=1;
$pdf->addInfo('Title', _('Payslip') );

if ($VoucherID == 'Preview'){

}
else{
  if(!is_numeric($VoucherID))
  {
    prnMsg(_('invalid Payment voucher number'),'error');
    exit();
  }
  //get voucher details and odbc_specialcolumns
  $sql = "SELECT supplier_id,voucher_description,approved_by,created_by,bank_account_used,gl_transaction_id, suppname from supplier_payment_vouchers
   join suppliers on supplier_payment_vouchers.supplier_id=suppliers.supplierid where supplier_paymentvoucher_id='".$VoucherID."'";
   $ErrMsg = _('Cannot get voucher details because');
   $DbgMsg = _('Cannot get voucher using the SQL');
   $result = DB_query($sql,$ErrMsg,$DbgMsg,true);
   $voucher_row = DB_fetch_array($result);
   $supplier_name = $voucher_row['suppname'];
   $approved_by = $voucher_row['approved_by'];
   $prepared_by = $voucher_row['created_by'];
	 $voucher_description = $voucher_row['voucher_description'];
   //get transaction Details
   $sql_transaction = DB_query("SELECT trandate,chequeno from gltrans where typeno='".$voucher_row['gl_transaction_id']."' and account='".$voucher_row['bank_account_used']."'");
   $sql_transaction_row = DB_fetch_array($sql_transaction);
   $date_of_payment = $sql_transaction_row['trandate'];
   $cheque_no = $sql_transaction_row['chequeno'];
   //
   $sql_amount = DB_query("SELECT SUM(ovamount) AS totalamount  FROM suptrans_paymentvoucher
           JOIN supptrans ON suptrans_paymentvoucher.suptrans_id  = supptrans.transno
           WHERE payment_voucher_id =".$VoucherID."");
   $sum_row = DB_fetch_array($sql_amount);
   $payment_voucher_amount = $sum_row['totalamount'];
   $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
   $amount_in_words = $f->format($payment_voucher_amount);
   //
/*   opted to use description in voucher instead of purchase invoice, can be revised later
   $payment_voucher_description = '';
   $sql_description = DB_query("SELECT suppreference,transtext,suptrans_id from suptrans_paymentvoucher join supptrans on suptrans_paymentvoucher.suptrans_id=supptrans.transno where payment_voucher_id='".$VoucherID."'");
   while($myrow = DB_fetch_array($sql_description))
   {
     $payment_voucher_description .=$myrow['suppreference'].",".$myrow['transtext'].",";
   }
*/
   //
   if ($PageNumber>1){
   	$pdf->newPage();
   }

$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x+40, $Page_Height - $FormDesign->HeaderRectangle2->y-140,$FormDesign->HeaderRectangle2->width+760, $FormDesign->HeaderRectangle2->height+25, $FormDesign->HeaderRectangle2->radius,$FormDesign->HeaderRectangle2->radius,'1111','DF',"",  array(220, 220, 220));
$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x+320, $Page_Height - $FormDesign->HeaderRectangle2->y-60,$FormDesign->HeaderRectangle2->width+180, $FormDesign->HeaderRectangle2->height+25, $FormDesign->HeaderRectangle2->radius,$FormDesign->HeaderRectangle2->radius,'1111','DF',"",  array(220, 220, 220));

   $pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin+$FormDesign->logo->x,$Page_Height- $FormDesign->logo->y,$FormDesign->logo->width,$FormDesign->logo->height);
   $pdf->addText($FormDesign->PaymentVoucherHeading->x,$Page_Height- $FormDesign->PaymentVoucherHeading->y,$FormDesign->PaymentVoucherHeading->FontSize, _('Payment Voucher '). ' ');
   //voucher number
   $pdf->addText($FormDesign->VoucherNumber->x,$Page_Height- $FormDesign->VoucherNumber->y,$FormDesign->VoucherNumber->FontSize, _('Payment Voucher No: '). $VoucherID);
   //date of Payment
   $pdf->addText($FormDesign->PaymentDate->x,$Page_Height- $FormDesign->PaymentDate->y,$FormDesign->PaymentDate->FontSize, _('Payment Date: '). $date_of_payment);
   //method of Payment
   $pdf->addText($FormDesign->MethodOfPayment->x,$Page_Height- $FormDesign->MethodOfPayment->y,$FormDesign->MethodOfPayment->FontSize, _('Method of Payment '));
   //Cash
   $pdf->addText($FormDesign->Cash->x,$Page_Height- $FormDesign->Cash->y,$FormDesign->Cash->FontSize, _('Cash: '). '');
   //chequeno
   $pdf->addText($FormDesign->ChequeNumber->x,$Page_Height- $FormDesign->ChequeNumber->y,$FormDesign->ChequeNumber->FontSize, _('Cheque #: '). $cheque_no);
   //AmountToPay
   $pdf->addText($FormDesign->Amount->x,$Page_Height- $FormDesign->Amount->y,$FormDesign->Amount->FontSize, _('Amount: '). number_format($payment_voucher_amount,2));
   //supplier names
   $pdf->addText($FormDesign->SupplierInfo->x,$Page_Height- $FormDesign->SupplierInfo->y,$FormDesign->SupplierInfo->FontSize, _('To: '). $supplier_name);
   //amount in words
   $pdf->addText($FormDesign->AmountInWords->x,$Page_Height- $FormDesign->AmountInWords->y,$FormDesign->AmountInWords->FontSize,_('Sum of: ').$amount_in_words.' only.');
   //description
   //$pdf->addText($FormDesign->Description->x,$Page_Height- $FormDesign->Description->y,$FormDesign->Description->FontSize,_('Being: '));

   $LeftOvers = $pdf->addTextWrap($FormDesign->Description->x,$Page_Height- $FormDesign->Description->y,40, $FormDesign->Description->FontSize, _('Being: ').$voucher_description,'right');
   $descYPos = $Page_Height- $FormDesign->Description->y;
   if (mb_strlen($LeftOvers)>1){
   	$LeftOvers = $pdf->addTextWrap($FormDesign->Description->x,$descYPos-11,300,$FormDesign->Description->FontSize,$LeftOvers);
   	if (mb_strlen($LeftOvers)>1){
   		$LeftOvers = $pdf->addTextWrap($FormDesign->Description->x,$descYPos-25,300,$FormDesign->Description->FontSize,$LeftOvers);
   		if (mb_strlen($LeftOvers)>1){
   			$LeftOvers = $pdf->addTextWrap($FormDesign->Description->x,$descYPos-38,300,$FormDesign->Description->FontSize,$LeftOvers);
   			if (mb_strlen($LeftOvers)>1){
   				$LeftOvers = $pdf->addTextWrap($FormDesign->Description->x,$descYPos-50,300,$FormDesign->Description->FontSize,$LeftOvers);
   			}
   		}
   	}
   }
   //bottom row
   $pdf->addText($FormDesign->Totals->Column1->x,$Page_Height - $FormDesign->Totals->Column1->y, $FormDesign->Totals->Column1->FontSize, _('Prepared by: ').$prepared_by );
   $pdf->addText($FormDesign->Totals->Column2->x,$Page_Height - $FormDesign->Totals->Column2->y, $FormDesign->Totals->Column2->FontSize, _('Approved by: ').$approved_by );
   $pdf->addText($FormDesign->Totals->Column3->x,$Page_Height - $FormDesign->Totals->Column3->y, $FormDesign->Totals->Column3->FontSize, _('Paid by: ').$approved_by );
   $pdf->addText($FormDesign->Totals->Column4->x,$Page_Height - $FormDesign->Totals->Column4->y, $FormDesign->Totals->Column4->FontSize, _('Signature: ') );

	 // Draws a box with round corners around  info:

	 $pdf->RoundRectangle(
	 	$XPos+40,// RoundRectangle $XPos.
	 	$YPos+480,// RoundRectangle $YPos.
	 	760,// RoundRectangle $Width.
	 	420,// RoundRectangle $Height.
	 	1,// RoundRectangle $RadiusX.
	 	1);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
 	 	$XPos+40,// RoundRectangle $XPos.
 	 	$YPos+480,// RoundRectangle $YPos.
 	 	760,// RoundRectangle $Width.
 	 	25,// RoundRectangle $Height.
 	 	1,// RoundRectangle $RadiusX.
 	 	1);// RoundRectangle $RadiusY.

		// $pdf->RoundRectangle(
		// $XPos+40,// RoundRectangle $XPos.
		// $YPos+455,// RoundRectangle $YPos.
		// 760,// RoundRectangle $Width.
		// 25,// RoundRectangle $Height.
		// 0,// RoundRectangle $RadiusX.
		// 0);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
		$XPos+40,// RoundRectangle $XPos.
		$YPos+430,// RoundRectangle $YPos.
		760,// RoundRectangle $Width.
		25,// RoundRectangle $Height.
		0,// RoundRectangle $RadiusX.
		0);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
		$XPos+40,// RoundRectangle $XPos.
		$YPos+405,// RoundRectangle $YPos.
		760,// RoundRectangle $Width.
		25,// RoundRectangle $Height.
		0,// RoundRectangle $RadiusX.
		0);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
		$XPos+40,// RoundRectangle $XPos.
		$YPos+380,// RoundRectangle $YPos.
		760,// RoundRectangle $Width.
		25,// RoundRectangle $Height.
		0,// RoundRectangle $RadiusX.
		0);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
		$XPos+40,// RoundRectangle $XPos.
		$YPos+355,// RoundRectangle $YPos.
		760,// RoundRectangle $Width.
		145,// RoundRectangle $Height.
		0,// RoundRectangle $RadiusX.
		0);// RoundRectangle $RadiusY.

		$pdf->RoundRectangle(
		$XPos+40,// RoundRectangle $XPos.
		$YPos+210,// RoundRectangle $YPos.
		760,// RoundRectangle $Width.
		150,// RoundRectangle $Height.
		0,// RoundRectangle $RadiusX.
		0);// RoundRectangle $RadiusY.

		//Example format
		//$pdf->Line($FormDesign->Column2->startx, $Page_Height - $FormDesign->Column2->starty, $FormDesign->Column2->endx,$Page_Height - $FormDesign->Column2->endy);

		$pdf->Line(415,480,415,455); // Column line 1
		$pdf->Line(415,430,415,405); // Column line 2
		$pdf->Line(300,210,300,60); // Column line 3
		$pdf->Line(570,355,570,60); // Column line 4

  $pdf->OutputD($_SESSION['DatabaseName'] . '_PaymentVoucher_' . $VoucherID . '_' . date('Y-m-d').'.pdf');
  $pdf->__destruct();
}
