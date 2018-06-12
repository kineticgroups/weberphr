<?php

/* $Id: SupplierPayVoucher.php 7751 2018-04-13 16:34:26Z raymond $ */
/*	Pay a suppliers payment voucher*/
include('includes/DefinePaymentClass.php');
include('includes/session.php');
$Title = _('Pay Payment Voucher');

$ViewTopic = 'Supplier';
$BookMark = 'Supplier';

include('includes/header.php');
include('includes/SQL_CommonFunctions.inc');
include('includes/CountriesArray.php');
if(empty($_GET['identifier'])) {
	/*unique session identifier to ensure that there is no conflict with other order enty session on the same machine */
	$identifier=date('U');
} else {
	$identifier=$_GET['identifier'];//edit GLItems
}
if(isset($_GET['NewPayment']) AND $_GET['NewPayment']=='Yes') {
	unset($_SESSION['PaymentDetail'.$identifier]->GLItems);
	unset($_SESSION['PaymentDetail'.$identifier]);
}

if(!isset($_SESSION['PaymentDetail'.$identifier])) {
	$_SESSION['PaymentDetail'.$identifier] = new Payment;
	$_SESSION['PaymentDetail'.$identifier]->GLItemCounter = 1;
}
if(isset($_GET['VoucherID']))
{
  $SelectedVoucher = $_GET['VoucherID'];
  $_POST['VoucherID']= $_GET['VoucherID'];
}

if((isset($_POST['UpdateHeader'])
	AND $_POST['BankAccount']=='')
	OR (isset($_POST['Process']) AND $_POST['BankAccount']=='')) {

	prnMsg(_('A bank account must be selected to make this payment from'), 'warn');
	$BankAccountEmpty=true;
} else {

	$BankAccountEmpty=false;
}
echo '<a href="' . $RootPath . '/SelectSupplier.php">' . _('Search For Supplier') . '</a><br />' . "\n";

if(isset($_GET['SupplierID'])) {
	/*The page was called with a supplierID check it is valid and default the inputs for Supplier Name and currency of payment */

	unset($_SESSION['PaymentDetail'.$identifier]->GLItems);
	unset($_SESSION['PaymentDetail'.$identifier]);
	$_SESSION['PaymentDetail'.$identifier] = new Payment;
	$_SESSION['PaymentDetail'.$identifier]->GLItemCounter = 1;

	$SQL= "SELECT suppname,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				currcode,
				factorcompanyid
			FROM suppliers
			WHERE supplierid='" . $_GET['SupplierID'] . "'";

	$Result = DB_query($SQL);
	if(DB_num_rows($Result)==0) {
		prnMsg( _('The supplier code that this payment page was called with is not a currently defined supplier code') . '. ' . _('If this page is called from the selectSupplier page then this assures that a valid supplier is selected'),'warn');
		include('includes/footer.php');
		exit;
	} else {
		$myrow = DB_fetch_array($Result);
		if($myrow['factorcompanyid'] == 0) {
			$_SESSION['PaymentDetail'.$identifier]->SuppName = $myrow['suppname'];
			$_SESSION['PaymentDetail'.$identifier]->Address1 = $myrow['address1'];
			$_SESSION['PaymentDetail'.$identifier]->Address2 = $myrow['address2'];
			$_SESSION['PaymentDetail'.$identifier]->Address3 = $myrow['address3'];
			$_SESSION['PaymentDetail'.$identifier]->Address4 = $myrow['address4'];
			$_SESSION['PaymentDetail'.$identifier]->Address5 = $myrow['address5'];
			$_SESSION['PaymentDetail'.$identifier]->Address6 = $myrow['address6'];
			$_SESSION['PaymentDetail'.$identifier]->SupplierID = $_GET['SupplierID'];
			$_SESSION['PaymentDetail'.$identifier]->Currency = $myrow['currcode'];
			$_POST['Currency'] = $_SESSION['PaymentDetail'.$identifier]->Currency;

		} else {
			$factorsql = "SELECT coyname,
			 					address1,
			 					address2,
			 					address3,
			 					address4,
			 					address5,
			 					address6
							FROM factorcompanies
							WHERE id='" . $myrow['factorcompanyid'] . "'";

			$FactorResult = DB_query($factorsql);
			$myfactorrow = DB_fetch_array($FactorResult);
			$_SESSION['PaymentDetail'.$identifier]->SuppName = $myrow['suppname'] . ' ' . _('care of') . ' ' . $myfactorrow['coyname'];
			$_SESSION['PaymentDetail'.$identifier]->Address1 = $myfactorrow['address1'];
			$_SESSION['PaymentDetail'.$identifier]->Address2 = $myfactorrow['address2'];
			$_SESSION['PaymentDetail'.$identifier]->Address3 = $myfactorrow['address3'];
			$_SESSION['PaymentDetail'.$identifier]->Address4 = $myfactorrow['address4'];
			$_SESSION['PaymentDetail'.$identifier]->Address5 = $myfactorrow['address5'];
			$_SESSION['PaymentDetail'.$identifier]->Address6 = $myfactorrow['address6'];
			$_SESSION['PaymentDetail'.$identifier]->SupplierID = $_GET['SupplierID'];
			$_SESSION['PaymentDetail'.$identifier]->Currency = $myrow['currcode'];
			$_POST['Currency'] = $_SESSION['PaymentDetail'.$identifier]->Currency;
		}
		if(isset($_GET['Amount']) AND is_numeric($_GET['Amount'])) {
			$_SESSION['PaymentDetail'.$identifier]->Amount = filter_number_format($_GET['Amount']);
		}
	}
}

