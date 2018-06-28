-- HR AND PA ADDITIONS

ALTER TABLE debtortrans MODIFY reference varchar(50);
INSERT INTO  `securityroles` (`secroleid`,`secrolename`) values(NULL,'Human Resource Manager');
INSERT INTO  `securityroles` (`secroleid`,`secrolename`) values(NULL, 'Human Resource Clerk');
INSERT INTO  `securityroles` (`secroleid`,`secrolename`) values(NULL, 'Employee');
INSERT INTO  `securitytokens` values(20, 'Employee Access');
INSERT INTO  `securitytokens` values(21, 'HR Basic Access');
INSERT INTO  `securitytokens` values(22, 'HR Advanced Access');
INSERT INTO  `securitygroups` values(10,0);
INSERT INTO  `securitygroups` values(10,20);
INSERT INTO  `securitygroups` values(10,21);
INSERT INTO  `securitygroups` values(10,22);
INSERT INTO  `securitygroups` values(11,0);
INSERT INTO  `securitygroups` values(11,20);
INSERT INTO  `securitygroups` values(11,21);
INSERT INTO  `securitygroups` values(12,0);
INSERT INTO  `securitygroups` values(12,20);
--
-- Table structure for table `hremployeecategories`
--
CREATE TABLE IF NOT EXISTS `hremployeecategories` (
  `employee_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  `category_prefix` varchar(5) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`employee_category_id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('HrEmployeeCategories.php','22', 'Manage Employee Categories');
INSERT INTO  `scripts` values('HrEmployeePositions.php','22', 'Manage Employee Job Positions');

CREATE TABLE IF NOT EXISTS `hremployeepositions` (
  `employee_position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(50) NOT NULL,
  `employee_category_id` int(11) NOT NULL,
  `position_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`employee_position_id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `hremployeegradings` (
  `employee_grading_id` int(11) NOT NULL AUTO_INCREMENT,
  `grading_name` varchar(20) NOT NULL,
  `priority` int(11) NOT NULL,
  `grading_description` text NOT NULL,
  `grading_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`employee_grading_id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('HrEmployeeGrades.php','22', 'Manage Employee Grading');


CREATE TABLE IF NOT EXISTS `hrpayrollcategories` (
  `payroll_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_category_name` varchar(50) NOT NULL,
  `payroll_category_code` varchar(10) NOT NULL,
  `payroll_category_value` varchar(100) NOT NULL,
  `payroll_category_type` tinyint(50) NOT NULL DEFAULT '1',
  `additional_condition` varchar(50) NOT NULL,
  `general_ledger_account_id` VARCHAR(20) NULL,
  PRIMARY KEY (`payroll_category_id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('HrPayrollCategories.php','22', 'Manage Payroll Categories');

CREATE TABLE IF NOT EXISTS `hrpaymentfrequency` (
  `paymentfrequency_id` int(11) NOT NULL AUTO_INCREMENT,
  `frequency_name` varchar(50) NOT NULL,
  `working_days` int(11) NOT NULL,
  PRIMARY KEY (`paymentfrequency_id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hrpayrollgroups` (
  `payrollgroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `payrollgroup_name` varchar(100) NOT NULL,
  `payment_frequency` int(11) NOT NULL,
  `generation_date` int(11) NOT NULL DEFAULT '1',
  `enable_lop` tinyint(1) NOT NULL DEFAULT '0',
  `lop_value` varchar(200) NOT NULL,
  `bank_account_to_use` varchar(200) DEFAULT NULL,
  `gl_posting_account` varchar(200) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`payrollgroup_id`),
  KEY `payment_frequency` (`payment_frequency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hrpayroll_groups_payroll_categories` (
  `groups_categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_group_id` int(11) NOT NULL,
  `payroll_category_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`groups_categories_id`),
  KEY `payroll_category_id` (`payroll_category_id`),
  KEY `payroll_group_id` (`payroll_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hrpayroll_groups_payroll_categories`
  ADD CONSTRAINT `fk_groups_2` FOREIGN KEY (`payroll_group_id`) REFERENCES `hrpayrollgroups` (`payrollgroup_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_groups_categories_payroll` FOREIGN KEY (`payroll_category_id`) REFERENCES `hrpayrollcategories` (`payroll_category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

  INSERT INTO `hrpaymentfrequency` (`paymentfrequency_id`, `frequency_name`,`working_days`) VALUES
  (1, 'Daily',1),
  (2, 'Weekly',7),
  (3, 'Bi-Weekly - Once in Two Weeks',14),
  (4, 'Semi-Monthly - Once in 15 Days',15),
  (5, 'Monthly',30);
INSERT INTO  `scripts` values('HrPayrollGroups.php','22', 'Manage Payroll Groups');

INSERT INTO `hrpayrollcategories` (`payroll_category_id`, `payroll_category_name`, `payroll_category_code`, `payroll_category_value`, `payroll_category_type`, `additional_condition`, `general_ledger_account_id`) VALUES (NULL, 'Gross Salary', 'GROSS', '0', '1', '', NULL);

CREATE TABLE IF NOT EXISTS `hremployees` (
  `empid` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `employee_position` int(11) NOT NULL,
  `employee_grade_id` int(11) DEFAULT NULL,
  `job_title` varchar(50) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `employee_department` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_of_birth` date NOT NULL,
  `marital_status` varchar(10) NOT NULL,
  `children_count` int(11) NOT NULL DEFAULT 0,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) NOT NULL DEFAULT 'Uganda',
  `national_id` varchar(50) DEFAULT NULL,
  `passport_no` varchar(50) DEFAULT NULL,
  `home_address` text NOT NULL,
  `home_city` varchar(50) DEFAULT NULL,
  `mobile_phone` varchar(13) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_no` varchar(50) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `spouse_phone_no` varchar(15) DEFAULT NULL,
  `social_security_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`empid`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `employee_department` (`employee_department`),
  KEY `employee_position` (`employee_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO  `scripts` values('HrEmployees.php','21', 'Add,Edit employees');
INSERT INTO  `scripts` values('HrSelectEmployee.php','21', 'Search employees');
INSERT INTO  `scripts` values('HrEmployeePayslips.php','20', 'Employee to see Payslips');
INSERT INTO  `scripts` values('HrGeneratePayroll.php','22', 'Generate Payroll for Paygroups');
INSERT INTO  `scripts` values('HrGenerateEmployeePay.php','22', 'Generate Payroll for Single Employee');

--
-- Table structure for table `hremployeeloantypes`
--

CREATE TABLE IF NOT EXISTS `hremployeeloantypes` (
  `loan_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_type_name` varchar(50) NOT NULL,
  PRIMARY KEY (`loan_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hremployeeloans`
--

CREATE TABLE IF NOT EXISTS `hremployeeloans` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `loan_type` int(11) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` varchar(20) DEFAULT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `number_of_installments` int(11) NOT NULL DEFAULT 1,
  `amount_per_installment` decimal(10,2) NOT NULL,
  `loan_status` tinyint(1) NOT NULL DEFAULT 0,
  `bank_account_to_use` varchar(20) DEFAULT NULL,
  `gl_posting_account` varchar(20) DEFAULT NULL,
  `finance_transaction_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hremployeeloanpayments`
--

CREATE TABLE IF NOT EXISTS `hremployeeloanpayments` (
  `loan_payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `date_paid` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`loan_payment_id`),
  KEY `loan_id_payment` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hremployeeloanpayments`
  ADD CONSTRAINT `fk_loan_payments_loans` FOREIGN KEY (`loan_id`) REFERENCES `hremployeeloans` (`loan_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Table structure for table `hremployeesalarystructures`
--

CREATE TABLE IF NOT EXISTS `hremployeesalarystructures` (
  `salary_structure_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `payrollgroup_id` int(11) NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`salary_structure_id`),
  KEY `employee_id_salary` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hremployeesalarystructures`
  ADD CONSTRAINT `fk_salary_structure_employees` FOREIGN KEY (`employee_id`) REFERENCES `hremployees` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table structure for table `hremployeesalarystructure_components`
--

CREATE TABLE IF NOT EXISTS `hremployeesalarystructure_components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `salary_structure_id` int(11) NOT NULL,
  `payroll_category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`component_id`),
  KEY `salary_structure_id_component` (`salary_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hremployeesalarystructure_components`
  ADD CONSTRAINT `fk_salary_structure_components` FOREIGN KEY (`salary_structure_id`) REFERENCES `hremployeesalarystructures` (`salary_structure_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table structure for table `hrpayslipdateranges`
--

CREATE TABLE IF NOT EXISTS `hrpayslipdateranges` (
  `daterange_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `payrollgroup_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`daterange_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hremployeepayslips`
--

CREATE TABLE IF NOT EXISTS `hremployeepayslips` (
  `payslip_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `payslip_status` varchar(20) NOT NULL DEFAULT 'pending',
  `approver_id` varchar(20) DEFAULT NULL,
  `rejector_id` varchar(20) DEFAULT NULL,
  `rejecting_reason` text DEFAULT NULL,
  `gross_salary` decimal(10,2) NOT NULL,
  `lop` decimal(10,2) DEFAULT NULL COMMENT 'standard lop per day',
  `lop_days` int(11) DEFAULT NULL COMMENT 'number of days for lop',
  `lop_amount` decimal(10,2) DEFAULT NULL COMMENT 'total amount for lop',
  `loan_deduction_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `days_worked` int(11) DEFAULT NULL,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payslip_date_range_id` int(11) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `finance_transaction_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`payslip_id`),
  KEY `payslip_date_range_id` (`payslip_date_range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hremployeepayslips` ADD UNIQUE( `employee_id`, `payslip_date_range_id`);

--
-- Table structure for table `hrpayslipextradetails`
--

CREATE TABLE IF NOT EXISTS `hrpayslipextradetails` (
  `extra_payslip_id` int(11) NOT NULL AUTO_INCREMENT,
  `payslip_id` int(11) NOT NULL,
  `entry_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'either earning or deduction',
  `amount` decimal(10,2) NOT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `user_id` varchar(20) NOT NULL,
  PRIMARY KEY (`extra_payslip_id`),
  KEY `payslip_id` (`payslip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `hrpayslipextradetails`
--
ALTER TABLE `hrpayslipextradetails`
  ADD CONSTRAINT `fk_payslips_extra_infomation` FOREIGN KEY (`payslip_id`) REFERENCES `hremployeepayslips` (`payslip_id`) ON DELETE CASCADE ON UPDATE CASCADE;

  CREATE TABLE IF NOT EXISTS `hremployeeleavetypes` (
    `hrleavetype_id` int(11) NOT NULL AUTO_INCREMENT,
    `leavetype_name` varchar(50) NOT NULL,
    `leavetype_code` varchar(50) NOT NULL,
    `leavetype_status` tinyint(1) NOT NULL DEFAULT '1',
    `leavetype_leavecount` varchar(100) NOT NULL,
    `carry_forward` tinyint(1) NOT NULL DEFAULT '1',
    `lop_enabled` tinyint(1) NOT NULL DEFAULT '1',
    `max_carry_forward_leaves` varchar(50) NOT NULL,
    `reset_date` date DEFAULT NULL,
    PRIMARY KEY (`hrleavetype_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  INSERT INTO  `scripts` values('HrLeaveTypes.php','22', 'Manage Leave Types');

  CREATE TABLE IF NOT EXISTS `hremployeeleavegroups` (
    `leavegroup_id` int(11) NOT NULL AUTO_INCREMENT,
    `leavegroup_name` varchar(50) NOT NULL,
    `leavegroup_description` text NOT NULL,
    `leavegroup_status` tinyint(4) NOT NULL DEFAULT '1',
    PRIMARY KEY (`leavegroup_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


  INSERT INTO  `scripts` values('HrLeaveGroups.php','22', 'Manage Leave Groups');
  INSERT INTO  `scripts` values('HrLeaveApplications.php','20', 'Add,Edit Employees Leaves');
INSERT INTO  `scripts` values('HrSelectLeave.php','21', 'Search leaves');

CREATE TABLE IF NOT EXISTS `hremployeeleaves` (
  `employee_leave_id` int(11) NOT NULL AUTO_INCREMENT,
  `leaveemployee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `is_half` varchar(200) NOT NULL,
  `leave_start_date` date DEFAULT NULL,
  `leave_end_date` date DEFAULT NULL,
  `leave_reason` text NOT NULL,
  `leave_approved` tinyint(4) NOT NULL DEFAULT '0',
  `leave_viewed_by_manager` tinyint(4) NOT NULL DEFAULT '0',
  `leave_manager_remark` text NOT NULL,
  `leave_approving_manager` int(11) NOT NULL,
  PRIMARY KEY (`employee_leave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO  `scripts` values('HrAttendanceRegister.php','21', 'Add,Edit Employees Attendance Register');
INSERT INTO  `scripts` values('HrEmployeeLoans.php','10', 'Manage employee salary loans');
INSERT INTO  `scripts` values('HrDeductablesReports.php','10', 'See Reports on Hr deductables');
INSERT INTO  `scripts` values('HrPrintPayslip.php','20', 'Print Payslip');

INSERT INTO `hremployeeloantypes` (`loan_type_id`, `loan_type_name`) VALUES (NULL, 'Salary Advance');

--
-- Table structure for table `hrpayslipcategorydetails`
--
CREATE TABLE `hrpayslipcategorydetails` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `payslip_id` int(11) NOT NULL,
  `payroll_category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `payslip_id` (`payslip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `hrpayslipcategorydetails`
--
ALTER TABLE `hrpayslipcategorydetails`
  ADD CONSTRAINT `fk_payslip_category_detail` FOREIGN KEY (`payslip_id`) REFERENCES `hremployeepayslips` (`payslip_id`) ON DELETE CASCADE ON UPDATE CASCADE;

  CREATE TABLE IF NOT EXISTS `hremployeeattendanceregister` (
    `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
    `employee_attendance_id` int(11) NOT NULL,
    `absent_date` date NOT NULL,
    `leave_type_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`attendance_id`)
  )  ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('HrGenerateAttendanceReport.php','21', 'Generate Attendance Report');
INSERT INTO  `scripts` values('SupplierPaymentVouchers.php','5', 'See Supplier Payment Vouchers');
INSERT INTO  `scripts` values('PV_AuthorisationLevels.php','15', 'Payment Voucher Authorisation Levels');

CREATE TABLE IF NOT EXISTS `suptrans_paymentvoucher` (
  `sup_pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_voucher_id` int(11) NOT NULL,
  `suptrans_id` int(11) NOT NULL,
  PRIMARY KEY (`sup_pay_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `supplier_payment_vouchers` (
  `supplier_paymentvoucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` varchar(10) NOT NULL,
  `voucher_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `voucher_status` varchar(50) NOT NULL DEFAULT 'pending',
  `voucher_description` varchar(255)NOT NULL DEFAULT 'payment voucher',
  `approved_by` varchar(20) DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `bank_account_used` varchar(20) DEFAULT NULL,
  `gl_transaction_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`supplier_paymentvoucher_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `paymentvoucherauth` (
  `userid` varchar(20) NOT NULL DEFAULT '',
  `currabrev` char(3) NOT NULL DEFAULT '',
  `cancreate` smallint(2) NOT NULL DEFAULT '0',
  `authlevel` double NOT NULL DEFAULT '0',
  `canapprove` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`currabrev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('SupplierPayVoucher.php','5', 'Pay Supplier Voucher');
INSERT INTO  `scripts` values('SupplierPrintVoucher.php','5', 'Print Supplier Voucher');

ALTER TABLE `www_users` CHANGE `modulesallowed` `modulesallowed` VARCHAR(50) CHARACTER SET utf8 NOT NULL;


-- Project Accounting
INSERT INTO  `securitytokens` values(25, 'Project Management Maintenance and Configuration');

INSERT INTO  `scripts` values('PaProjectTypes.php','25', 'Project Types');

CREATE TABLE IF NOT EXISTS `paprojecttypes` (
  `project_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_type_name` varchar(50) NOT NULL,
  `project_type_desc` text NOT NULL,
  `project_type_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('PaProjectStatus.php','25', 'Project Status');

CREATE TABLE IF NOT EXISTS `paprojectstatus` (
  `project_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_status_name` varchar(50) NOT NULL,
  `project_status_desc` text,
  `project_status_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_status_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `paprojectstatusprevents` (
  `status_id` int(11) NOT NULL,
  `prevents` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `paprojectprevents` (
  `prevent` varchar(100) NOT NULL,
  `names` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paprojectprevents` (`prevent`, `names`) VALUES
('timesheet', 'Timesheet Entry'),
('expense', 'Expense entry'),
('purchasing', 'Purchasing'),
('invoices', ' Generate invoices');

CREATE TABLE IF NOT EXISTS `paprojectcategories` (
  `project_category` varchar(100) NOT NULL,
  `project_category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paprojectcategories` (`project_category`, `project_category_name`) VALUES
('contract', 'Contract'),
('capitalized', 'Capitalized'),
('non-billable', 'Internal Non-billable'),
('billable', 'Internal Billable');

CREATE TABLE IF NOT EXISTS `paprojectbillings` (
  `billing_name` varchar(50) NOT NULL,
  `billing_desc` varchar(50) NOT NULL
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paprojectbillings` (`billing_name`, `billing_desc`) VALUES
('notthing', 'Do nothing'),
('warning', 'Issue a warning message'),
('prevent', 'Prevent billing');


INSERT INTO  `scripts` values('PaProjectBillingTerm.php','25', 'Project Billing Terms');



CREATE TABLE IF NOT EXISTS `paprojectperiodtostart` (
  `period_id` varchar(50) NOT NULL,
  `period_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paprojectperiodtostart` (`period_id`, `period_name`) VALUES
('month', 'of the month of invoice/bill date'),
('nextmoth', 'of next month from invoice/bill date'),
('2ndmonth', 'of 2nd month from invoice/bill date'),
('3rdmonth', 'of 3rd month from invoice/bill date'),
('4thmonth', 'of 4th month from invoice/bill date'),
('5thmonth', 'of 5th month from invoice/bill date'),
('6thmonth', 'of 6th month from invoice/bill date');


CREATE TABLE IF NOT EXISTS `paprojectbillingcycle` (
  `cycle_id` varchar(50) NOT NULL,
  `cycle_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `paprojectbillingcycle` (`cycle_id`, `cycle_name`) VALUES
('nopenalty', 'No Penalty'),
('daily', 'Daily'),
('weekly', 'Weekly'),
('biweekly', 'Biweekly'),
('monthly', 'Monthly'),
('bimonthly', 'Bimonthly'),
('quarterly', 'Quarterly'),
('halfyearly', 'Half yearly'),
('annually', 'Annually');


CREATE TABLE IF NOT EXISTS `paprojectbillingterms` (
  `billing_term_id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_term_name` varchar(50) NOT NULL,
  `billing_term_desc` text NOT NULL,
  `billing_term_status` tinyint(4) NOT NULL DEFAULT '1',
  `billing_term_duedate` int(11) DEFAULT NULL,
  `billing_term_dueperiod` varchar(50) NOT NULL,
  `billing_term_discountdate` int(11) DEFAULT NULL,
  `billing_term_discountperiod` varchar(50) NOT NULL,
  `billing_term_discountamount` decimal(10,2) DEFAULT '0.00',
  `biling_term_discountcal` varchar(50) NOT NULL,
  `billing_term_discountgracedays` int(11) DEFAULT NULL,
  `billing_term_discountcalculate` varchar(50) NOT NULL,
  `billing_term_penalitycycle` varchar(50) NOT NULL,
  `billing_term_penalityamount` decimal(10,0) DEFAULT '0',
  `billing_term_penalitycal` varchar(50) NOT NULL,
  `billing_term_penalitygracedays` int(11) NOT NULL,
  PRIMARY KEY (`billing_term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('PaSelectProject.php','25', 'Project Search');
INSERT INTO  `scripts` values('PaProjects.php','25', 'Create and edit projects');

--
-- Table structure for table `paprojects`
--

CREATE TABLE `paprojects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` varchar(50) NOT NULL,
  `project_name` varchar(300) NOT NULL,
  `project_category` varchar(50) NOT NULL DEFAULT 'Contract',
  `project_type` int(11) NOT NULL,
  `project_description` text NOT NULL,
  `parent_project` int(11) DEFAULT NULL,
  `customer` varchar(50) DEFAULT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `project_manager` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `project_status` int(11) NOT NULL,
  `project_location` varchar(10) NOT NULL,
  `billing_term` int(11) NOT NULL,
  `billing_type` varchar(20) NOT NULL,
  `billable_expense` tinyint(1) NOT NULL DEFAULT 0,
  `billable_ap` tinyint(1) NOT NULL DEFAULT 0,
  `contract_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `invoice_with_parent` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('PaProjectResourcesLabour.php','25', 'Project Resources Labour');



CREATE TABLE IF NOT EXISTS `paprojectresourcelabour` (
  `project_resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `services` varchar(50) NOT NULL,
  `resource_description` text,
  `resource_startdate` date NOT NULL,
  `labourrate` decimal(12,2) NOT NULL,
  `resource_expense` int(11) NOT NULL,
  `resource_price` int(11) NOT NULL,
  `resource_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_resource_id`),
  UNIQUE KEY `project_id` (`project_id`,`employee_id`,`services`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO  `scripts` values('PaProjectTasks.php','25', 'Project Tasks');



CREATE TABLE IF NOT EXISTS `paprojecttasks` (
  `projecttask_id` int(11) NOT NULL AUTO_INCREMENT,
  `projecttask_name` varchar(50) NOT NULL,
  `project_id` int(11) NOT NULL,
  `planbegindate` date NOT NULL,
  `planenddate` date NOT NULL,
  `plannedduration` decimal(10,2) NOT NULL,
  `dependanttask` int(11) NOT NULL,
  `servicetask` varchar(50) NOT NULL,
  `taskbillable` tinyint(4) DEFAULT '1',
  `taskdesc` text,
  `taskmilestone` tinyint(4) DEFAULT '1',
  `taskutilized` tinyint(4) DEFAULT '1',
  `taskpriority` int(11) DEFAULT '1',
  `taskwbscode` varchar(50) DEFAULT NULL,
  `taskstatusid` varchar(50) DEFAULT 'inprogress',
  `parent_task` int(11) DEFAULT NULL,
  PRIMARY KEY (`projecttask_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('PaProjectTaskResources.php','25', 'Project Tasks Resources');

CREATE TABLE IF NOT EXISTS `paprojecttaskresources` (
  `projecttaskresource_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `projecttask_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `resource_begindate` date NOT NULL,
  `resource_enddate` date NOT NULL,
  `resource_desc` text CHARACTER SET latin1 NOT NULL,
  `resource_fulltime` tinyint(4) NOT NULL DEFAULT '0',
  `resource_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`projecttaskresource_id`),
  UNIQUE KEY `projecttaskresource_id` (`project_id`,`projecttask_id`,`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `paprojecttaskstatus` (
  `taskstatusid` varchar(50) NOT NULL,
  `taskstatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paprojecttaskstatus` (`taskstatusid`, `taskstatus`) VALUES
('notstarted', 'Not Started'),
('planned', 'Planned'),
('inprogress', 'In Progress'),
('completed', 'Completed'),
('onhold', 'On Hold');

INSERT INTO  `scripts` values('PaTimesheets.php','25', 'Project Accounting Timesheets');


CREATE TABLE IF NOT EXISTS `patimesheetentries` (
  `timesheetentry_id` int(11) NOT NULL AUTO_INCREMENT,
  `timesheetinfo_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `projecttask_id` int(11) NOT NULL,
  `sun` double(10,2) NOT NULL,
  `mon` double(10,2) NOT NULL,
  `tue` double(10,2) NOT NULL,
  `wed` double(10,2) NOT NULL,
  `thu` double(10,2) NOT NULL,
  `fri` double(10,2) NOT NULL,
  `sat` double(10,2) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`timesheetentry_id`),
  UNIQUE KEY `project_id` (`project_id`,`projecttask_id`,`timesheetinfo_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `patimesheetsinfo` (
  `timesheetsinfo_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `timesheet_desc` text CHARACTER SET latin1 NOT NULL,
  `attachment` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `timesheet_status` tinyint(4) NOT NULL DEFAULT '0',
  `approved_by` varchar(50) DEFAULT NULL,
  `sheet_billed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`timesheetsinfo_id`),
  UNIQUE KEY `employee_id` (`employee_id`,`begin_date`,`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO  `scripts` values('PaEmplyeeTimeReport.php','25', 'Employee Timesheets Reports');

INSERT INTO  `scripts` values('PaProjectStatusReports.php','25', 'Project Status  Reports');
INSERT INTO  `scripts` values('HrGlSettings.php','22', 'General ledger settings');
INSERT INTO  `scripts` values('HrWorkingDays.php','22', 'Working days settings');
INSERT INTO  `scripts` values('HrPayrollMode.php','22', 'Payroll mode settings');
INSERT INTO  `scripts` values('HrPayslipSettings.php','22', 'Payslip settings');
INSERT INTO  `scripts` values('HrLoanTypes.php','22', 'Loan Type settings');
INSERT INTO  `scripts` values('HrNotificationSettings.php','22', 'Notification settings');
INSERT INTO  `scripts` values('HrGenerateEstimatedSalary.php','22', 'Salary Estimate Report');


INSERT INTO  `scripts` values('HrMyLeave.php','20', 'View Employee Leaves  ');

ALTER TABLE `debtorsmaster` ADD `isproject` TINYINT NOT NULL DEFAULT '0' AFTER `language_id`;
ALTER TABLE `debtortrans` ADD `projecttaskresource_id` INT NOT NULL DEFAULT '0' AFTER `salesperson`;

INSERT INTO  `scripts` values('PaPayApprovedTimesheets.php','25', 'Pay Approved Timesheets');
ALTER TABLE `paprojects` ADD `bankaccount` VARCHAR(100) NOT NULL AFTER `invoice_with_parent`;
ALTER TABLE `patimesheetentries` ADD `paystatus` TINYINT NOT NULL DEFAULT '0' AFTER `created_date`;

INSERT INTO  `scripts` values('PaProjectInvoices.php','25', 'Generate Project Invoice');
CREATE TABLE IF NOT EXISTS `patimesheetspayments` (
  `timesheetspay_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `labourrate` decimal(12,2) NOT NULL,
  `totaltime` decimal(12,2) NOT NULL,
  `totalamount` decimal(12,2) NOT NULL,
  `datepaid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  PRIMARY KEY (`timesheetspay_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
