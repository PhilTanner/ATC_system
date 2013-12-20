<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	if( !(int)$_POST['student_id'] )
	{
		$query = "
INSERT INTO student ( firstname, lastname, is_male, parent_email, parent_firstname, parent_lastname, parent_title ) VALUES (
'".$mysqli->real_escape_string($_POST['firstname'])."', '".$mysqli->real_escape_string($_POST['lastname'])."', ".$mysqli->real_escape_string($_POST['is_male']).", '".$mysqli->real_escape_string($_POST['parent_email'])."', '".$mysqli->real_escape_string($_POST['parent_firstname'])."', '".$mysqli->real_escape_string($_POST['parent_lastname'])."', '".$mysqli->real_escape_string($_POST['parent_title'])."' );";
	} else {
		$query = "
UPDATE	student
SET	firstname = '".$mysqli->real_escape_string($_POST['firstname'])."',
	lastname = '".$mysqli->real_escape_string($_POST['lastname'])."',
	is_male = ".$mysqli->real_escape_string($_POST['is_male']).",
	parent_email = '".$mysqli->real_escape_string($_POST['parent_email'])."',
	parent_firstname = '".$mysqli->real_escape_string($_POST['parent_firstname'])."',
	parent_lastname = '".$mysqli->real_escape_string($_POST['parent_lastname'])."',
	parent_title = '".$mysqli->real_escape_string($_POST['parent_title'])."',
	display = ".(int)$_POST['display']."
WHERE	student_id = ".(int)$_POST['student_id']."
LIMIT 1;";
	}
	
	if ($result = $mysqli->query($query))
	{
?>
<script type="text/javascript">
	$('#studentlist tbody tr[data-studentid="<?=(int)$_POST['student_id']?>"]').attr('data-display', <?=(int)$_POST['display']?>);
	refreshstudentlist();
	$('#dialog').dialog({ modal: true, title: 'Data saved!', buttons: { "OK": function() { $( this ).dialog( "close" ); } }  }).html('<?=$_POST['firstname']?>\'s details have been updated.');
</script>
<?php
	}
	
	/* close connection */
	$mysqli->close();
?>