if(isset($_POST['BankAccount']) AND $_POST['BankAccount']!='') {

	$_SESSION['PaymentDetail'.$identifier]->Account=$_POST['BankAccount'];
	/*Get the bank account currency and set that too */
	$ErrMsg = _('Could not get the currency of the bank account');
	$result = DB_query("SELECT currcode,
								decimalplaces
						FROM bankaccounts INNER JOIN currencies
						ON bankaccounts.currcode = currencies.currabrev
						WHERE accountcode ='" . $_POST['BankAccount'] . "'",
						$ErrMsg);

	$myrow = DB_fetch_array($result);
	if($_SESSION['PaymentDetail'.$identifier]->AccountCurrency != $myrow['currcode']) {
		//then we'd better update the functional exchange rate
		$DefaultFunctionalRate = true;
		$_SESSION['PaymentDetail'.$identifier]->AccountCurrency = $myrow['currcode'];
		$_SESSION['PaymentDetail'.$identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
	} else {
		$DefaultFunctionalRate = false;
	}
} else {

	$_SESSION['PaymentDetail'.$identifier]->AccountCurrency = $_SESSION['CompanyRecord']['currencydefault'];
	$_SESSION['PaymentDetail'.$identifier]->CurrDecimalPlaces = $_SESSION['CompanyRecord']['decimalplaces'];

}

/*set up the form whatever */
if(!isset($_POST['DatePaid'])) {
	$_POST['DatePaid'] = '';
}

if(isset($_POST['DatePaid'])
	AND ($_POST['DatePaid']==''
		OR !Is_Date($_SESSION['PaymentDetail'.$identifier]->DatePaid))) {

	$_POST['DatePaid']= Date($_SESSION['DefaultDateFormat']);
	$_SESSION['PaymentDetail'.$identifier]->DatePaid = $_POST['DatePaid'];
}

if(isset($_POST['Currency']) AND $_POST['Currency']!='') {
	/* Payment currency is the currency that is being paid */
	$_SESSION['PaymentDetail'.$identifier]->Currency=$_POST['Currency']; // Payment currency

	if($_SESSION['PaymentDetail'.$identifier]->AccountCurrency==$_SESSION['CompanyRecord']['currencydefault']) {
		$_POST['FunctionalExRate']=1;
		$_SESSION['PaymentDetail'.$identifier]->FunctionalExRate=1;
		$SuggestedFunctionalExRate =1;

	} else {
		/*To illustrate the rates required
			Take an example functional currency NZD payment in USD from an AUD bank account
			1 NZD = 0.80 USD
			1 NZD = 0.90 AUD
			The FunctionalExRate = 0.90 - the rate between the functional currency and the bank account currency
			The payment ex rate is the rate at which one can purchase the payment currency in the bank account currency
			or 0.8/0.9 = 0.88889
		*/

		/*Get suggested FunctionalExRate - between bank account and home functional currency */
		$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail'.$identifier]->AccountCurrency . "'");
		$myrow = DB_fetch_row($result);
		$SuggestedFunctionalExRate = $myrow[0];
		if($DefaultFunctionalRate) {
			$_SESSION['PaymentDetail'.$identifier]->FunctionalExRate = $SuggestedFunctionalExRate;
		}
	}

	if($_POST['Currency']==$_SESSION['PaymentDetail'.$identifier]->AccountCurrency) {
		/* if the currency being paid is the same as the bank account currency then default ex rate to 1 */
		$_POST['ExRate']=1;
		$_SESSION['PaymentDetail'.$identifier]->ExRate = 1; //ex rate between payment currency and account currency is 1 if they are the same!!
		$SuggestedExRate=1;
	} elseif(isset($_POST['Currency'])) {
		/*Get the exchange rate between the bank account currency and the payment currency*/
		$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail'.$identifier]->Currency . "'");
		$myrow = DB_fetch_row($result);
		$TableExRate = $myrow[0]; //this is the rate of exchange between the functional currency and the payment currency
		/*Calculate cross rate to suggest appropriate exchange rate between payment currency and account currency */
		$SuggestedExRate = $TableExRate/$SuggestedFunctionalExRate;
	}
}

// Reference in banking transactions:
if(isset($_POST['BankTransRef']) AND $_POST['BankTransRef']!='') {
	$_SESSION['PaymentDetail'.$identifier]->BankTransRef = $_POST['BankTransRef'];
}
// Narrative in general ledger transactions:
if(isset($_POST['Narrative']) AND $_POST['Narrative']!='') {
	$_SESSION['PaymentDetail'.$identifier]->Narrative = $_POST['Narrative'];
}
// Supplier narrative in general ledger transactions:
if(isset($_POST['gltrans_narrative'])) {
	if($_POST['gltrans_narrative']=='') {
		$_SESSION['PaymentDetail'.$identifier]->gltrans_narrative = $_POST['Narrative'];// If blank, it uses the bank narrative.
	} else {
		$_SESSION['PaymentDetail'.$identifier]->gltrans_narrative = $_POST['gltrans_narrative'];
	}
}
// Supplier reference in supplier transactions:
if(isset($_POST['supptrans_suppreference'])) {
	if($_POST['supptrans_suppreference']=='') {
		$_SESSION['PaymentDetail'.$identifier]->supptrans_suppreference = $_POST['Paymenttype'];// If blank, it uses the payment type.
	} else {
		$_SESSION['PaymentDetail'.$identifier]->supptrans_suppreference = $_POST['supptrans_suppreference'];
	}
}
// Transaction text in supplier transactions:
if(isset($_POST['supptrans_transtext'])) {
	if($_POST['supptrans_transtext']=='') {
		$_SESSION['PaymentDetail'.$identifier]->supptrans_transtext = $_POST['Narrative'];// If blank, it uses the narrative.
	} else {
		$_SESSION['PaymentDetail'.$identifier]->supptrans_transtext = $_POST['supptrans_transtext'];
	}
}

if(isset($_POST['Amount']) AND $_POST['Amount']!='') {
	$_SESSION['PaymentDetail'.$identifier]->Amount = filter_number_format($_POST['Amount']);
} else {
	if(!isset($_SESSION['PaymentDetail'.$identifier]->Amount)) {
		$_SESSION['PaymentDetail'.$identifier]->Amount = 0;
	}
}

if(isset($_POST['Discount']) AND $_POST['Discount']!='') {
	$_SESSION['PaymentDetail'.$identifier]->Discount = filter_number_format($_POST['Discount']);
} else {
	if(!isset($_SESSION['PaymentDetail'.$identifier]->Discount)) {
	 $_SESSION['PaymentDetail'.$identifier]->Discount = 0;
 }
}

if(isset($_POST['CommitBatch'])) {
	if($_POST['Amount']==0){
		prnMsg( _('This payment has no amounts entered and will not be processed'),'warn');
		include('includes/footer.php');
		exit;
	}

	if($_POST['BankAccount']=='') {
		prnMsg( _('No bank account has been selected so this payment cannot be processed'),'warn');
		include('includes/footer.php');
		exit;
	}

	//start transactions
	$PeriodNo = GetPeriod($_SESSION['PaymentDetail'.$identifier]->DatePaid,$db);

	$result = DB_Txn_Begin();


		/*Its a supplier payment type 22 */
		$CreditorTotal = (($_SESSION['PaymentDetail'.$identifier]->Amount)/$_SESSION['PaymentDetail'.$identifier]->ExRate)/$_SESSION['PaymentDetail'.$identifier]->FunctionalExRate;

		$TransNo = GetNextTransNo(22, $db);
		$TransType = 22;

		/* Create a SuppTrans entry for the supplier payment */
		$SQL = "INSERT INTO supptrans (
						transno,
						type,
						supplierno,
						trandate,
						inputdate,
						suppreference,
						rate,
						ovamount,
						transtext
					) VALUES ('" .
						$TransNo . "',
						22,'" .
						$_SESSION['PaymentDetail'.$identifier]->SupplierID . "','" .
						FormatDateForSQL($_SESSION['PaymentDetail'.$identifier]->DatePaid) . "','" .
						date('Y-m-d H-i-s') . "','" .
						$_SESSION['PaymentDetail'.$identifier]->supptrans_suppreference . "','" .
						($_SESSION['PaymentDetail'.$identifier]->FunctionalExRate * $_SESSION['PaymentDetail'.$identifier]->ExRate) . "','" .
						(-$_SESSION['PaymentDetail'.$identifier]->Amount-$_SESSION['PaymentDetail'.$identifier]->Discount) . "','" .
						$_SESSION['PaymentDetail'.$identifier]->supptrans_transtext .
					"')";
		$ErrMsg = _('Cannot insert a payment transaction against the supplier because');
		$DbgMsg = _('Cannot insert a payment transaction against the supplier using the SQL');
		$result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

		/*Update the supplier master with the date and amount of the last payment made */
		$SQL = "UPDATE suppliers
				SET	lastpaiddate = '" . FormatDateForSQL($_SESSION['PaymentDetail'.$identifier]->DatePaid) . "',
					lastpaid='" . $_SESSION['PaymentDetail'.$identifier]->Amount ."'
				WHERE suppliers.supplierid='" . $_SESSION['PaymentDetail'.$identifier]->SupplierID . "'";
		$ErrMsg = _('Cannot update the supplier record for the date of the last payment made because');
		$DbgMsg = _('Cannot update the supplier record for the date of the last payment made using the SQL');
		$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		$_SESSION['PaymentDetail'.$identifier]->gltrans_narrative = $_SESSION['PaymentDetail'.$identifier]->SupplierID . ' - ' . $_SESSION['PaymentDetail'.$identifier]->gltrans_narrative;

		if($_SESSION['CompanyRecord']['gllink_creditors']==1) { /* then do the supplier control GLTrans */
		/* Now debit creditors account with payment + discount */

			$SQL = "INSERT INTO gltrans (
						type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount
					) VALUES (
						22,'" .
						$TransNo . "','" .
						FormatDateForSQL($_SESSION['PaymentDetail'.$identifier]->DatePaid) . "','" .
						$PeriodNo . "','" .
						$_SESSION['CompanyRecord']['creditorsact'] . "','" .
						$_SESSION['PaymentDetail'.$identifier]->gltrans_narrative . "','" .
						$CreditorTotal .
					"')";
			$ErrMsg = _('Cannot insert a GL transaction for the creditors account debit because');
			$DbgMsg = _('Cannot insert a GL transaction for the creditors account debit using the SQL');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);



		} // end if gl creditors

		/* Bank account entry first */
		$SQL = "INSERT INTO gltrans (
					type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount
				) VALUES ('" .
					$TransType . "','" .
					$TransNo . "','" .
					FormatDateForSQL($_SESSION['PaymentDetail'.$identifier]->DatePaid) . "','" .
					$PeriodNo . "','" .
					$_SESSION['PaymentDetail'.$identifier]->Account . "','" .
					$_SESSION['PaymentDetail'.$identifier]->Narrative . "','" .
					(-$_SESSION['PaymentDetail'.$identifier]->Amount/$_SESSION['PaymentDetail'.$identifier]->ExRate/$_SESSION['PaymentDetail'.$identifier]->FunctionalExRate) .
				"')";
		$ErrMsg = _('Cannot insert a GL transaction for the bank account credit because');
		$DbgMsg = _('Cannot insert a GL transaction for the bank account credit using the SQL');
		$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		EnsureGLEntriesBalance($TransType,$TransNo,$db);

		/*now enter the BankTrans entry */
		$SQL = "INSERT INTO banktrans (
					transno,
					type,
					bankact,
					ref,
					exrate,
					functionalexrate,
					transdate,
					banktranstype,
					amount,
					currcode
				) VALUES ('" .
					$TransNo . "','" .
					$TransType . "','" .
					$_SESSION['PaymentDetail'.$identifier]->Account . "','" .
					$_SESSION['PaymentDetail'.$identifier]->BankTransRef . "','" .
					$_SESSION['PaymentDetail'.$identifier]->ExRate . "','" .
					$_SESSION['PaymentDetail'.$identifier]->FunctionalExRate . "','" .
					FormatDateForSQL($_SESSION['PaymentDetail'.$identifier]->DatePaid) . "','" .
					$_SESSION['PaymentDetail'.$identifier]->Paymenttype . "','" .
					-$_SESSION['PaymentDetail'.$identifier]->Amount . "','" .
					$_SESSION['PaymentDetail'.$identifier]->Currency .
				"')";
		$ErrMsg = _('Cannot insert a bank transaction because');
		$DbgMsg = _('Cannot insert a bank transaction using the SQL');
		$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		//update payment voucher status.
		$sql_voucher = "UPDATE supplier_payment_vouchers SET voucher_status='paid',bank_account_used='".$_SESSION['PaymentDetail'.$identifier]->Account."',gl_transaction_id='".$TransNo."' WHERE supplier_paymentvoucher_id='".$_POST['VoucherID']."'";
		$ErrMsg = _('Cannot update payment voucher because');
		$DbgMsg = _('Cannot update payment voucher using the SQL');
		$result = DB_query($sql_voucher,$ErrMsg,$DbgMsg,true);

		DB_Txn_Commit();
		prnMsg(_('Payment') . ' ' . $TransNo . ' ' . _('has been successfully entered'),'success');

		unset($_POST['BankAccount']);
		unset($_POST['DatePaid']);
		unset($_POST['ExRate']);
		unset($_POST['Paymenttype']);
		unset($_POST['Currency']);
		unset($_POST['Narrative']);
		unset($_POST['gltrans_narrative']);
		unset($_POST['supptrans_suppreference']);
		unset($_POST['supptrans_transtext']);
		unset($_POST['Amount']);
		unset($_POST['Discount']);
		unset($_SESSION['PaymentDetail'.$identifier]->GLItems);
		unset($_SESSION['PaymentDetail'.$identifier]->SupplierID);
		unset($_SESSION['PaymentDetail'.$identifier]);

}

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'] . '?identifier=' . $identifier) . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
	<br />
	<table class="selection">
	<tr>
		<th colspan="2"><h3>' . _('Make Payment ');

