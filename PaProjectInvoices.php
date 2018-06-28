<?php
/* $Id: HrSelectEmployee.php 7751 2018-04-13 16:34:26Z raymond $*/
/* Search for employees  */

include('includes/session.php');
$Title = _('Project Invoices Report');
$ViewTopic = 'ProjectAccounting';
$BookMark = 'Project Invoices Report';
include('includes/header.php');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>
	<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
	<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';



		if (isset($_GET['Projects'])) {
			$SelectedProject = $_GET['Projects'];
		} elseif (isset($_POST['Projects'])){
			$SelectedProject = $_POST['Projects'];
		} else {
			unset($SelectedProject);
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

    echo  _('Project ') . ': <select name="Projects" required="required">
<option value="">Select Project</option>';

		$sql = "SELECT id, project_id,project_name FROM paprojects";
		$resultProjects = DB_query($sql);
		while ($myrow=DB_fetch_array($resultProjects)){
					if ($myrow['project_id']==$SelectedProject){
						echo '<option data-id="'.$myrow['project_id'].'" selected="selected" value="'. $myrow['project_id'] . '">' . $myrow['project_id'].' - '. $myrow['project_name'] . '</option>';
					} else {
						echo '<option data-id="'.$myrow['project_id'].'" value="'. $myrow['project_id'] . '">' . $myrow['project_id'].' -'. $myrow['project_name']. '</option>';
					}

			}


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

			$Fromdate=DateTime::createFromFormat($_SESSION['DefaultDateFormat'],$_POST['FromDate']);
			$Todate=DateTime::createFromFormat($_SESSION['DefaultDateFormat'],$_POST['ToDate']);

			echo '<table class="selection">
					<tr>
					<th class="ascending">' . _('invoice No') . '</th>
					<th class="ascending">' . _('Particulars') . '</th>
					<th class="ascending">' . _('Amount') . '</th>

			';
			echo'</tr>';


					echo'<tr>	</tr>';

					$sqltime= "SELECT
					transno,
					debtorno,
					invtext ,
					ovamount
					FROM debtortrans

					WHERE
					 debtorno ='".$SelectedProject."' AND
					 trandate Between '".$Fromdate->format('Y-m-d')."' AND
					'".$Todate->format('Y-m-d')."'
					";
					$ErrMsg = _('The Projects could not be loaded because');
					$DbgMsg = _('The SQL that was used to get the Projects and failed was');
					$resulttime = DB_query($sqltime,$ErrMsg,$DbgMsg);

					while($myrowtime = DB_fetch_array($resulttime))
					{

			echo'<tr><td>'.$myrowtime['transno'].'</td>

			<td>'.$myrowtime['invtext'].'</td>
			<td>'.number_format($myrowtime['ovamount'],2).'</td>

			<td></td>
			</tr>';
					}



			echo '</table>';

}
include('includes/footer.php');
?>
