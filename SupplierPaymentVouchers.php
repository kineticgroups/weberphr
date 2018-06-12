<?php
/* $Id: Suppliers.php 7751 2018-04-13 16:34:26Z raymond $ */

include('includes/session.php');
$Title = _('Supplier Payment Vouchers');
/* webERP manual links before header.php */
$ViewTopic= 'AccountsPayable';
$BookMark = 'PaymentVouchers';
include('includes/header.php');

include('includes/SQL_CommonFunctions.inc');
include('includes/CountriesArray.php');

echo '<a href="' . $RootPath . '/SelectSupplier.php">' . _('Search For Supplier') . '</a><br />' . "\n";

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/user.png" title="' .
		_('Employee data') . '" alt="" />' . ' ' . $Title . '</p>';

		if (isset($_POST['SupplierID'])){
			$SelectedSupplier = mb_strtoupper($_POST['SupplierID']);
		} elseif (isset($_GET['SupplierID'])){
			$SelectedSupplier = mb_strtoupper($_GET['SupplierID']);
		}

		if (isset($_POST['VoucherID'])){
			$SelectedVoucher = mb_strtoupper($_POST['VoucherID']);
		} elseif (isset($_GET['VoucherID'])){
			$SelectedVoucher = mb_strtoupper($_GET['VoucherID']);
		}

		if (isset($_POST['New'])){
			$New = mb_strtoupper($_POST['New']);
		} elseif (isset($_GET['New'])){
			$New = mb_strtoupper($_GET['New']);
		}

		if (isset($Errors)) {
			unset($Errors);
		}

		$Errors = array();

		if(isset($_GET['Transno'])){
			$sqlqt= "SELECT userid,cancreate,authlevel,canapprove
						 FROM paymentvoucherauth
						 WHERE userid='".$_SESSION['UserID']."'";

			$ErrMsg = _('Your not Authorised');
			$resultqt = DB_query($sqlqt,$ErrMsg);

			$myrowqt = DB_fetch_array($resultqt);
			if ($myrowqt > 0) {

				if($myrowqt['cancreate']==1){

					$status="pending";
					$sqlQueryt = "INSERT INTO  supplier_payment_vouchers
								(supplier_id,
									voucher_status,
									created_by
							)
							VALUES ('" . $_GET['SupplierID'] . "',
					'" . $status. "',
					'" . $_SESSION['UserID']. "'
					)";
					$Suppliervoucher = DB_query($sqlQueryt);

					$supplier_payment_vouchers_id = DB_Last_Insert_ID($db,'supplier_payment_vouchers','supplier_paymentvoucher_id');


						$sqlQuery2t = "INSERT INTO  suptrans_paymentvoucher
									(payment_voucher_id,
										suptrans_id
								)
								VALUES ('" . $supplier_payment_vouchers_id . "',
						'" . $_GET['Transno']. "'
						)";
					$trans = DB_query($sqlQuery2t);

					prnMsg(_('Payment Voucher') . ' ' .$supplier_payment_vouchers_id .  ' ' . _('has been created'),'success');




				}else {
						//$New=true;
							$InputError = 1;
							prnMsg(_('Your not Authorised to create a Payment Voucher'),'error');
							$Errors[$i] = 'SUPPTRANS';
							$i++;

						}


		}

		}