if($_SESSION['PaymentDetail'.$identifier]->SupplierID!='') {
	echo ' ' . _('to') . ' ' . $_SESSION['PaymentDetail'.$identifier]->SuppName;
}

if($_SESSION['PaymentDetail'.$identifier]->BankAccountName!='') {
	echo ' ' . _('from the') . ' ' . $_SESSION['PaymentDetail'.$identifier]->BankAccountName;
}

echo ' ' . _('on') . ' ' . $_SESSION['PaymentDetail'.$identifier]->DatePaid . '</h3></th></tr>';

$SQL = "SELECT bankaccountname,
				bankaccounts.accountcode,
				bankaccounts.currcode
		FROM bankaccounts
		INNER JOIN chartmaster
			ON bankaccounts.accountcode=chartmaster.accountcode
		INNER JOIN bankaccountusers
			ON bankaccounts.accountcode=bankaccountusers.accountcode
		WHERE bankaccountusers.userid = '" . $_SESSION['UserID'] ."'
		ORDER BY bankaccountname";

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank accounts was');
$AccountsResults = DB_query($SQL,$ErrMsg,$DbgMsg);

echo '<tr>
		<td>', _('Bank Account'), ':</td>
		<td><select autofocus="autofocus" name="BankAccount" onchange="ReloadForm(UpdateHeader)"  required="required" title="', _('Select the bank account that the payment has been made from'), '">';

