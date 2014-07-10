<?php
error_reporting(1);
//require_once('config.php');
//require_once('modules/stu_Students/stu_Students.php');

global $sugar_config;
$db = $GLOBALS['db'];

$host = $sugar_config['dbconfig']['db_host_name'];
$user = $sugar_config['dbconfig']['db_user_name'];
$pass = $sugar_config['dbconfig']['db_password'];
$dbname = $sugar_config['dbconfig']['db_name'];
$connString = mysql_connect($host,$user,$pass);
mysql_select_db($dbname,$connString);

	
echo $sql_std = "SELECT * FROM stu_students WHERE deleted = 0 LIMIT 500,500";
echo '<BR />';
$rs_std = mysql_query($sql_std) or mysql_error();
echo '<BR /><BR />';
$count = 1;
while($dt_std = mysql_fetch_array($rs_std)){
	//Select from Student Custom table
	echo $std_cstm = "SELECT * FROM stu_students_cstm WHERE id_c = '".$dt_std['id']."'";
	$rs_std_cstm = mysql_query($std_cstm) or mysql_error();
	$dt_std_cstm = mysql_fetch_array($rs_std_cstm);
	echo '<BR /><BR />';
	//SELECT from Student User relationship
	echo $std_user_sql = "SELECT users_stu_students_1users_ida FROM users_stu_students_1_c WHERE users_stu_students_1stu_students_idb = '".$dt_std['id']."' ";
	$rs_std_user = mysql_query($std_user_sql) or mysql_error();
	$dt_std_user = mysql_fetch_array($rs_std_user);
	if(isset($dt_std_user) && ($dt_std_user != '')){
		$assigned_user_id = $dt_std_user['users_stu_students_1users_ida'];
	}else{
		$assigned_user_id = $dt_std['assigned_user_id'];
	}
	echo '<BR /><BR />';
	//Insert into Contacts table
	$con_id = getUid();
	$sql_insert="INSERT INTO `contacts` SET `id`='".$con_id."', ";
	$sql_insert.="`date_entered`='".$dt_std['date_entered']."', ";
	$sql_insert.="`date_modified`='".$dt_std['date_modified']."', ";
	$sql_insert.="`modified_user_id`='".$dt_std['modified_user_id']."', ";
	$sql_insert.="`created_by`='".$dt_std['created_by']."', ";
	$sql_insert.="`deleted`='0', ";
	$sql_insert.="`assigned_user_id`='".$assigned_user_id."', ";
	$sql_insert.="`team_id`='".$dt_std['team_id']."', ";
	$sql_insert.="`team_set_id`='".$dt_std['team_set_id']."', ";
	$sql_insert.="`salutation`='".$dt_std['salutation']."', ";
	$sql_insert.="`first_name`='".addslashes($dt_std['first_name'])."', ";
	$sql_insert.="`last_name`='".addslashes($dt_std['last_name'])."', ";
	$sql_insert.="`description`='".addslashes($dt_std['description'])."', ";
	$sql_insert.="`phone_home`='".$dt_std['phone_home']."', ";
	$sql_insert.="`phone_mobile`='".$dt_std['phone_mobile']."', ";
	$sql_insert.="`primary_address_street`='".$dt_std['primary_address']."', ";	
	$sql_insert.="`primary_address_city`='".$dt_std['primary_address_city']."', ";
	$sql_insert.="`primary_address_state`='".$dt_std['primary_address_state']."', ";
	$sql_insert.="`primary_address_postalcode`='".$dt_std['primary_address_postalcode']."', ";	
	$sql_insert.="`primary_address_country`='".$dt_std['primary_address_country']."' ";
	
	
	$db->query($sql_insert);
	echo $sql_insert;
	echo '<BR /><BR />';;
	//Custom Contacts table
	$con_cust_sql = "INSERT INTO `contacts_cstm` SET `id_c`='".$con_id."', ";
	$con_cust_sql.="`school_reseller_name_c`='".$dt_std['school_name']."', ";
	$con_cust_sql.="`number_of_courses_c`='".$dt_std['number_of_courses']."', ";
	$con_cust_sql.="`contact_type_c`='Student', ";
	$con_cust_sql.="`student_status_c`='".$dt_std['status']."' ";
	$db->query($con_cust_sql);
	echo $con_cust_sql;
	echo '<BR /><BR />';
	//Get Email id of Student
	$student = BeanFactory::getBean('stu_Students',$dt_std['id']);
	$primary_email = $student->emailAddress->getPrimaryAddress($student);	
	//Set Email id of Contacts
	$focus = new Contact();
	$focus->retrieve($con_id);
	$focus->email1 = $primary_email;
	$focus->save();
	echo '<BR /><BR />';
	//Select from Student Account Table
	echo $std_acc_sql = "SELECT * FROM accounts_stu_students_1_c WHERE accounts_stu_students_1stu_students_idb = '".$dt_std['id']."'";
	$rs_std_acc = mysql_query($std_acc_sql) or mysql_error();
	$dt_std_acc = mysql_fetch_array($rs_std_acc);
	//stu_students_accounts_c
	//Insert into Account_Contact Rel Table
	echo '<BR /><BR />';
	echo $sql2 = "INSERT INTO accounts_contacts (id,contact_id,account_id) VALUES (uuid(),'".$con_id."','".$dt_std_acc['accounts_stu_students_1accounts_ida']."')";
	$db->query($sql2);
	echo '<BR /><BR />';
	//Insert into Opprotunity Table
	$opp_id = getUid();
	$opp_name = $dt_std['first_name'].'_'.$dt_std['last_name'].'_'.$dt_std['start_date'];
	$opp_stage = 'Closed Won';
	$opp_sql = "INSERT INTO `opportunities` SET `id`='".$opp_id."', ";	
	$opp_sql.="`name`='".$opp_name."', ";
	$opp_sql.="`date_entered`='".$dt_std['date_entered']."', ";
	$opp_sql.="`modified_user_id`='".$dt_std['date_modified']."', ";
	$opp_sql.="`sales_stage`='".$opp_stage."', ";
	$opp_sql.="`assigned_user_id`='".$assigned_user_id."' ";
	echo $opp_sql;
	$db->query($opp_sql);
	echo '<BR /><BR />';
	//Custom Opportunity table
	$opp_cust_sql = "INSERT INTO `opportunities_cstm` SET `id_c`='".$opp_id."', ";	
	$opp_cust_sql.="`date_invoice_paid_c`='".$dt_std_cstm['invoice_paid_date_c']."', ";
	$opp_cust_sql.="`invoice_date_c`='".$dt_std['invoice_date']."', ";
	$opp_cust_sql.="`invoice_number_c`='".$dt_std['invoice_number']."', ";
	$opp_cust_sql.="`invoice_paid_c`='".$dt_std['invoice_paid']."', ";
	$opp_cust_sql.="`type_of_payment_c`='".$dt_std['type_of_payment']."' ";
	echo $opp_cust_sql;
	$db->query($opp_cust_sql);
	echo '<BR /><BR />';
	//Create Opportunity-Accounts relationship
	echo $opp_acc_sql = "INSERT INTO accounts_opportunities (id,opportunity_id,account_id) VALUES (uuid(),'".$opp_id."','".$dt_std_acc['accounts_stu_students_1accounts_ida']."')";
	$db->query($opp_acc_sql);
	echo '<BR /><BR />';
	//Create Relation B/w Contacts & Opportunity
	echo $cont_opp_sql = "INSERT INTO opportunities_contacts(id,contact_id,opportunity_id) VALUES (uuid(),'".$con_id."','".$opp_id."')";
	$db->query($cont_opp_sql);
	echo '<BR /><BR />';
	//Select All products associated with Student
	echo $std_prod_sql = "SELECT stu_students_products_1products_idb FROM stu_students_products_1_c WHERE deleted = 0 AND stu_students_products_1stu_students_ida = '".$dt_std['id']."'";
	echo '<BR /><BR />';
	$rs_std_prod = mysql_query($std_prod_sql);
	//While Loop
	while($dt_std_prod = mysql_fetch_array($rs_std_prod)){
		//Update Products table
		//Check that product in custom table
		$chk_sql = "SELECT id_c FROM products_cstm WHERE id_c = '".$dt_std_prod['stu_students_products_1products_idb']."'";
		$rs_chk = mysql_query($chk_sql);
		if(mysql_num_rows($rs_chk) > 0){
			//Update existing record
			$prod_sql = "UPDATE products_cstm SET end_date_c = '".$dt_std['end_date']."',start_date_c = '".$dt_std['start_date']."',textbook_tracking_c = '".$dt_std['textbook_tracking']."', textbook_po_c = '".$dt_std['textbook_invoice_number']."',extension_order_date_c = '".$dt_std['textbook_ordered']."',extension_cost_c = '".$dt_std_cstm['extension_cost_c']."' WHERE id_c = '".$dt_std_prod['stu_students_products_1products_idb']."'";
		}else{
			//Create new enrty
			$prod_sql = "INSERT INTO `products_cstm` SET ";	
			$prod_sql.="`id_c`='".$dt_std_prod['stu_students_products_1products_idb']."', ";
			$prod_sql.="`end_date_c`='".$dt_std['end_date']."', ";
			$prod_sql.="`start_date_c`='".$dt_std['start_date']."', ";
			$prod_sql.="`textbook_tracking_c`='".$dt_std['textbook_tracking']."', ";
			$prod_sql.="`textbook_po_c`='".$dt_std['textbook_invoice_number']."', ";
			$prod_sql.="`extension_order_date_c`='".$dt_std['textbook_ordered']."', ";
			$prod_sql.="`extension_cost_c`='".$dt_std_cstm['extension_cost_c']."' ";
		}
		echo $prod_sql;
		$db->query($prod_sql);
		echo '<BR /><BR />';
		
		//Create Relationship with Contacts+Products
		echo $cont_prod_rel = "INSERT INTO contacts_products_1_c (id,contacts_products_1contacts_ida,contacts_products_1products_idb) VALUES (uuid(),'".$con_id."','".$dt_std_prod['stu_students_products_1products_idb']."')";
		$db->query($cont_prod_rel);
		echo '<BR /><BR />';
		
		//Create Relationship with Opportunity+Products
		echo $opp_prod_rel = "INSERT INTO opportunities_products_1_c (id,opportunities_products_1opportunities_ida,opportunities_products_1products_idb) VALUES (uuid(),'".$opp_id."','".$dt_std_prod['stu_students_products_1products_idb']."')";
		$db->query($opp_prod_rel);
		echo '<BR /><BR />';
	}
	$count++;
}
echo '<BR /><BR />';
echo 'Record Processed '.$count;

function getUid() {
    return mysql_result(mysql_query('SELECT UUID()'),0);
}