if (isset($_POST['submit'])){
		$InputError = 0;
		$i=1;

		if (empty($_POST['SUPPTRANS'])) {
			$InputError = 1;
			prnMsg(_('Please select a Purchase Invoice'),'error');
			$Errors[$i] = 'SUPPTRANS';
			$i++;
			$New='TRUE';
		}
		if(!isset($_POST['VoucherDescription']) || mb_strlen($_POST['VoucherDescription']) < 5)
		{
			$InputError = 1;
			prnMsg(_('Please enter details(Description) for payment voucher'),'error');
			$Errors[$i] = 'VoucherDescription';
			$i++;
			$New='TRUE';
		}

		if($InputError == 0)
		{
				$sqlq= "SELECT userid,cancreate,authlevel,canapprove
						 FROM paymentvoucherauth
						 WHERE userid='".$_SESSION['UserID']."'";

				$ErrMsg = _('Your not Authorised');
				$resultq = DB_query($sqlq,$ErrMsg);

				$myrowq = DB_fetch_array($resultq);
				if ($myrowq > 0) {
				    if($myrowq['cancreate']==1){
						    if(isset($_POST['Edit'])){
										//edit the voucher description
										$sql_voucher_description = "UPDATE supplier_payment_vouchers SET voucher_description='".$_POST['VoucherDescription']."' where supplier_paymentvoucher_id='".$SelectedVoucher."'";
										$voucher_description_result = DB_query($sql_voucher_description);
							      $sql14="DELETE FROM suptrans_paymentvoucher WHERE payment_voucher_id='".$SelectedVoucher."'";
										$result14 =DB_query($sql14);
										$selected_trans = $_POST['SUPPTRANS'];

										foreach($selected_trans as $trans)
										{
										    $sqlQuery2 = "INSERT INTO  suptrans_paymentvoucher
												(payment_voucher_id,
													suptrans_id
												)
												VALUES ('" . $SelectedVoucher . "',
												'" . $trans. "'
												)";
												$trans = DB_query($sqlQuery2);
										}
										prnMsg(_('Payment Voucher') . ' ' .$SelectedVoucher .  ' ' . _('has been Edited'),'success');

										unset($SelectedVoucher);

								}
								else
								{
						        $status="pending";
										$sqlQuery = "INSERT INTO  supplier_payment_vouchers
										(supplier_id,
										voucher_status,
										voucher_description,
		                created_by
										)
										VALUES ('" . $_POST['SupplierID'] . "',
										'" . $status. "',
										'" . $_POST['VoucherDescription']. "',
		        				'" . $_SESSION['UserID']. "'
										)";
										$Suppliervoucher = DB_query($sqlQuery);
										$supplier_payment_vouchers_id = DB_Last_Insert_ID($db,'supplier_payment_vouchers','supplier_paymentvoucher_id');

										$selected_trans = $_POST['SUPPTRANS'];
										foreach($selected_trans as $trans)
										{
										    $sqlQuery2 = "INSERT INTO  suptrans_paymentvoucher
												(payment_voucher_id,
													suptrans_id
												)
												VALUES ('" . $supplier_payment_vouchers_id . "',
												'" . $trans. "'
												)";
												$trans = DB_query($sqlQuery2);

										}
										prnMsg(_('Payment Voucher') . ' ' .$supplier_payment_vouchers_id .  ' ' . _('has been created'),'success');

								}

						}
						else
						{
						    $New=true;
								$InputError = 1;
								prnMsg(_('You cannot create a Payment Voucher'),'error');
								$Errors[$i] = 'SUPPTRANS';
								$i++;
						}

					}
					else
					{
							$New=true;
							$InputError = 1;
							prnMsg(_('You cannot create  a Payment Voucher'),'error');
							$Errors[$i] = 'SUPPTRANS';
							$i++;

					}
		}


}elseif (isset($_GET['delete'])) {
	$sqlq= "SELECT userid,cancreate,authlevel,canapprove
				 FROM paymentvoucherauth
				 WHERE userid='".$_SESSION['UserID']."'";

	$ErrMsg = _('Your not Authorised');
	$resultq = DB_query($sqlq,$ErrMsg);
	$myrowq = DB_fetch_array($resultq);

	if ($myrowq > 0) {
		if($myrowq['cancreate']==1){
			$sql12= "SELECT voucher_status
						 FROM supplier_payment_vouchers
						 WHERE supplier_paymentvoucher_id='".$SelectedVoucher."'";
$result12 = DB_query($sql12);
$myrow12 = DB_fetch_array($result12);
if($myrow12['voucher_status']=='approved'){
	$InputError = 1;
	prnMsg(_('This Voucher is already Approved'),'error');
	$Errors[$i] = 'SUPPTRANS';
	$i++;
}else{

$sql14="DELETE FROM suptrans_paymentvoucher WHERE payment_voucher_id='".$SelectedVoucher."'";
$result14 =DB_query($sql14);

	$sql13="DELETE FROM supplier_payment_vouchers WHERE supplier_paymentvoucher_id='".$SelectedVoucher."'";
	$ErrMsg = _('The Payment Voucher record could not be deleted because');
	$result13 = DB_query($sql13,$ErrMsg);
	echo '<br />';
	prnMsg(_('Payment Voucher') . ' ' . $SelectedVoucher  . ' ' . _('has been deleted') ,'success');
}

	}else {
			$InputError = 1;
			prnMsg(_('You dont have the rights to delete this Payment VoucherDD'),'error');
			$Errors[$i] = 'SUPPTRANS';
			$i++;
		}

	}else {
		$InputError = 1;
		prnMsg(_('You dont have the rights to delete this Payment Voucher'),'error');
		$Errors[$i] = 'SUPPTRANS';
		$i++;

	}

unset($SelectedVoucher);



}