if(DB_num_rows($AccountsResults)==0) {
	echo '</select></td>
		</tr>
		</table>
		<p />';
	prnMsg( _('Bank Accounts have not yet been defined. You must first') . ' <a href="' . $RootPath . '/BankAccounts.php">' . _('define the bank accounts') . '</a> ' . _('and general ledger accounts to be affected'),'warn');
	include('includes/footer.php');
	exit;
} else {
	echo '<option value=""></option>';
	while($myrow=DB_fetch_array($AccountsResults)) {
	/*list the bank account names */
		echo '<option ';
		if(isset($_POST['BankAccount']) AND $_POST['BankAccount']==$myrow['accountcode']) {
			echo 'selected="selected" ';
		}
		echo 'value="', $myrow['accountcode'], '">', $myrow['bankaccountname'], ' - ', $myrow['currcode'], '</option>';
	}
	echo '</select></td>
		</tr>';
}

echo '<tr>
		<td>', _('Date Paid'), ':</td>
		<td><input alt="', $_SESSION['DefaultDateFormat'], '" class="date" maxlength="10" name="DatePaid" onchange="isDate(this, this.value, ', "'", $_SESSION['DefaultDateFormat'], "'", ')" required="required" size="10" type="text" value="', $_SESSION['PaymentDetail'.$identifier]->DatePaid, '" /></td>
	</tr>';


