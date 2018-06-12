<?php

/* $Id: PO_AuthorisationLevels.php 7751 2017-04-13 16:34:26Z rchacon $*/

include('includes/session.php');

$Title = _('Payment Voucher Authorisation Maintenance');
include('includes/header.php');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';


/*Note: If CanCreate==0 then this means the user can create orders
 *     Also if canapprove==0 then the user can release purchase invocies
 *     This logic confused me a bit to start with
 */


if (isset($_POST['Submit'])) {
	if (isset($_POST['CanCreate']) AND $_POST['CanCreate']=='on') {
		$CanCreate=1;
	} else {
		$CanCreate=0;
	}
	if (isset($_POST['canapprove']) AND $_POST['canapprove']=='on') {
		$canapprove=1;
	} else {
		$canapprove=0;
	}
	if ($_POST['AuthLevel']=='') {
		$_POST['AuthLevel']=0;
	}
	$sql="SELECT COUNT(*)
		FROM paymentvoucherauth
		WHERE userid='" . $_POST['UserID'] . "'
		AND currabrev='" . $_POST['CurrCode'] . "'";
	$result=DB_query($sql);
	$myrow=DB_fetch_array($result);
	if ($myrow[0]==0) {
		$sql="INSERT INTO paymentvoucherauth ( userid,
						currabrev,
						cancreate,
						canapprove,
						authlevel)
					VALUES( '".$_POST['UserID']."',
						'".$_POST['CurrCode']."',
						'".$CanCreate."',
						'".$canapprove."',
						'" . filter_number_format($_POST['AuthLevel'])."')";
	$ErrMsg = _('The authentication details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	} else {
		prnMsg(_('There already exists an entry for this user/currency combination'), 'error');
		echo '<br />';
	}
}

if (isset($_POST['Update'])) {
	if (isset($_POST['CanCreate']) AND $_POST['CanCreate']=='on') {
		$CanCreate=1;
	} else {
		$CanCreate=0;
	}
	if (isset($_POST['canapprove']) AND $_POST['canapprove']=='on') {
		$canapprove=1;
	} else {
		$canapprove=0;
	}
	$sql="UPDATE paymentvoucherauth SET
			cancreate='".$CanCreate."',
			canapprove='".$canapprove."',
			authlevel='".filter_number_format($_POST['AuthLevel'])."'
			WHERE userid='".$_POST['UserID']."'
			AND currabrev='".$_POST['CurrCode']."'";

	$ErrMsg = _('The authentication details cannot be updated because');
	$Result=DB_query($sql,$ErrMsg);
}