if (isset($_GET['Approve'])) {
	$sqlq= "SELECT userid,cancreate,authlevel,canapprove
				 FROM paymentvoucherauth
				 WHERE userid='".$_SESSION['UserID']."'";

	$ErrMsg = _('Your not Authorised');
	$resultq = DB_query($sqlq,$ErrMsg);

	$myrowq = DB_fetch_array($resultq);
	if ($myrowq > 0) {

		if($myrowq['canapprove']==1  ){

if($_GET['Total']<=$myrowq['authlevel']){

			$sql13 = "UPDATE supplier_payment_vouchers
				SET voucher_status = 'approved',
            approved_by='".$_SESSION['UserID']."'
			WHERE supplier_paymentvoucher_id='".$SelectedVoucher."'";
$result14 = DB_query($sql13,$ErrMsg);
	echo '<br />';
	prnMsg(_('Payment Voucher') . ' ' . $SelectedVoucher  . ' ' . _('has been Approved') ,'success');
}else{

	$InputError = 1;
	prnMsg(_('Your not allowed to approve a PaymentVoucher above'._($myrowq['authlevel']).''),'error');
	$Errors[$i] = 'SUPPTRANS';
	$i++;

}

	}else {
			$InputError = 1;
			prnMsg(_('You dont have the rights to Approve Payment Voucher'),'error');
			$Errors[$i] = 'SUPPTRANS';
			$i++;
		}

	}else {
		$InputError = 1;
		prnMsg(_('You dont have the rights to Approve Payment Vouchers'),'error');
		$Errors[$i] = 'SUPPTRANS';
		$i++;

	}

	unset($SelectedVoucher);

}

