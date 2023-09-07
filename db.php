<?php
//echo 'Hello<br>';
$hostname='localhost';
$username='root';
$password='';
$database='cvs_to_mysql';
$conn=new mysqli($hostname,$username,$password,$database);
if(!$conn){
die('Error In connection'.mysqli_connect_error());
}else{
echo 'Connection Success<br>';
}
//mysqli_close($conn);
?>