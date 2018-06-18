<?php
/* $Id: HrSelectEmployee.php 7751 2018-04-13 16:34:26Z raymond $*/
/* Search for employees  */

include('includes/session.php');
$Title = _('Timesheets Report');
$ViewTopic = 'ProjectAccounting';
$BookMark = 'Timesheets Report';
include('includes/header.php');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>
	<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
	<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


		// BEGIN: Leave Types  array.
		$LeaveTypes = array();
		$Query = "SELECT 	hrleavetype_id, leavetype_name FROM hremployeeleavetypes WHERE leavetype_status =1 ";
		$Result = DB_query($Query);
		while ($Row = DB_fetch_array($Result)) {
		$LeaveTypes[$Row['hrleavetype_id']] = $Row['leavetype_name'];
		}

    if (isset($_GET['Report'])) {
    	$SelectedReport = $_GET['Report'];
    } elseif (isset($_POST['Report'])){
    	$SelectedReport = $_POST['Report'];
    } else {
    	unset($SelectedReport);
    }

		if (isset($_GET['FromDate'])) {
			$SelectedStartDate = $_GET['FromDate'];
		} elseif (isset($_POST['FromDate'])){
			$SelectedStartDate = $_POST['FromDate'];
		} else {
			unset($SelectedStartDate);
		}

		if (isset($_GET['ToDate'])) {
			$SelectedEndtDate = $_GET['ToDate'];
		} elseif (isset($_POST['ToDate'])){
			$SelectedEndDate = $_POST['ToDate'];
		} else {
			unset($SelectedStartDate);
		}

//    if (!isset($EN) or ($EN=='')){
      echo '<table class="selection"><tr><td>';

    echo  _('Employee Timesheet ') . ':<select name="Report">
    <option value="time" '._(($SelectedReport=='time')?' selected ':' ').'>Time</option>
		<option value="timebycustomer" '._(($SelectedReport=='timebycustomer')?' selected ':' ').'>Time By Customer</option>
		<option value="timebyproject" '._(($SelectedReport=='timebyproject')?' selected ':' ').'>Time By Project</option>
		<option value="timebytask" '._(($SelectedReport=='timebytask')?' selected ':' ').'>Time By Task</option>
		<option value="project" '._(($SelectedReport=='project')?' selected ':' ').'>Project Time</option>
		';

  		echo '</select> &nbsp;&nbsp'._('From') . ':
<input type="text" name="FromDate" required="required"  value="'.$SelectedStartDate.'" class="datepicker" maxlength="10" size="20"  />&nbsp';
echo '</select> &nbsp;&nbsp'._('To') . ':
<input type="text" name="ToDate" required="required" value="'.$SelectedEndDate.'" class="datepicker" maxlength="10" size="20"  />&nbsp';


      echo '<input type="submit" name="GenerateReport" value="' . _('Generate') . '" />
  			</td>
  			</tr>
  			</table>
  			<br />
				</div>
        </form>';

				echo "<script>
								$( document ).ready(function() {
										//create date.
										//get format.
										var date_format = '".$_SESSION['DefaultDateFormat']."';
										var new_date_format = date_format.replace('Y', 'yy');
										$('.datepicker').datepicker({
												changeMonth: true,
												changeYear: true,
												showButtonPanel: true,
												dateFormat: new_date_format
										});
								});

						</script>";