if(!empty($SelectedVoucher)){

	echo'<h3>Supplier Payment Voucher '. _($SelectedVoucher).'</h3>';

		echo '<table class="selection">
				<tr>
					<th class="ascending">', _('Invoice No'), '</th>
					<th class="ascending">', _('Supplier Reference'), '</th>
					<th class="ascending">', _('Due Date'), '</th>
					<th class="ascending">', _('Transction Date'), '</th>
					<th class="ascending">', _('Amount'), '</th>
	<th class="ascending">', _('Alloc'), '</th>

				</tr>';
		$base_sql =	"SELECT
            suptrans_id,
						transno,
           suppreference,
					 duedate,
					 inputdate ,
						ovamount ,
						alloc
					FROM suptrans_paymentvoucher
					 JOIN supptrans ON suptrans_paymentvoucher.suptrans_id  = supptrans.transno
					 WHERE payment_voucher_id=".$SelectedVoucher."";


		$Result = DB_query($base_sql);

		$k = 1;// Row colour counter.
		while ($MyRow = DB_fetch_array($Result)) {
			if($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="EvenTableRows">';
				$k = 1;
			}

				//$deparmentDetails = DB_fetch_array($result2);
			/*The SecurityHeadings array is defined in config.php */
			echo	'<td class="text">'. $MyRow['transno']. '</td>
					<td class="text">'. $MyRow['suppreference']. '</td>

					<td class="text">'. $MyRow['duedate']. '</td>
					<td class="text">'.$MyRow['inputdate']. '</td>
	<td class="text">'. $MyRow['ovamount']. '</td>
	<td class="text">'._(($MyRow['alloc']==1)?'YES':'NO').'</a></td>
	</tr>';
		}// END foreach($Result as $MyRow).
		echo '</table>';
}

if($New=='TRUE' AND $Edit!='1'){

	$SupplierName = '';
	$SQL = "SELECT suppliers.suppname
			FROM suppliers
			WHERE suppliers.supplierid ='" . $SelectedSupplier . "' ";
	$SupplierNameResult = DB_query($SQL);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow = DB_fetch_row($SupplierNameResult);
		$SupplierName = $myrow[0];
	}

	$AllVoucherstrans = array();
			$Query1 = "SELECT suptrans_id FROM suptrans_paymentvoucher";

			$Result5 = DB_query($Query1);
			while ($Row1 = DB_fetch_array($Result5)) {
				$AllVoucherstrans[$Row1['suptrans_id']] = $Row1['suptrans_id'];
			}

if (isset($_GET['VoucherID'])){
		$sql = "SELECT voucher_description,voucher_date,voucher_status FROM supplier_payment_vouchers
						where supplier_paymentvoucher_id='".$_GET['VoucherID']."' limit 1";
		$result_voucher_details = DB_query($sql);
		$voucher_description = '';
		while($voucher_row = DB_fetch_array($result_voucher_details))
		{
			 $voucher_description = $voucher_row['voucher_description'];
		}
    $AllVoucherstransId = array();
					$Query1 = "SELECT suptrans_id FROM suptrans_paymentvoucher
					WHERE payment_voucher_id='".$_GET['VoucherID']."'";

					$Result5 = DB_query($Query1);
					while ($Row1 = DB_fetch_array($Result5)) {
						$AllVoucherstransId[$Row1['suptrans_id']] = $Row1['suptrans_id'];
					}
}



	$AllTRANS = array();
			$Query = "SELECT 	transno, trandate,inputdate,suppreference FROM supptrans
			 WHERE supplierno ='" . $SelectedSupplier . "'AND alloc = 0 AND type=20 ";
			$Result4 = DB_query($Query);
			while ($RowType = DB_fetch_array($Result4)) {
				$AllTRANS[$RowType['transno']] = 'INV_No: '.$RowType['transno'].', Date: '.$RowType['inputdate'].' ,of REF_No: '.$RowType['suppreference'].' ';
			}