if($_SESSION['PaymentDetail'.$identifier]->SupplierID=='') {
	echo '<tr>
			<td>' . _('Currency of Payment') . ':</td>
			<td><select name="Currency" required="required" onchange="ReloadForm(UpdateHeader)">';
	$SQL = "SELECT currency, currabrev, rate FROM currencies";
	$result=DB_query($SQL);

	if(DB_num_rows($result)==0) {
		echo '</select></td>
			</tr>';
		prnMsg( _('No currencies are defined yet. Payments cannot be entered until a currency is defined'),'error');
	} else {
		include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
		while($myrow=DB_fetch_array($result)) {
			echo '<option ';
			if($_SESSION['PaymentDetail'.$identifier]->Currency==$myrow['currabrev']) {
				echo 'selected="selected" ';
			}
			echo 'value="', $myrow['currabrev'], '">', $CurrencyName[$myrow['currabrev']], '</option>';
		}
		echo '</select> <i>', _('The transaction currency does not need to be the same as the bank account currency'), '</i></td>
			</tr>';
	}
} else { /*its a supplier payment so it must be in the suppliers currency */
	echo '<tr>';
	echo '<td><input type="hidden" name="Currency" value="' . $_SESSION['PaymentDetail'.$identifier]->Currency . '" />
			' . _('Supplier Currency') . ':</td>
			<td>' . $_SESSION['PaymentDetail'.$identifier]->Currency . '</td>
		</tr>';
	/*get the default rate from the currency table if it has not been set */
	if(!isset($_POST['ExRate']) OR $_POST['ExRate']=='') {
		$SQL = "SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail'.$identifier]->Currency ."'";
		$Result=DB_query($SQL);
		$myrow=DB_fetch_row($Result);
		$_POST['ExRate']=locale_number_format($myrow[0],'Variable');
	}
}

