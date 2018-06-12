<?php

/* $Id: HrEmploymentCategories.php 7772 2018-04-07 09:30:06Z bagenda $ */

include('includes/session.php');

$Title = _('Employment Leave Groups');

include('includes/header.php');

if (isset($_POST['SelectedName'])){
	$SelectedName = mb_strtoupper($_POST['SelectedName']);
} elseif (isset($_GET['SelectedName'])){
	$SelectedName = mb_strtoupper($_GET['SelectedName']);
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Customer Types') .
	'" alt="" />' . _('Employee Leave Group ') . '</p>';


if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i=1;
	if (mb_strlen($_POST['LeaveGroupName']) >100) {
		$InputError = 1;
		prnMsg(_('The Leave Group  Name  must be 100 characters or less long'),'error');
		$Errors[$i] = 'LeaveGroupName';
		$i++;
	}

	if (mb_strlen($_POST['LeaveGroupName'])==0) {
		$InputError = 1;
		echo '<br />';
		prnMsg(_('The Leave Group Name  must contain at least one character'),'error');
		$Errors[$i] = 'LeaveGroupName';
		$i++;
	}



	$checksql = "SELECT count(*)
		     FROM hremployeeleavegroups
		     WHERE leavegroup_name = '" . $_POST['LeaveGroupName'] . "'";
	$checkresult=DB_query($checksql);
	$checkrow=DB_fetch_row($checkresult);
	if ($checkrow[0]>0 and !isset($SelectedName)) {
		$InputError = 1;
		echo '<br />';
		prnMsg(_('You already have a Leave Grouop').' '.$_POST['LeaveGroupName'],'error');
			$Errors[$i] = 'LeaveGroupName';
		$i++;
	}

	if (isset($SelectedName) AND $InputError !=1) {

		$sql = "UPDATE hremployeeleavegroups
			SET leavegroup_name = '" . $_POST['LeaveGroupName'] . "',
leavegroup_description= '" . $_POST['GroupDescription']. "',
leavegroup_status= '" . $_POST['Status']. "'
			WHERE leavegroup_id = '" .$SelectedName."'";

		$msg = _('The Leave Group Name') . ' ' . $_POST['LeaveGroupName']. ' ' .  _('has been updated');
	} elseif ( $InputError !=1 ) {

		// First check the Name is not being duplicated

		$checkSql = "SELECT count(*)
			     FROM hremployeeleavegroups
			     WHERE leavegroup_name  = '" . $_POST['LeaveGroupName'] . "'";

		$checkresult = DB_query($checkSql);
		$checkrow = DB_fetch_row($checkresult);

		if ( $checkrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The Leave Group Name') . ' ' . $_POST['LeaveGroupName'] . _(' already exist.'),'error');
		} else {

			// Add new record on submit

			$sql = "INSERT INTO hremployeeleavegroups
						(leavegroup_name,
 leavegroup_description,leavegroup_status)
					VALUES ('" . $_POST['LeaveGroupName'] . "',
'" . $_POST['GroupDescription'] . "',
'" . $_POST['Status'] . "'
)";


			$msg = _('Leave Group Name') . ' ' . $_POST["LeaveGroupName"] .  ' ' . _('has been created');
			$checkSql = "SELECT count(leavegroup_id)
			     FROM hremployeeleavegroups";
			$result = DB_query($checkSql);
			$row = DB_fetch_row($result);

		}
	}

	if ( $InputError !=1) {
	//run the SQL from either of the above possibilites
		$result = DB_query($sql);


	// Fetch the default Category list.
		$DefaultGroupName = $_SESSION['DefaultGroupName'];

	// Does it exist
		$checkSql = "SELECT count(*)
			     FROM hremployeeleavegroups
			     WHERE leavegroup_id = '" . $DefaultGroupName . "'"
					 ;
		$checkresult = DB_query($checkSql);
		$checkrow = DB_fetch_row($checkresult);

	// If it doesnt then update config with newly created one.
		if ($checkrow[0] == 0) {
			$sql = "UPDATE config
					SET confvalue='" . $_POST['leavegroup_id'] . "'
					WHERE confname='DefaultGroupName'";
			$result = DB_query($sql);
			$_SESSION['DefaultGroupName'] = $_POST['leavegroup_id'];
		}
		echo '<br />';
		prnMsg($msg,'success');

		unset($SelectedName);
		unset($_POST['leavegroup_id']);
		unset($_POST['LeaveGroupName']);
		unset($_POST['GroupDescription']);
	}

} elseif ( isset($_GET['delete']) ) {

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'EMPLOYEE Positions'


	$sql= "SELECT COUNT(*)
	       FROM hremployeepositions
	       WHERE employee_category_id='".$SelectedName."'";

	$ErrMsg = _('The number of transactions using this Category Name could not be retrieved');
	$result = DB_query($sql,$ErrMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this Category because Employee Positions  have been created using this Category') . '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('Positions using this Category'),'error');

	}

	 else {
			$result = DB_query("SELECT category_name FROM hremployeecategories WHERE employee_category_id='".$SelectedName."'");
			if (DB_Num_Rows($result)>0){
				$NameRow = DB_fetch_array($result);
				$CategoryName = $NameRow['category_name'];

				$sql="DELETE FROM hremployeecategories WHERE employee_category_id='".$SelectedName."'";
				$ErrMsg = _('The Category record could not be deleted because');
				$result = DB_query($sql,$ErrMsg);
				echo '<br />';
				prnMsg(_('Category Name') . ' ' . $CategoryName  . ' ' . _('has been deleted') ,'success');
			}
			unset ($SelectedName);
			unset($_GET['delete']);

	} //end if Positions used in Employees set up
}

