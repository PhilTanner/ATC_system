<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$confirm = (isset($_GET['confirm']) && (bool)$_GET['confirm']?true:false);
	
	if( !$confirm )
	{
		$query = "
SELECT	student.firstname,
	student.lastname
FROM 	student 
WHERE	student_id = ".(int)$_GET['student_id']."
LIMIT 1;";

		if ($result = $mysqli->query($query)) 
		{
			$n = 0;
			/* fetch object array */
			while ($obj = $result->fetch_object()) 
			{
				$n++;
				if( $n == 1 )
				{
					echo '<h1> Really delete '.$obj->firstname.' '.$obj->lastname.'?</h1>'."\n";
					echo '<p>There is no way to reverse this action afterwards.</p>'."\n";
					echo '<button type="button" class="yes">Yes. Delete '.$obj->firstname.'</button>'."\n"; 
				}
			}
		}
		
		$result->close();
		$mysqli->close();	
?>
	
	<script type="text/javascript">
		$('button.yes').button({ icons: { primary: 'ui-icon-check' } }).click(function(){
			if( confirm('Are you really, REALLY, sure you want to DELETE this student?') )
			{
				$.ajax({
					url:	"ajax-deletestudent.php",
					data: 	{ student_id: <?= (int)$_GET['student_id'] ?>, confirm: true },
					success:function(response){
							refreshstudentlist();
							$('#dialog').html('<h1>Deleted</h1>');
						}
				});
			} else $('#dialog').dialog('close');
		}).addClass('ui-state-error');
	</script>
<?php 	} else {
		ob_start();
		$_GET['automatic'] = true;
		include 'ajax-systembackup.php';
		ob_end_clean();  
		$query = "DELETE FROM attendance        WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
		$query = "DELETE FROM booking_exception WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
		$query = "DELETE FROM invoice           WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
		$query = "DELETE FROM payments          WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
		$query = "DELETE FROM recurring_booking WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
		$query = "DELETE FROM student           WHERE student_id = ".(int)$_GET['student_id'].";";
		$mysqli->query($query);
echo $query;
		$mysqli->close();
	}
?>