if(!isset($_POST['ExRate'])) {
	$_POST['ExRate']=1;
}

if(!isset($_POST['FunctionalExRate'])) {
	$_POST['FunctionalExRate']=1;
}
if($_SESSION['PaymentDetail'.$identifier]->AccountCurrency != $_SESSION['PaymentDetail'.$identifier]->Currency AND isset($_SESSION['PaymentDetail'.$identifier]->AccountCurrency)) {
	if (isset($SuggestedExRate) AND ($_POST['ExRate'] == 1 OR $_POST['Currency'] != $_POST['PreviousCurrency'] OR $_POST['PreviousBankAccount'] != $_SESSION['PaymentDetail' . $identifier]->Account)) {
		$_POST['ExRate'] = locale_number_format($SuggestedExRate,8);
	}

	if(isset($SuggestedExRate)) {
		$SuggestedExRateText = '<b>' . _('Suggested rate:') . ' 1 ' . $_SESSION['PaymentDetail'.$identifier]->AccountCurrency . ' = '	. locale_number_format($SuggestedExRate,8) . ' ' . $_SESSION['PaymentDetail'.$identifier]->Currency . '</b>';
	} else {
		$SuggestedExRateText = '1 ' . $_SESSION['PaymentDetail'.$identifier]->AccountCurrency . ' = ? ' . $_SESSION['PaymentDetail'.$identifier]->Currency;
	}
	echo '<tr>
			<td>', _('Payment Exchange Rate'), ':</td>
			<td><input class="number" maxlength="12" name="ExRate" size="14" title="', _('The exchange rate between the currency of the bank account currency and the currency of the payment'), '" type="text" value="', $_POST['ExRate'], '" /> ', $SuggestedExRateText, '. <i>', _('The exchange rate between the currency of the bank account currency and the currency of the payment'), '.</i></td>
		</tr>';
}

