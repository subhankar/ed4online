<?php
class ContactHook{
	function countCourse($bean, $event, $arguments){
	   global $db;
	   $query = "SELECT count(*) as num "
			  . "FROM   contacts_products_1_c "
			  . "WHERE  deleted=0 "
			  . "AND    contacts_products_1contacts_ida ='$bean->id';";
	   $result = $db->query($query, true,"Error reading number of accounts: ");
	   $row = $db->fetchByAssoc($result);
	   if($row != null){ 
		  $bean->number_of_courses_c = $row['num'];
	   }
	}
}