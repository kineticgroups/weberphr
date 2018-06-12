<?php
/* $Id: HrSelectEmployee.php 7751 2018-04-13 16:34:26Z raymond $*/
/* Search for employees  */

include('includes/session.php');
$Title = _('Search Employees');
$ViewTopic = 'HumanResource';
$BookMark = 'HumanResource';
include('includes/header.php');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>
	<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
	<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

    if (isset($_GET['EN'])) {
    	$EN = $_GET['EN'];
    } elseif (isset($_POST['EN'])){
    	$EN = $_POST['EN'];
    } else {
    	unset($EN);
    }
    if (isset($_GET['Department'])) {
    	$SelectedDEPT = $_GET['Department'];
    } elseif (isset($_POST['Department'])){
    	$SelectedStockItem = $_POST['Department'];
    } else {
    	unset($SelectedDEPT);
    }
//    if (!isset($EN) or ($EN=='')){
      echo '<table class="selection"><tr><td>';
    if (isset($SelectedEmployee)) {
      echo _('For the Employee') . ': ' . $SelectedEmployee . ' ' . _('and') . ' <input type="hidden" name="$SelectedEmployee" value="' . $SelectedEmployee . '" />';
    }
    echo _('Employee number') . ': <input type="text" name="EN" autofocus="autofocus" maxlength="8" size="9" />&nbsp; ' . _('Department') . ':<select name="Department"> <option value="">search by department</option>';
    $sql = "SELECT departmentid, description FROM departments";
    $resultDepartments = DB_query($sql);
    while ($myrow=DB_fetch_array($resultDepartments)){
  			if (isset($_POST['Department'])){
  				if ($myrow['departmentid'] == $_POST['Department']){
  					 echo '<option selected="selected" value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
  				} else {
  					 echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
  				}
  			} elseif ($myrow['departmentid']==$_SESSION['UserStockLocation']){
  				 echo '<option selected="selected" value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
  			} else {
  				 echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
  			}
  		}

  		echo '</select> &nbsp;&nbsp'._('Employee name') . ': <input type="text" name="Ename"  maxlength="8" size="9" />&nbsp';
      echo '<input type="submit" name="SearchEmployee" value="' . _('Search') . '" />
  			&nbsp;&nbsp;<a href="' . $RootPath . '/HrEmployees.php?New=Yes">' . _('New Employee') . '</a></td>
  			</tr>
  			</table>
  			<br />
				</div>
        </form>';
//    }
    if(isset($_POST['SearchEmployee'])) {

    	echo '<table class="selection">
    			<tr>
    				<th class="ascending">', _('Employee ID'), '</th>
    				<th class="ascending">', _('Full Name'), '</th>
            <th class="ascending">', _('Department'), '</th>
    				<th class="ascending">', _('Telephone'), '</th>
    				<th class="ascending">', _('Joining Date'), '</th>
    				<th class="ascending">', _('Gender'), '</th>
    				<th class="ascending">', _('Date of Birth'), '</th>
    				<th class="ascending">', _('Nationality'), '</th>
    				<th class="ascending">', _('Address'), '</th>
    				<th class="ascending">', _('Marital Status'), '</th>
						<th class="ascending">', _('Gross Salary'), '</th>
						<th class="ascending">', _('Net Salary'), '</th>
    				<th class="noprint" colspan="2">&nbsp;</th>
    			</tr>';
			$base_sql =	"SELECT hremployees.employee_id,hremployees.empid,
    					first_name,
    					middle_name,
              last_name,
    					mobile_phone,
    					marital_status,
    					date_of_birth,
              joining_date,
    					home_address,
    					gender,
    					status,
    					user_id,
              nationality,
    					employee_department,
							gross_pay,
							net_pay
    				FROM hremployees JOIN hremployeesalarystructures ON hremployees.empid = hremployeesalarystructures.employee_id WHERE";
      if(isset($EN) && $EN !=""){
    	$Sql = $base_sql." hremployees.employee_id LIKE '%".$EN."%'";

    }
    elseif(isset($_POST['Department']) && $_POST['Department'] != "")
    {
      $Sql = $base_sql." employee_department=".$_POST['Department']."";
    }
    elseif(isset($_POST['Ename']) && $_POST['Ename'] != "")
    {
      $Sql = $base_sql." first_name LIKE '%".$_POST['Ename']."%'";
    }

    $Result = DB_query($Sql);

    	$k = 1;// Row colour counter.
    	while ($MyRow = DB_fetch_array($Result)) {
    		if($k == 1) {
    			echo '<tr class="OddTableRows">';
    			$k = 0;
    		} else {
    			echo '<tr class="EvenTableRows">';
    			$k = 1;
    		}
					$sql2 ="SELECT departmentid,description FROM departments WHERE departmentid =".$MyRow['employee_department']."";
					$result2 = DB_query($sql2);
					$deparmentDetails = DB_fetch_array($result2);
    		/*The SecurityHeadings array is defined in config.php */
    		echo	'<td class="text">'. $MyRow['employee_id']. '</td>
    				<td class="text">'. $MyRow['first_name'].' '.$MyRow['middle_name'].' '.$MyRow['last_name']. '</td>
    				<td class="text">'. $deparmentDetails['description']. ' </td>
    				<td class="text">'. $MyRow['mobile_phone']. '</td>
    				<td class="text">'. $MyRow['joining_date']. '</td>
    				<td class="text">'. $MyRow['gender']. '</td>
    				<td class="centre">'. $MyRow['date_of_birth']. '</td>
    				<td class="text">'. $MyRow['nationality']. '</td>
    				<td class="text">'. $MyRow['home_address']. '</td>
    				<td class="text">'. $MyRow['marital_status']. '</td>
						<td class="text">'. number_format($MyRow['gross_pay'],2). '</td>
						<td class="text">'. number_format($MyRow['net_pay'],2). '</td>
    				<td class="noprint"><a href="HrEmployees.php?EmpID='. $MyRow['employee_id']. '">'. _('Edit'). '</a></td>
    				<td class="noprint"><a href="HrEmployees.php?EmpID='. $MyRow['employee_id']. '&amp;delete=1" onclick="return confirm(\'', _('Are you sure you wish to delete this employee?'), '\');">'. _('Delete'). '</a></td>
    			</tr>';
    	}// END foreach($Result as $MyRow).
    	echo '</table>
    		<br />';
    }
include('includes/footer.php');
?>