if($_GET['Edit']!=1){
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/supplier.png" title="' . _('Supplier') . '" alt="" />' . ' ' . _('Supplier') . ' : <b>' . $SelectedSupplier . ' - ' . $SupplierName . '</b> ' . _('has been selected') . '.</p>';

	echo '<table class="selection">
			<tr>
				<th class="ascending">', _('Voucher No'), '</th>
				<th class="ascending">', _('Supplier'), '</th>
				<th class="ascending">', _('Date'), '</th>
				<th class="ascending">', _('Amount'), '</th>
				<th class="ascending">', _('Description'), '</th>
				<th class="ascending">', _('Status'), '</th>


				<th class="noprint" colspan="2">&nbsp;</th>
			</tr>';
	$base_sql =	"SELECT
					supplier_paymentvoucher_id,
					suppname,
					supplier_id,
					voucher_date,
					voucher_description,
					supplier_paymentvoucher_id,
					voucher_status
				FROM supplier_payment_vouchers
				 JOIN suppliers ON supplier_payment_vouchers.supplier_id  = suppliers.supplierid
				 WHERE supplierid='".$SelectedSupplier ."'";


	$Result = DB_query($base_sql);

	$k = 1;// Row colour counter.
	while ($MyRow = DB_fetch_array($Result)) {
		if($k == 1) {
			echo '<tr class="OddTableRows">';
			$k = 0;
		} else {
			echo '<tr class="EvenTableRows">';
			$k = 1;
		}
	$sql3 ="SELECT SUM(ovamount) AS totalamount  FROM suptrans_paymentvoucher
	JOIN supptrans ON suptrans_paymentvoucher.suptrans_id  = supptrans.transno
	 WHERE payment_voucher_id =".$MyRow['supplier_paymentvoucher_id']."";
	$Result3 = DB_query($sql3);
	$MyRow3 = DB_fetch_row($Result3);
			//$deparmentDetails = DB_fetch_array($result2);
		/*The SecurityHeadings array is defined in config.php */
		echo	'<td class="text">'. $MyRow['supplier_paymentvoucher_id']. '</td>
				<td class="text">'. $MyRow['suppname']. '</td>

				<td class="text">'. $MyRow['voucher_date']. '</td>

	<td class="text">'. $MyRow3[0] . '</td>
	<td class="text">'. $MyRow['voucher_description'] . '</td>
	<td class="text">';
	echo $MyRow['voucher_status'].': ';
if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers.php?Total='.$MyRow3[0].'&Approve=1&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Approve'). '</a>';
}
echo'</td>
<td class="text"><a href="SupplierPaymentVouchers.php?NEW1=1&VoucherID='. $MyRow['supplier_paymentvoucher_id'].'">'._('View'). '</a></td>
<td class="noprint">';if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers.php?SupplierID='.$MyRow['supplier_id'].'&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '&New=true&Edit=1">'. _('Edit'). '</a>';
}
else if($MyRow['voucher_status']=='approved'){
echo'<a href="SupplierPayVoucher.php?SupplierID='.$MyRow['supplier_id'].'&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Pay'). '</a>';
}
else if($MyRow['voucher_status']=='paid'){
echo'<a href="SupplierPrintVoucher.php?VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Print'). '</a>';
}
echo '</td><td class="noprint">';
if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers
.php?VoucherID='. $MyRow['supplier_paymentvoucher_id']. '&amp;delete=1" onclick="return confirm(\'', _('Are you sure you wish to delete this employee?'), '\');">'. _('Delete');
}
echo '</a></td>

			</tr>';
	}// END foreach($Result as $MyRow).
	echo '</table>
		<br />';

}

	echo'<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
	<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="SupplierID" value="' . $SelectedSupplier. '" />';
if (isset($_GET['VoucherID'])){
echo'<input type="hidden" name="Edit" value="3" />';
echo'<input type="hidden" name="VoucherID" value="'.$_GET['VoucherID'].'" />';
}

echo'
	<table class="selection"><tr>
			<td>' . _('Purchase Invoices ') . ':</td>
			<td><select name="SUPPTRANS[]" multiple="multiple" size="10">';

				foreach ($AllTRANS as $Transno => $Trans) {
				if (in_array($Transno, $AllVoucherstrans)) {

					}else{
						echo'<option value="'.$Transno.'">'.$Trans.'</option>';
					}


if (isset($_GET['VoucherID'])){

					if (in_array($Transno, $AllVoucherstransId)) {
echo'<option selected value="'.$Transno.'">'.$Trans.'</option>';
						}
}
			}

			echo '</select>
