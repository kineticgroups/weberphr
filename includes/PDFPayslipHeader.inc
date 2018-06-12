<?php
/* $Id: PO_PDFOrderPageHeader.inc 6941 2014-10-26 23:18:08Z daintree $*/
/*
	R & OS PHP-PDF class code to set up a new page
	a new page is implicit on the establishment of a new pdf object so
	only for subsequent pages
*/
if ($PageNumber>1){
	$pdf->newPage();
}
$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin+$FormDesign->logo->x,$Page_Height- $FormDesign->logo->y,$FormDesign->logo->width,$FormDesign->logo->height);

$pdf->RoundRectangleFill($FormDesign->HeaderRectangle->x, $Page_Height - $FormDesign->HeaderRectangle->y,$FormDesign->HeaderRectangle->width, $FormDesign->HeaderRectangle->height, $FormDesign->HeaderRectangle->radius,$FormDesign->HeaderRectangle->radius,'1111','DF',"",  array(220, 220, 220));
$pdf->addText($FormDesign->PayslipHeading->x,$Page_Height- $FormDesign->PayslipHeading->y,$FormDesign->PayslipHeading->FontSize, _('Payslip '). ' ');
$pdf->addText($FormDesign->PayMonth->x,$Page_Height- $FormDesign->PayMonth->y,$FormDesign->PayMonth->FontSize, _('PayPeriod: '). ' ' . $PayPeriod);
$pdf->addText($FormDesign->PageNumber->x,$Page_Height - $FormDesign->PageNumber->y, $FormDesign->PageNumber->FontSize, _('Page') . ': ' .$PageNumber);
$pdf->addText($FormDesign->CompanyName->x,$Page_Height - $FormDesign->CompanyName->y, $FormDesign->CompanyName->FontSize,  $_SESSION['CompanyRecord']['coyname']);

$pdf->Rectangle($FormDesign->DataRectangle->x, $Page_Height - $FormDesign->DataRectangle->y, $FormDesign->DataRectangle->width,$FormDesign->DataRectangle->height);
$pdf->RoundRectangleFill($FormDesign->HeaderRectangle2->x, $Page_Height - $FormDesign->HeaderRectangle2->y,$FormDesign->HeaderRectangle2->width, $FormDesign->HeaderRectangle2->height, $FormDesign->HeaderRectangle2->radius,$FormDesign->HeaderRectangle2->radius,'1111','DF',"",  array(220, 220, 220));
$pdf->RoundRectangleFill($FormDesign->HeaderRectangle3->x, $Page_Height - $FormDesign->HeaderRectangle3->y,$FormDesign->HeaderRectangle3->width, $FormDesign->HeaderRectangle3->height, $FormDesign->HeaderRectangle3->radius,$FormDesign->HeaderRectangle3->radius,'1111','DF',"",  array(220, 220, 220));
/*show employee info*/
$pdf->addText($FormDesign->EmployeeInfo->Line1->x,$Page_Height - $FormDesign->EmployeeInfo->Line1->y, $FormDesign->EmployeeInfo->Line1->FontSize,  _('Employee Name: '). ' ' . $EmpRow['first_name']. ' ' . $EmpRow['middle_name']. ' ' . $EmpRow['last_name']);
$pdf->addText($FormDesign->EmployeeInfo->Line2->x,$Page_Height - $FormDesign->EmployeeInfo->Line2->y, $FormDesign->EmployeeInfo->Line2->FontSize,  _('Employee Number: '). ' ' . $EmpRow['employee_id']);
$pdf->addText($FormDesign->EmployeeInfo->Line3->x,$Page_Height - $FormDesign->EmployeeInfo->Line3->y, $FormDesign->EmployeeInfo->Line3->FontSize,  _('Employee Department: '). ' ' . $DepartmentName);
$pdf->addText($FormDesign->EmployeeInfo->Line4->x,$Page_Height - $FormDesign->EmployeeInfo->Line4->y, $FormDesign->EmployeeInfo->Line4->FontSize,  _('Employee Joining Date: '). ' ' . $EmpRow['joining_date']);

/* show paygroup info*/
$pdf->addText($FormDesign->PaygroupInfo->Line1->x,$Page_Height - $FormDesign->PaygroupInfo->Line1->y, $FormDesign->PaygroupInfo->Line1->FontSize,  _('Payment Frequency: '). ' ' . $PayrollGroupRow['frequency_name']);
$pdf->addText($FormDesign->PaygroupInfo->Line2->x,$Page_Height - $FormDesign->PaygroupInfo->Line2->y, $FormDesign->PaygroupInfo->Line2->FontSize,  _('Loss of Pay: '). ' ' . $PayrollGroupRow['enable_lop']);
$pdf->addText($FormDesign->PaygroupInfo->Line3->x,$Page_Height - $FormDesign->PaygroupInfo->Line3->y, $FormDesign->PaygroupInfo->Line3->FontSize,  _('No of working days: '). ' ' . $PayrollGroupRow['working_days']);
$pdf->addText($FormDesign->PaygroupInfo->Line4->x,$Page_Height - $FormDesign->PaygroupInfo->Line4->y, $FormDesign->PaygroupInfo->Line4->FontSize,  _('No of days present: '). ' ' . $PayrollGroupRow['working_days']);
$pdf->addText($FormDesign->PaygroupInfo->Line5->x,$Page_Height - $FormDesign->PaygroupInfo->Line5->y, $FormDesign->PaygroupInfo->Line5->FontSize,  _('No of days absent: '). ' 0' );

/*Set up headings */
$pdf->addText($FormDesign->Headings->Column1->x,$Page_Height - $FormDesign->Headings->Column1->y, $FormDesign->Headings->Column1->FontSize, _('Earnings') );
$pdf->addText($FormDesign->Headings->Column2->x,$Page_Height - $FormDesign->Headings->Column2->y, $FormDesign->Headings->Column2->FontSize, _('Credited') );
$pdf->addText($FormDesign->Headings->Column3->x,$Page_Height - $FormDesign->Headings->Column3->y, $FormDesign->Headings->Column3->FontSize, _('Deductions') );
$pdf->addText($FormDesign->Headings->Column4->x,$Page_Height - $FormDesign->Headings->Column4->y, $FormDesign->Headings->Column4->FontSize, _('Deducted') );

$pdf->Line($FormDesign->Column1->startx, $Page_Height - $FormDesign->Column1->starty, $FormDesign->Column1->endx,$Page_Height - $FormDesign->Column1->endy);
$pdf->Line($FormDesign->Column2->startx, $Page_Height - $FormDesign->Column2->starty, $FormDesign->Column2->endx,$Page_Height - $FormDesign->Column2->endy);
$pdf->Line($FormDesign->Column3->startx, $Page_Height - $FormDesign->Column3->starty, $FormDesign->Column3->endx,$Page_Height - $FormDesign->Column3->endy);


//$pdf->Rectangle($FormDesign->DataRectangle->x, $Page_Height - $FormDesign->DataRectangle->y, $FormDesign->DataRectangle->width,$FormDesign->DataRectangle->height);
?>