if (!isset($SelectedName)){

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedPosition will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of sales types will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT * FROM hremployeeleavegroups";
	$result = DB_query($sql);

	echo '<br /><table class="selection">';
	echo '<tr>
	<th class="ascending">' . _('Leave Group id') . '</th>
 <th class="ascending">' . _('Leave Group Name') . '</th>
 <th class="ascending">' . _('Leave Group Description') . '</th>
<th class="ascending">' . _('Status') . '</th>
		</tr>';

		$k=0; //row colour counter
		while ($myrow = DB_fetch_array($result)) {
     

			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}

printf('<td>%s</td>
		<td>%s</td>
<td>%s</td>
<td>%s</td>

		<td><a href="%sSelectedName=%s">' . _('Edit') . '</a></td>
		<td><a href="%sSelectedName=%s&amp;delete=yes" onclick=\'return confirm("' . _('Are you sure you wish to delete this Category Name?') . '");\'>' . _('Delete') . '</a></td>
		</tr>',
		$myrow['leavegroup_id'],
		$myrow['leavegroup_name'],
		$myrow['leavegroup_description'],
		($myrow['leavegroup_status'] == 1) ? 'Active' : 'Inactive',
		htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
		$myrow['leavegroup_id'],
		htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
		$myrow['hleavegroup_id']);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!
if (isset($SelectedName)) {

	echo '<div class="centre"><br /><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Show All Types Defined') . '</a></div>';
}
if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<br />';

	// The user wish to EDIT an existing name
	if ( isset($SelectedName) AND $SelectedName!='' ) {

		$sql = "SELECT *
		        FROM hremployeeleavegroups
		        WHERE leavegroup_id='".$SelectedName."'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['leavegroup_id'] = $myrow['leavegroup_id'];
		$_POST['LeaveGroupName']  = $myrow['leavegroup_name'];
		$_POST['GroupDescription']  = $myrow['leavegroup_description'];

$_POST['status']  = $myrow['leavegroup_status'];
		echo '<input type="hidden" name="SelectedName" value="' . $SelectedName . '" />
			<input type="hidden" name="leavegroup_id" value="' . $_POST['leavegroup_id'] . '" />
			<table class="selection">';

		// We dont allow the user to change an existing Name code

		echo '<tr>
				<td>' . _('Leave Group ID') . ': ' . $_POST['leavegroup_id'] . '</td>
			</tr>';
	} else 	{
		// This is a new Name so the user may volunteer a Name code
		echo '<table class="selection">';
	}

	if (!isset($_POST['LeaveGroupName'])) {
		$_POST['LeaveGroupName']='';
	}
	echo '<tr>
			<td>' . _('Leave Group Name') . ':</td>
			<td><input type="text" name="LeaveGroupName"  required="required" title="' . _('The Leave Group Name is required') . '" value="' . $_POST['LeaveGroupName'] . '" /></td>
		</tr>
		<tr>
				<td>' . _('Description ') . ':</td>
				<td>
<textarea name="GroupDescription"  title="' . _('The  Description is required') . '">' . $_POST['GroupDescription'] . '</textarea>
				</td>
			</tr>

			<tr><td><label for="Status">' . _('Status') .
			  ':</label></td>
			  <td><input type="radio"';
				if (! isset($SelectedName)) {
			   echo ' checked';}
			  if (isset($_POST['status']) and $_POST['status']==1) {
			    echo ' checked';}
			echo'
			   name="Status" value="1"> Active

			  <input';
			  if (isset($_POST['status']) and $_POST['status']==0) {
			    echo ' checked';
			  }
			echo'

			  type="radio" name="Status" value="0"> Inactive
			  </td></tr>


		</table>
		<br />
		<div class="centre">
			<input type="submit" name="submit" value="' . _('Accept') . '" />
		</div>
	</div>
	</form>';

} // end if user wish to delete

include('includes/footer.php');
?>