</td>
		</tr>
		<tr>
			<td>' . _('Voucher Details ') . ':</td>
			<td><textarea name="VoucherDescription" maxlength="300">'.$voucher_description.'</textarea></td>
		</tr>


	</table>
	<br />
	<div class="centre">
		<input type="submit" name="submit" value="' . _('Accept') . '" />
	</div>
	</form>';

}elseif (!isset($_GET['NEW1'])) {


	echo '<table class="selection">
			<tr>
				<th class="ascending">', _('Voucher No'), '</th>
				<th class="ascending">', _('Supplier'), '</th>
				<th class="ascending">', _('Date'), '</th>
				<th class="ascending">', _('Amount'), '</th>
				<th class="ascending">', _('Description'), '</th>
				<th class="ascending">', _('Status'), '</th>
<th class="ascending"></th>
				<th class="noprint" colspan="2">&nbsp;</th>
			</tr>';
	$base_sql =	"SELECT
					supplier_paymentvoucher_id,
					suppname,
          supplier_id,
					voucher_date,
					voucher_description,
					voucher_status
				FROM supplier_payment_vouchers
				 JOIN suppliers ON supplier_payment_vouchers.supplier_id  = suppliers.supplierid";


	$Result = DB_query($base_sql);

	$k = 1;// Row colour counter.
	while ($MyRow = DB_fetch_array($Result)) {
		if($k == 1) {
			echo '<tr class="OddTableRows">';
			$k = 0;
		} else {
			echo '<tr class="EvenTableRows">';
			$k = 1;
		}
	$sql3 ="SELECT SUM(ovamount) AS totalamount  FROM suptrans_paymentvoucher
JOIN supptrans ON suptrans_paymentvoucher.suptrans_id  = supptrans.transno
	 WHERE payment_voucher_id =".$MyRow['supplier_paymentvoucher_id']."";
$Result3 = DB_query($sql3);
$MyRow3 = DB_fetch_row($Result3);
			//$deparmentDetails = DB_fetch_array($result2);
		/*The SecurityHeadings array is defined in config.php */
		echo	'<td class="text">'. $MyRow['supplier_paymentvoucher_id']. '</td>
				<td class="text">'. $MyRow['suppname']. '</td>

				<td class="text">'. $MyRow['voucher_date']. '</td>

<td class="text">'. $MyRow3[0] . '</td>
<td class="text">'. $MyRow['voucher_description']. '</td>
<td class="text">';
	echo $MyRow['voucher_status'].': ';
if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers.php?Total='.$MyRow3[0].'&Approve=1&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Approve'). '</a>';
}
echo'</td>
<td class="text"><a href="SupplierPaymentVouchers.php?NEW1=1&VoucherID='. $MyRow['supplier_paymentvoucher_id'].'">'._('View'). '</a></td>
<td class="noprint">';if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers.php?SupplierID='.$MyRow['supplier_id'].'&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '&New=true&Edit=1">'. _('Edit'). '</a>';
}
else if($MyRow['voucher_status']=='approved'){
echo'<a href="SupplierPayVoucher.php?SupplierID='.$MyRow['supplier_id'].'&VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Pay'). '</a>';
}
else if($MyRow['voucher_status']=='paid'){
echo'<a href="SupplierPrintVoucher.php?VoucherID='. $MyRow['supplier_paymentvoucher_id']. '">'. _('Print'). '</a>';
}
echo '</td><td class="noprint">';
if($MyRow['voucher_status']=='pending'){
echo'<a href="SupplierPaymentVouchers
.php?VoucherID='. $MyRow['supplier_paymentvoucher_id']. '&amp;delete=1" onclick="return confirm(\'', _('Are you sure you wish to delete this employee?'), '\');">'. _('Delete');
}
echo '</a></td>

				</tr>';
	}// END foreach($Result as $MyRow).
	echo '</table>
		<br />
<style>
select{widith:200px!important;height:400px !important;}
</style>

		';
}



include ('includes/footer.php');
?>