//    }
    if(isset($_POST['GenerateReport'])) {

		if($_POST['Report']=='time'){

    	echo '<table class="selection">
    			<tr>
					<th class="ascending">' . _('Subtotal name') . '</th>
					<th class="ascending">' . _('Begin Date') . '</th>
					<th class="ascending">' . _('End Date') . '</th>
	<th class="ascending">' . _('Project ID') . '</th>
	<th class="ascending">' . _('Project Name') . '</th>
<th class="ascending">' . _('Customer Name') . '</th>
<th class="ascending">' . _('Task Name') . '</th>
<th class="ascending">' . _('Service Offered') . '</th>
<th class="ascending">' . _('Duration') . '</th>';
echo'</tr>';

						$sqlemployeelist= "SELECT

						DISTINCT(patimesheetsinfo.employee_id),
						patimesheetsinfo.employee_id As employeeid,
						first_name,
						middle_name,
						last_name
						FROM patimesheetsinfo
						JOIN hremployees ON patimesheetsinfo.employee_id=hremployees.empid
						";
						$ErrMsg = _('The employee could not be loaded because');
						$DbgMsg = _('The SQL that was used to get the employees and failed was');
						$employeelists = DB_query($sqlemployeelist,$ErrMsg,$DbgMsg);

						while($myrowemployeelists = DB_fetch_array($employeelists))
						{

$sumduration=0;
					echo'<tr>	<td colspan="6"><b>'.$myrowemployeelists['first_name'].' '.$myrowemployeelists['middle_name'].' '.$myrowemployeelists['last_name'].'</b></td></tr>';

					$sqltime= "SELECT
					patimesheetsinfo.begin_date As begindate,
					patimesheetsinfo.end_date As enddate,
					created_date,
					timesheet_status,
					 sun,
					 mon,
					 tue,
					 wed,
					 thu,
					 fri,
					 sat,
					 projecttask_name,
					 project_name,
					 name,
					 description,
					patimesheetentries.project_id As projectid
					FROM patimesheetentries
					JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
					JOIN paprojecttasks ON patimesheetentries.projecttask_id=paprojecttasks.projecttask_id
					JOIN paprojects ON patimesheetentries.project_id=paprojects.id
					Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
					JOIN stockmaster on paprojecttasks.servicetask = stockmaster.stockid
					WHERE employee_id ='".$myrowemployeelists['employeeid']."'
          AND patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
					'".date('Y-m-d',strtotime($_POST['ToDate']))."'
					";
					$ErrMsg = _('The employee could not be loaded because');
					$DbgMsg = _('The SQL that was used to get the employees and failed was');
					$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

					while($myrowtime = DB_fetch_array($resulttime))
					{
	$totalduration=$myrowtime['sun']+$myrowtime['mon']+$myrowtime['tue']+$myrowtime['wed']+$myrowtime['thu']+$myrowtime['fri']+$myrowtime['sat'];
echo'<tr><td></td>

<td>'.$myrowtime['begindate'].'</td>
<td>'.date('Y-m-d',strtotime($myrowtime['created_date'])).'</td>
<td>'.$myrowtime['projectid'].'</td>
<td>'.$myrowtime['project_name'].'</td>
<td>'.$myrowtime['name'].'</td>
<td>'.$myrowtime['projecttask_name'].'</td>
<td>'.$myrowtime['description'].'</td>
<td>'.$totalduration.'</td>
</tr>';
$sumduration=$sumduration+$totalduration;
					}
echo'<tr><td colspan="8">Total Duration </td><td>'.$sumduration.'</td></tr>
<tr><td td colspan="9"><hr></td></tr>';
$grandtotaltime= $grandtotaltime+$sumduration;
}
echo'<tr><td colspan="8">Grand Total Duration </td><td>'.$grandtotaltime.'</td></tr>';




    	echo '</table>';

}elseif($_POST['Report']=='timebycustomer'){

	echo '<table class="selection">
			<tr>
			<th class="ascending">' . _('Subtotal name') . '</th>
			<th class="ascending">' . _('Begin Date') . '</th>
			<th class="ascending">' . _('End Date') . '</th>
	<th class="ascending">' . _('Project ID') . '</th>
	<th class="ascending">' . _('Project Name') . '</th>

	<th class="ascending">' . _('Task Name') . '</th>
	<th class="ascending">' . _('Service Offered') . '</th>
	<th class="ascending">' . _('Duration') . '</th>
	<th class="ascending">' . _('Billable') . '</th>
	<th class="ascending">' . _('Billed') . '</th>
	';
	echo'</tr>';

				$sqlemployeelist= "SELECT

				DISTINCT(patimesheetsinfo.employee_id),
				patimesheetsinfo.employee_id As employeeid,
				first_name,
				middle_name,
				last_name
				FROM patimesheetsinfo
				JOIN hremployees ON patimesheetsinfo.employee_id=hremployees.empid
				";
				$ErrMsg = _('The employee could not be loaded because');
				$DbgMsg = _('The SQL that was used to get the employees and failed was');
				$employeelists = DB_query($sqlemployeelist,$ErrMsg,$DbgMsg);

				while($myrowemployeelists = DB_fetch_array($employeelists))
				{


echo'<tr>	<td colspan="6"><b>'.$myrowemployeelists['first_name'].' '.$myrowemployeelists['middle_name'].' '.$myrowemployeelists['last_name'].'</b></td></tr>';


$sqlcustomerlists="SELECT DISTINCT(debtorno),name
FROM paprojects
Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
";
$resultcustomerlists = DB_query($sqlcustomerlists);
$subtotalcustomer=0;
while($myrowcustomerlists = DB_fetch_array($resultcustomerlists))
{

	$sumduration=0;


			$sqltime= "SELECT
			patimesheetsinfo.begin_date As begindate,
			patimesheetsinfo.end_date As enddate,
			created_date,
			timesheet_status,
			 sun,
			 mon,
			 tue,
			 wed,
			 thu,
			 fri,
			 sat,
			 projecttask_name,
			 project_name,
			 name,
			 description,
			 taskbillable,
			 sheet_billed,
			patimesheetentries.project_id As projectid
			FROM patimesheetentries
			JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
			JOIN paprojecttasks ON patimesheetentries.projecttask_id=paprojecttasks.projecttask_id
			JOIN paprojects ON patimesheetentries.project_id=paprojects.id
			Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
			JOIN stockmaster on paprojecttasks.servicetask = stockmaster.stockid
			WHERE employee_id ='".$myrowemployeelists['employeeid']."'
			AND customer ='".$myrowcustomerlists['debtorno']."'
			AND patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
			'".date('Y-m-d',strtotime($_POST['ToDate']))."'
			";
			$ErrMsg = _('The employee could not be loaded because');
			$DbgMsg = _('The SQL that was used to get the employees and failed was');
			$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

if(DB_num_rows($resulttime)!=0){

	echo'<tr><td></td><td colspan="6"><b>Customer: ' .$myrowcustomerlists['name'].'</b></td></tr>';


				while($myrowtime = DB_fetch_array($resulttime))
				{
		$totalduration=$myrowtime['sun']+$myrowtime['mon']+$myrowtime['tue']+$myrowtime['wed']+$myrowtime['thu']+$myrowtime['fri']+$myrowtime['sat'];
		echo'<tr><td></td>

		<td>'.$myrowtime['begindate'].'</td>
		<td>'.date('Y-m-d',strtotime($myrowtime['created_date'])).'</td>
		<td>'.$myrowtime['projectid'].'</td>
		<td>'.$myrowtime['project_name'].'</td>
		<td>'.$myrowtime['projecttask_name'].'</td>
		<td>'.$myrowtime['description'].'</td>
		<td>'.$totalduration.'</td>
		<td>'._(($myrowtime['taskbillable']==1)? 'YES':' NO ').'</td>
		<td>'._(($myrowtime['sheet_billed']==1)? 'YES':' NO ').'</td>
		</tr>';
		$sumduration=$sumduration+$totalduration;
				}
				$subtotalcustomer=$subtotalcustomer+$sumduration;
		echo'
		<tr><td></td><td colspan="6">Total Duration </td><td>'.$sumduration.'</td></tr>

		<tr><td></td><td colspan="9"><hr> </td></tr>
		';
}

}

echo'
<tr><td></td><td colspan="6">Sub Total Duration </td><td>'.$subtotalcustomer.'</td></tr>
<tr><td td colspan="10"><hr></td></tr>

';
$grandtotaldurationcustomer =$grandtotaldurationcustomer+$subtotalcustomer;
	}


echo'<tr><td colspan="7">Grand Total Duration </td><td>'.$grandtotaldurationcustomer.'</td></tr>';


	echo '</table>';


}elseif($_POST['Report']=='timebyproject'){


		echo '<table class="selection">
				<tr>
				<th class="ascending">' . _('Subtotal name') . '</th>
				<th class="ascending">' . _('Begin Date') . '</th>
				<th class="ascending">' . _('End Date') . '</th>
		<th class="ascending">' . _('Project ID') . '</th>
		<th class="ascending">' . _('Customer Name') . '</th>
		<th class="ascending">' . _('Task Name') . '</th>
		<th class="ascending">' . _('Service Offered') . '</th>
		<th class="ascending">' . _('Duration') . '</th>
		<th class="ascending">' . _('Billable') . '</th>
		<th class="ascending">' . _('Billed') . '</th>
		';
		echo'</tr>';

					$sqlemployeelist= "SELECT

					DISTINCT(patimesheetsinfo.employee_id),
					patimesheetsinfo.employee_id As employeeid,
					first_name,
					middle_name,
					last_name
					FROM patimesheetsinfo
					JOIN hremployees ON patimesheetsinfo.employee_id=hremployees.empid
					";
					$ErrMsg = _('The employee could not be loaded because');
					$DbgMsg = _('The SQL that was used to get the employees and failed was');
					$employeelists = DB_query($sqlemployeelist,$ErrMsg,$DbgMsg);

					while($myrowemployeelists = DB_fetch_array($employeelists))
					{


	echo'<tr>	<td colspan="6"><b>'.$myrowemployeelists['first_name'].' '.$myrowemployeelists['middle_name'].' '.$myrowemployeelists['last_name'].'</b></td></tr>';


	$sqlprojectslists="SELECT project_name,project_id
	FROM paprojects
	";
	$resultprojectlists = DB_query($sqlprojectslists);
	$subtotalcustomer=0;
	while($myrowprojectlists = DB_fetch_array($resultprojectlists))
	{

		$sumduration=0;


				$sqltime= "SELECT
				patimesheetsinfo.begin_date As begindate,
				patimesheetsinfo.end_date As enddate,
				created_date,
				timesheet_status,
				 sun,
				 mon,
				 tue,
				 wed,
				 thu,
				 fri,
				 sat,
				 projecttask_name,
				 project_name,
				 name,
				 description,
				 taskbillable,
				 sheet_billed,
				patimesheetentries.project_id As projectid
				FROM patimesheetentries
				JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
				JOIN paprojecttasks ON patimesheetentries.projecttask_id=paprojecttasks.projecttask_id
				JOIN paprojects ON patimesheetentries.project_id=paprojects.id
				Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
				JOIN stockmaster on paprojecttasks.servicetask = stockmaster.stockid
				WHERE employee_id ='".$myrowemployeelists['employeeid']."'
				AND paprojects.project_id ='".$myrowprojectlists['project_id']."'
				AND patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
				'".date('Y-m-d',strtotime($_POST['ToDate']))."'
				";
				$ErrMsg = _('The employee could not be loaded because');
				$DbgMsg = _('The SQL that was used to get the employees and failed was');
				$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

  if(DB_num_rows($resulttime)!=0){
		echo'<tr><td></td><td colspan="6"><b>Project Name: ' .$myrowprojectlists['project_name'].'</b></td></tr>';


		while($myrowtime = DB_fetch_array($resulttime))
		{

$totalduration=$myrowtime['sun']+$myrowtime['mon']+$myrowtime['tue']+$myrowtime['wed']+$myrowtime['thu']+$myrowtime['fri']+$myrowtime['sat'];
echo'<tr><td></td>

<td>'.$myrowtime['begindate'].'</td>
<td>'.date('Y-m-d',strtotime($myrowtime['created_date'])).'</td>
<td>'.$myrowtime['projectid'].'</td>
<td>'.$myrowtime['name'].'</td>
<td>'.$myrowtime['projecttask_name'].'</td>
<td>'.$myrowtime['description'].'</td>
<td>'.$totalduration.'</td>
<td>'._(($myrowtime['taskbillable']==1)? 'YES':' NO ').'</td>
<td>'._(($myrowtime['sheet_billed']==1)? 'YES':' NO ').'</td>
</tr>';
$sumduration=$sumduration+$totalduration;


		}

		$subtotalcustomer=$subtotalcustomer+$sumduration;
echo'
<tr><td></td><td colspan="6">Total Duration </td><td>'.$sumduration.'</td></tr>

<tr><td></td><td colspan="9"><hr> </td></tr>
';


	}


	}

	echo'
	<tr><td></td><td colspan="6">Sub Total Duration </td><td>'.$subtotalcustomer.'</td></tr>
	<tr><td td colspan="10"><hr></td></tr>

	';
	$grandtotaldurationcustomer =$grandtotaldurationcustomer+$subtotalcustomer;
		}


	echo'<tr><td colspan="7">Grand Total Duration </td><td>'.$grandtotaldurationcustomer.'</td></tr>';


		echo '</table>';
}elseif($_POST['Report']=='timebytask') {


			echo '<table class="selection">
					<tr>
					<th class="ascending">' . _('Subtotal name') . '</th>
					<th class="ascending">' . _('Begin Date') . '</th>
					<th class="ascending">' . _('End Date') . '</th>
			<th class="ascending">' . _('Project ID') . '</th>
			<th class="ascending">' . _('Project Name') . '</th>
			<th class="ascending">' . _('Service Offered') . '</th>
			<th class="ascending">' . _('Duration') . '</th>
			<th class="ascending">' . _('Billable') . '</th>
			<th class="ascending">' . _('Billed') . '</th>
			';
			echo'</tr>';

						$sqlemployeelist= "SELECT

						DISTINCT(patimesheetsinfo.employee_id),
						patimesheetsinfo.employee_id As employeeid,
						first_name,
						middle_name,
						last_name
						FROM patimesheetsinfo
						JOIN hremployees ON patimesheetsinfo.employee_id=hremployees.empid
						";
						$ErrMsg = _('The employee could not be loaded because');
						$DbgMsg = _('The SQL that was used to get the employees and failed was');
						$employeelists = DB_query($sqlemployeelist,$ErrMsg,$DbgMsg);

						while($myrowemployeelists = DB_fetch_array($employeelists))
						{


		echo'<tr>	<td colspan="6"><b>'.$myrowemployeelists['first_name'].' '.$myrowemployeelists['middle_name'].' '.$myrowemployeelists['last_name'].'</b></td></tr>';


		$sqltasklists="SELECT DISTINCT(patimesheetentries.projecttask_id),
		patimesheetentries.projecttask_id As taskid,projecttask_name

		FROM patimesheetentries
		Join paprojecttasks ON patimesheetentries.projecttask_id =paprojecttasks.projecttask_id
		JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
		Where patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
		'".date('Y-m-d',strtotime($_POST['ToDate']))."'
		";
		$resulttasklists = DB_query($sqltasklists);


		$subtotalcustomer=0;
		while($myrowtasklists = DB_fetch_array($resulttasklists))
		{

			$sumduration=0;


					$sqltime= "SELECT
					patimesheetsinfo.begin_date As begindate,
					patimesheetsinfo.end_date As enddate,
					created_date,
					timesheet_status,
					 sun,
					 mon,
					 tue,
					 wed,
					 thu,
					 fri,
					 sat,
					 projecttask_name,
					 project_name,
					 name,
					 description,
					 taskbillable,
					 sheet_billed,
					patimesheetentries.project_id As projectid
					FROM patimesheetentries
					JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
					JOIN paprojecttasks ON patimesheetentries.projecttask_id=paprojecttasks.projecttask_id
					JOIN paprojects ON patimesheetentries.project_id=paprojects.id
					Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
					JOIN stockmaster on paprojecttasks.servicetask = stockmaster.stockid
					WHERE employee_id ='".$myrowemployeelists['employeeid']."'
					AND paprojecttasks.projecttask_id ='".$myrowtasklists['taskid']."'
					AND patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
					'".date('Y-m-d',strtotime($_POST['ToDate']))."'
					";
					$ErrMsg = _('The employee could not be loaded because');
					$DbgMsg = _('The SQL that was used to get the employees and failed was');
					$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

          if(DB_num_rows($resulttime)!=0){
						echo'<tr><td></td><td colspan="6"><b>Task Name: ' .$myrowtasklists['projecttask_name'].'</b></td></tr>';

						while($myrowtime = DB_fetch_array($resulttime))
						{
						$totalduration=$myrowtime['sun']+$myrowtime['mon']+$myrowtime['tue']+$myrowtime['wed']+$myrowtime['thu']+$myrowtime['fri']+$myrowtime['sat'];
						echo'<tr><td></td>

						<td>'.$myrowtime['begindate'].'</td>
						<td>'.date('Y-m-d',strtotime($myrowtime['created_date'])).'</td>
						<td>'.$myrowtime['projectid'].'</td>
						<td>'.$myrowtime['project_name'].'</td>
						<td>'.$myrowtime['description'].'</td>
						<td>'.$totalduration.'</td>
						<td>'._(($myrowtime['taskbillable']==1)? 'YES':' NO ').'</td>
						<td>'._(($myrowtime['sheet_billed']==1)? 'YES':' NO ').'</td>
						</tr>';
						$sumduration=$sumduration+$totalduration;
						}
						$subtotalcustomer=$subtotalcustomer+$sumduration;
				echo'
				<tr><td></td><td colspan="6">Total Duration </td><td>'.$sumduration.'</td></tr>

				<tr><td></td><td colspan="9"><hr> </td></tr>
				';

					}


		}

		echo'
		<tr><td></td><td colspan="6">Sub Total Duration </td><td>'.$subtotalcustomer.'</td></tr>
		<tr><td td colspan="10"><hr></td></tr>

		';
		$grandtotaldurationcustomer =$grandtotaldurationcustomer+$subtotalcustomer;
			}


		echo'<tr><td colspan="7">Grand Total Duration </td><td>'.$grandtotaldurationcustomer.'</td></tr>';


			echo '</table>';
}elseif ($_POST['Report']=='project') {


				echo '<table class="selection">
						<tr>
						<th class="ascending">' . _('Subtotal name') . '</th>
						<th class="ascending">' . _('Begin Date') . '</th>
						<th class="ascending">' . _('Entry Date') . '</th>

				<th class="ascending">' . _('Task Name') . '</th>
				<th class="ascending">' . _('Service Offered') . '</th>

				<th class="ascending">' . _('Employee ID') . '</th>
				<th class="ascending">' . _('Employee Name') . '</th>


				<th class="ascending">' . _('Duration') . '</th>
				<th class="ascending">' . _('Billable') . '</th>
				<th class="ascending">' . _('Billed') . '</th>
				';
				echo'</tr>';

							$sqlprojectlist= "SELECT
              project_name,
							project_id,
              name
							FROM paprojects
							Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
							";
							$ErrMsg = _('The Projects could not be loaded because');
							$DbgMsg = _('The SQL that was used to get the projects and failed was');
							$projectlists = DB_query($sqlprojectlist,$ErrMsg,$DbgMsg);

							while($myrowprojectlists = DB_fetch_array($projectlists))
							{


			echo'<tr>	<td colspan="6"><b>'.$myrowprojectlists['project_name'].' for '.$myrowprojectlists['name'].'</b></td></tr>';

						$sqltime= "SELECT
						patimesheetsinfo.begin_date As begindate,
						patimesheetsinfo.end_date As enddate,
						created_date,
						timesheet_status,
						 sun,
						 mon,
						 tue,
						 wed,
						 thu,
						 fri,
						 sat,
						 projecttask_name,
						 project_name,
						 name,
						 description,
						 taskbillable,
						 sheet_billed,
						 patimesheetsinfo.employee_id As employeeid,
						 first_name,
						 middle_name,
						 last_name,
						patimesheetentries.project_id As projectid
						FROM patimesheetentries
						JOIN patimesheetsinfo ON patimesheetentries.timesheetinfo_id=patimesheetsinfo.timesheetsinfo_id
						JOIN paprojecttasks ON patimesheetentries.projecttask_id=paprojecttasks.projecttask_id
						JOIN paprojects ON patimesheetentries.project_id=paprojects.id
						Join debtorsmaster ON paprojects.customer=debtorsmaster.debtorno
						JOIN stockmaster on paprojecttasks.servicetask = stockmaster.stockid
						JOIN hremployees ON patimesheetsinfo.employee_id=hremployees.empid
						WHERE  paprojects.project_id ='".$myrowprojectlists['project_id']."'
						AND patimesheetsinfo.begin_date Between '".date('Y-m-d',strtotime($_POST['FromDate']))."' AND
						'".date('Y-m-d',strtotime($_POST['ToDate']))."'
						";
						$ErrMsg = _('The Project could not be loaded because');
						$DbgMsg = _('The SQL that was used to get the Projects and failed was');
						$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

	          if(DB_num_rows($resulttime)!=0){

											$sumduration=0;

							while($myrowtime = DB_fetch_array($resulttime))
							{
							$totalduration=$myrowtime['sun']+$myrowtime['mon']+$myrowtime['tue']+$myrowtime['wed']+$myrowtime['thu']+$myrowtime['fri']+$myrowtime['sat'];
							echo'<tr><td></td>

							<td>'.$myrowtime['begindate'].'</td>
							<td>'.date('Y-m-d',strtotime($myrowtime['created_date'])).'</td>

							<td>'.$myrowtime['projecttask_name'].'</td>
							<td>'.$myrowtime['description'].'</td>
								<td>'.$myrowtime['employeeid'].'</td>
               <td>'.$myrowtime['first_name'].' '.$myrowtime['middle_name'].' '.$myrowtime['last_name'].'</td>
							<td>'.$totalduration.'</td>
							<td>'._(($myrowtime['taskbillable']==1)? 'YES':' NO ').'</td>
							<td>'._(($myrowtime['sheet_billed']==1)? 'YES':' NO ').'</td>
							</tr>';
							$sumduration=$sumduration+$totalduration;
							}
							$subtotalcustomer=$subtotalcustomer+$sumduration;
					echo'
					<tr><td></td><td colspan="6">Total Duration </td><td>'.$sumduration.'</td></tr>

					<tr><td></td><td colspan="9"><hr> </td></tr>
					';

						}


				}


			echo'<tr><td colspan="7">Grand Total Duration </td><td>'.$subtotalcustomer.'</td></tr>';


				echo '</table>';


}
}
include('includes/footer.php');
?>