if (isset($_GET['Delete'])) {
	$sql="DELETE FROM paymentvoucherauth
		WHERE userid='".$_GET['UserID']."'
		AND currabrev='".$_GET['Currency']."'";

	$ErrMsg = _('The authentication details cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
}

if (isset($_GET['Edit'])) {
	$sql="SELECT cancreate,
				canapprove,
				authlevel
			FROM paymentvoucherauth
			WHERE userid='".$_GET['UserID']."'
			AND currabrev='".$_GET['Currency']."'";
	$ErrMsg = _('The authentication details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	$UserID=$_GET['UserID'];
	$Currency=$_GET['Currency'];
	$CanCreate=$myrow['CanCreate'];
	$canapprove=$myrow['canapprove'];
	$AuthLevel=$myrow['authlevel'];
}

$sql="SELECT paymentvoucherauth.userid,
			www_users.realname,
			currencies.currabrev,
			currencies.currency,
			currencies.decimalplaces,
			paymentvoucherauth.cancreate,
			paymentvoucherauth.canapprove,
			paymentvoucherauth.authlevel
	FROM paymentvoucherauth INNER JOIN www_users
		ON paymentvoucherauth.userid=www_users.userid
	INNER JOIN currencies
		ON paymentvoucherauth.currabrev=currencies.currabrev";

$ErrMsg = _('The authentication details cannot be retrieved because');
$Result=DB_query($sql,$ErrMsg);

echo '<table class="selection">
     <tr>
		<th>' . _('User ID') . '</th>
		<th>' . _('User Name') . '</th>
		<th>' . _('Currency') . '</th>
		<th>' . _('Create Voucher') . '</th>
		<th>' . _('Can Approve') . '<br />' .  _('Payments') . '</th>
		<th>' . _('Authority Level') . '</th>
    </tr>';

while ($myrow=DB_fetch_array($Result)) {
	if ($myrow['cancreate']==1) {
		$DisplayCanCreate=_('Yes');
	} else {
		$DisplayCanCreate=_('No');
	}
	if ($myrow['canapprove']==1) {
		$Displaycanapprove=_('Yes');
	} else {
		$Displaycanapprove=_('No');
	}
	echo '<tr>
			<td>' . $myrow['userid'] . '</td>
			<td>' . $myrow['realname'] . '</td>
			<td>' . $myrow['currency'] . '</td>
			<td>' . $DisplayCanCreate . '</td>
			<td>' . $Displaycanapprove . '</td>
			<td class="number">' . locale_number_format($myrow['authlevel'],$myrow['decimalplaces']) . '</td>
			<td><a href="'.$RootPath.'/PV_AuthorisationLevels.php?Edit=Yes&amp;UserID=' . $myrow['userid'] .
	'&amp;Currency='.$myrow['currabrev'].'">' . _('Edit') . '</a></td>
			<td><a href="'.$RootPath.'/PV_AuthorisationLevels.php?Delete=Yes&amp;UserID=' . $myrow['userid'] .
	'&amp;Currency='.$myrow['currabrev'].'" onclick="return confirm(\'' . _('Are you sure you wish to delete this authorisation level?') . '\');">' . _('Delete') . '</a></td>
		</tr>';
}

echo '</table><br /><br />';

if (!isset($_GET['Edit'])) {
	$UserID=$_SESSION['UserID'];
	$Currency=$_SESSION['CompanyRecord']['currencydefault'];
	$CanCreate=0;
	$canapprove=0;
	$AuthLevel=0;
}

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post" id="form1">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">';

if (isset($_GET['Edit'])) {
	echo '<tr><td>' . _('User ID') . '</td><td>' . $_GET['UserID'] . '</td></tr>';
	echo '<input type="hidden" name="UserID" value="'.$_GET['UserID'].'" />';
} else {
	echo '<tr><td>' . _('User ID') . '</td><td><select name="UserID">';
	$usersql="SELECT userid FROM www_users";
	$userresult=DB_query($usersql);
	while ($myrow=DB_fetch_array($userresult)) {
		if ($myrow['userid']==$UserID) {
			echo '<option selected="selected" value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
		} else {
			echo '<option value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
		}
	}
	echo '</select></td></tr>';
}

if (isset($_GET['Edit'])) {
	$sql="SELECT cancreate,
				canapprove,
				authlevel,
				currency,
				decimalplaces
			FROM paymentvoucherauth INNER JOIN currencies
			ON paymentvoucherauth.currabrev=currencies.currabrev
			WHERE userid='".$_GET['UserID']."'
			AND paymentvoucherauth.currabrev='".$_GET['Currency']."'";
	$ErrMsg = _('The authentication details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	$UserID=$_GET['UserID'];
	$Currency=$_GET['Currency'];
	$CanCreate=$myrow['cancreate'];
	$canapprove=$myrow['canapprove'];
	$AuthLevel=$myrow['authlevel'];
	$CurrDecimalPlaces=$myrow['decimalplaces'];

	echo '<tr>
			<td>' . _('Currency') . '</td>
			<td>' . $myrow['currency'] . '</td>
		</tr>';
	echo '<input type="hidden" name="CurrCode" value="'.$Currency.'" />';
} else {
	echo '<tr>
			<td>' . _('Currency') . '</td>
			<td><select name="CurrCode">';
	$currencysql="SELECT currabrev,currency FROM currencies";
	$currencyresult=DB_query($currencysql);
	while ($myrow=DB_fetch_array($currencyresult)) {
		if ($myrow['currabrev']==$Currency) {
			echo '<option selected="selected" value="'.$myrow['currabrev'].'">' . $myrow['currency'] . '</option>';
		} else {
			echo '<option value="'.$myrow['currabrev'].'">' . $myrow['currency'] . '</option>';
		}
	}
	echo '</select></td></tr>';
}

echo '<tr>
		<td>' . _('User can create Vouchers') . '</td>';
if ($CanCreate==1) {
	echo '<td><input type="checkbox" checked="checked" name="CanCreate" /></td>
		</tr>';
} else {
	echo '<td><input type="checkbox"  name="CanCreate" /></td>
		</tr>';
}

echo '<tr>
		<td>' . _('User can Approve payments') . '</td>';
if ($canapprove==1) {
	echo '<td><input type="checkbox" checked="checked" name="canapprove" /></td>
		</tr>';
} else {
	echo '<td><input type="checkbox"  name="canapprove" /></td>
		</tr>';
}

echo '<tr>
		<td>' . _('User can authorise orders up to :') . '</td>';
echo '<td><input type="text" name="AuthLevel" size="11" class="integer" title="' . _('Enter the amount that this user is premitted to authorise purchase orders up to') . '" value="'  . locale_number_format($AuthLevel,$CurrDecimalPlaces) . '" /></td>
	</tr>
	</table>';

if (isset($_GET['Edit'])) {
	echo '<br />
			<div class="centre">
				<input type="submit" name="Update" value="'._('Update Information').'" />
			</div>';
} else {
	echo '<br />
		<div class="centre">
			<input type="submit" name="Submit" value="'._('Enter Information').'" />
		</div>';
}
echo '</div>
        </form>';
include('includes/footer.php');
?>