if($_SESSION['PaymentDetail'.$identifier]->AccountCurrency != $_SESSION['CompanyRecord']['currencydefault'] AND isset($_SESSION['PaymentDetail'.$identifier]->AccountCurrency)) {
	if (isset($SuggestedFunctionalExRate) AND ($_POST['FunctionalExRate']==1 OR $_POST['Currency'] != $_POST['PreviousCurrency'] OR $_POST['PreviousBankAccount'] != $_SESSION['PaymentDetail' . $identifier]->Account)) {
		$_POST['FunctionalExRate'] = locale_number_format($SuggestedFunctionalExRate,'Variable');
	}

	if(isset($SuggestedFunctionalExRate)) {
		$SuggestedFunctionalExRateText = '<b>' . _('Suggested rate:') . ' 1 ' . $_SESSION['CompanyRecord']['currencydefault'] . ' = ' . locale_number_format($SuggestedFunctionalExRate,8) . ' ' . $_SESSION['PaymentDetail'.$identifier]->AccountCurrency . '</b>';
	} else {
		$SuggestedFunctionalExRateText = '1 ' . $_SESSION['CompanyRecord']['currencydefault'] . ' = ? ' . $_SESSION['PaymentDetail'.$identifier]->AccountCurrency;
	}
	echo '<tr>
			<td>', _('Functional Exchange Rate'), ':</td>
			<td><input class="number" maxlength="12" name="FunctionalExRate" pattern="[0-9\.,]*" required="required" size="14" title="', _('The exchange rate between the currency of the business (the functional currency) and the currency of the bank account'), '" type="text" value="', $_POST['FunctionalExRate'], '" /> ', $SuggestedFunctionalExRateText, '. <i>', _('The exchange rate between the currency of the business (the functional currency) and the currency of the bank account'), '.</i></td>
		</tr>';
}
echo '<tr>
		<td>' . _('Payment type') . ':</td>
		<td><select name="Paymenttype">';

include('includes/GetPaymentMethods.php');
/* The array Payttypes is set up in includes/GetPaymentMethods.php
payment methods can be modified from the setup tab of the main menu under payment methods*/

foreach($PaytTypes as $PaytType) {

	if(isset($_POST['Paymenttype']) AND $_POST['Paymenttype']==$PaytType) {
		echo '<option selected="selected" value="' . $PaytType . '">' . $PaytType . '</option>';
	} else {
		echo '<option value="' . $PaytType . '">' . $PaytType . '</option>';
	}
} //end foreach
echo '</select></td>
	</tr>';
	echo '<tr>
			<td>' . _('Payment Voucher') . ':</td>
			<td><select id="VoucherID" name="VoucherID"  ><option>Select Payment voucher</option>';
			$sql_vouchers = DB_query("SELECT * FROM supplier_payment_vouchers WHERE voucher_status='approved'");
			$amount_to_pay = 0;
			while($vouchers_row = DB_fetch_array($sql_vouchers))
			{
				$payment_voucher_id = $vouchers_row['supplier_paymentvoucher_id'];
				$sql_amount = DB_query("SELECT SUM(ovamount) AS totalamount  FROM suptrans_paymentvoucher
								JOIN supptrans ON suptrans_paymentvoucher.suptrans_id  = supptrans.transno
				 				WHERE payment_voucher_id =".$payment_voucher_id."");
				$sum_row = DB_fetch_array($sql_amount);
				$payment_voucher_amount = $sum_row['totalamount'];
				if(isset($_POST['VoucherID']) AND $_POST['VoucherID']==$payment_voucher_id) {
					$amount_to_pay = $payment_voucher_amount;
					echo '<option data-id="'.$payment_voucher_amount.'" selected="selected" value="' . $payment_voucher_id . '">Voucher_no: ' . $payment_voucher_id . ' amount: '.number_format($payment_voucher_amount,2).'</option>';
				} else {
					echo '<option data-id="'.$payment_voucher_amount.'" value="' . $payment_voucher_id . '">Voucher No:' . $payment_voucher_id . ' amount: '.number_format($payment_voucher_amount,2). '</option>';
				}

			}
echo '</select></td></tr>';
echo '<script>
$( document ).ready(function() {
	var total_amount = $("#VoucherID").find(":selected").attr("data-id");
	$("#AmountToPay").val(total_amount);
		$("#VoucherID").change(function(){
				var total_amount = $("option:selected", this).attr("data-id");


				$("#AmountToPay").val(total_amount);

		});

});
			</script>';

if(!isset($_POST['ChequeNum'])) {
	$_POST['ChequeNum']='';
}
echo '<tr>
		<td>' . _('Cheque Number') . ':</td>
		<td><input maxlength="8" name="ChequeNum" size="10" type="text" value="' . $_POST['ChequeNum'] . '" /> ' . _('(if using pre-printed stationery)') . '</td>
	</tr>';

// Info to be inserted on `banktrans`.`ref` varchar(50):
if(!isset($_POST['BankTransRef'])) {
	$_POST['BankTransRef'] = '';
}
echo '<tr>
		<td>', _('Reference'), ':</td>
		<td><input maxlength="50" name="BankTransRef" size="52" type="text" value="', stripslashes($_POST['BankTransRef']), '" /> ', _('Reference in banking transactions'), '</td>
	</tr>';

// Info to be inserted on `gltrans`.`narrative` varchar(200):
if(!isset($_POST['Narrative'])) {
	$_POST['Narrative'] = '';
}
echo '<tr>
		<td>', _('Narrative'), ':</td>
		<td><input maxlength="200" name="Narrative" size="52" type="text" value="', stripslashes($_POST['Narrative']), '" /> ', _('Narrative in general ledger transactions'), '</td>
	</tr>';

echo '<tr>
		<td colspan="2"><div class="centre">
			<input name="PreviousCurrency" type="hidden" value="', $_POST['Currency'], '" />
			<input type="hidden" name="PreviousBankAccount" value="' . $_SESSION['PaymentDetail' . $identifier]->Account . '" />
			<input name="UpdateHeader" type="submit" value="', _('Update'), '" />
		</div></td>
	</tr>
	</table>
	<br />';

  /*a supplier is selected or the GL link is not active then set out
  the fields for entry of receipt amt and disc */

  	echo '<table class="selection">
  			<tr>
  				<th colspan="2"><h3>', _('Supplier Transactions Payment Entry'), '</h3></th>
  			</tr>';

  	// If the script was called with a SupplierID, it allows to input a customised gltrans.narrative, supptrans.suppreference and supptrans.transtext:
  	// Info to be inserted on `gltrans`.`narrative` varchar(200):
  	if(!isset($_POST['gltrans_narrative'])) {
  		$_POST['gltrans_narrative'] = '';
  	}
  	echo '<tr>
  			<td>', _('Supplier Narrative'), ':</td>
  			<td><input class="text" maxlength="200" name="gltrans_narrative" size="52" type="text" value="', stripslashes($_POST['gltrans_narrative']), '" /> ', _('Supplier narrative in general ledger transactions. If blank, it uses the bank narrative.'), '</td>
  		</tr>';
  	// Info to be inserted on `supptrans`.`suppreference` varchar(20):
  	if(!isset($_POST['supptrans_suppreference'])) {
  		$_POST['supptrans_suppreference'] = '';
  	}
  	echo '<tr>
  			<td>', _('Supplier Reference'), ':</td>
  			<td><input class="text" maxlength="20" name="supptrans_suppreference" size="22" type="text" value="', stripslashes($_POST['supptrans_suppreference']), '" /> ', _('Supplier reference in supplier transactions. If blank, it uses the payment type.'), '</td>
  		</tr>';
  	// Info to be inserted on `supptrans`.`transtext` text:
  	if(!isset($_POST['supptrans_transtext'])) {
  		$_POST['supptrans_transtext'] = '';
  	}
  	echo '<tr>
  			<td>', _('Transaction Text'), ':</td>
  			<td><input class="text" maxlength="200" name="supptrans_transtext" size="52" type="text" value="', stripslashes($_POST['supptrans_transtext']), '" /> ', _('Transaction text in supplier transactions. If blank, it uses the bank narrative.'), '</td>
  		</tr>';

  	echo '<tr>
  			<td>',
  				_('Amount of Payment'), ' ', $_SESSION['PaymentDetail'.$identifier]->Currency, ':</td>
  			<td><input id="AmountToPay" readonly class="number" maxlength="12" name="Amount" size="13" type="text" value="', $_SESSION['PaymentDetail'.$identifier]->Amount, '" /></td>
  		</tr>';

  /*	if(isset($_SESSION['PaymentDetail'.$identifier]->SupplierID)) {//included in a if with same condition.*/ /*So it is a supplier payment so show the discount entry item */

  /*	} else {
  		echo '<input type="hidden" name="Discount" value="0" />';
  	}*/
  	echo '</table><br />';
  	echo '<div class="centre"><input type="submit" name="CommitBatch" value="' . _('Accept and Process Payment') . '" /></div>';

  echo '</div>';
  echo '</form>';
include('includes/footer.php');
?>
