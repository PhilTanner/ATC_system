<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	 
	$query = "SELECT * FROM student ORDER BY lastname, firstname, lastname ASC;";
	
	if ($result = $mysqli->query($query))
	{
		while ($obj = $result->fetch_object()) 
			$students[] = $obj;
		/* free result set */
		$result->close();
	}
	$date = (isset($_GET['date'])&&strtotime(str_replace(',','',$_GET['date']))?strtotime(str_replace(',','',$_GET['date'])):time());
	$weekcommencing = strtotime((date('D', $date) == 'Mon'?'today':'last Monday'),$date );
	$attendingstudents = studentBookings( date('c', $weekcommencing) );
	//var_dump($attendingstudents);
	$attendingstudents = $attendingstudents[(date('N', $date)-1)];

	$studentlist = '<select name="student[]" class="studentnames" required="required" onchange="addnewrow()">'."\n";
	$studentlist .= '	<option value="">Please choose:</option>'."\n";
	foreach($students as $student)
		$studentlist .= '	<option value="'.$student->student_id.'">'.$student->lastname.', '.$student->firstname.'</option>'."\n";
	$studentlist .= '</select>'."\n";
	
	/* close connection */
	//$mysqli->close();
?>

<form onsubmit="return false;" id="attendanceform">
	<table id="attendancelist">
		<caption> 
			Timeout sheet for <input type="text" class="pickdate" value="<?= date('l d M, Y', $date ) ?>" id="choosedate" style="width:12em;" onchange="$('#attendancelist').addClass('ui-state-disabled'); $('#dialog').dialog({ title: 'Time out sheet' }).load('ajax-recordtimeoutsheet.php?date='+escape($('#choosedate').val()));" />
		</caption>
		<thead class="ui-widget-header">
			<tr>
				<th>Name</th>
				<th>Date</th>
				<th>In</th>
				<th>Out</th>
				<th>Rate p/h</th>
				<th>Absent</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="4"></th>
				<th> <button type="button" id="donetimesheet">Done</button> </th>
			</tr>
		</tfoot>
		<tbody>
			<?php
			
				foreach( $attendingstudents as $attendee )
				{
					if( $attendee[0] == ABSENT ) continue;
					
					$query = "
SELECT	arrived 
FROM 	attendance 
	INNER JOIN student
		ON attendance.student_id = student.student_id
WHERE	`attendance`.`date` = '".date('Y-m-d', $date)."'
	AND CONCAT(lastname, ', ', firstname) = '".$attendee[1]."'";
	
					if ($result = $mysqli->query($query))
						if( $obj = $result->fetch_object() )
							if( $obj->arrived )
								continue;
			//var_dump($query);
			?>
			<tr>
				<td>
					<?php
						echo '<select name="student[]" class="studentnames" required="required">'."\n";
						$hourlyrate = '5.00';
						foreach($students as $student)
						{
							echo '	<option value="'.$student->student_id.'"';
							if( $student->lastname.', '.$student->firstname == $attendee[1] )
							{
								echo ' selected="selected"';
								$myid = (int)$student->student_id;
								// Staff children don't pay
								if( ($myid>=25&&$myid<=27)||$myid==2||$myid==22 ) 
									$hourlyrate = '0.00';
							}
							echo '>'.$student->lastname.', '.$student->firstname.'</option>'."\n";
						}
						echo '</select>'."\n";
					?>
				</td>
				<td> <input type="text" name="datepicker[]" class="pickdate" value="<?= date('d M, Y', $date)?>" /> </td>
				<td> <input type="text" name="arrived[]" class="picktime" value="15:00" /> </td>
				<td> <input type="text" name="left[]" class="picktime" /> </td>
				<td> <input type="text" name="hourly_charge[]" value="<?= $hourlyrate?>" class="hourlyrate<?=($hourlyrate=='5.00'?'':' ui-state-error')?>" /> </td>
				<td> <button type="button" class="authedabsence">Notified before noon</button><button type="button" class="unauthedabsence">Notified after noon</button> </td>
				<!-- <td> <input type="checkbox" name="dna[]" value="1" /> </td> -->
			</tr>
			<?php
				}
			?>
			<tr>
				<td> <?= $studentlist ?> </td>
				<td> <input type="text" name="datepicker[]" class="pickdate" value="<?= date('d M, Y', $date)?>" /> </td>
				<td> <input type="text" name="arrived[]" class="picktime" value="15:00" /> </td>
				<td> <input type="text" name="left[]" class="picktime" /> </td>
				<td> <input type="text" name="hourly_charge[]" value="5.00" class="hourlyrate" /> </td>
				<td> <button type="button" class="authedabsence">Notified before noon</button><button type="button" class="unauthedabsence">Notified after noon</button> </td>
				<!-- <td> <input type="checkbox" name="dna[]" value="1" /> </td> -->
			</tr>			
		</tbody>
	</table>
</form>	
<script type="text/javascript">
	$('#donetimesheet').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){
		$('#attendanceform').addClass('ui-state-disabled');
		$.ajax({
			type:	"POST",
			url:	'ajax-recordtimesheet-submit.php',
			data:	$('#attendanceform').serialize(),
			success:function(data) 
				{
					$('#dialog').dialog('close');
				}
		});
	});
	$( ".pickdate" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: $.datepicker.noWeekends
	});
	$('.picktime').timepicker({ hourMin: 12, hourMax: 18, hourGrid: 1, minuteGrid: 10 });
	$('button.authedabsence').button({ icons: { primary: 'ui-icon-close'}, text:false }).click(function(){
		$(this).parent().prev().prev().children('input').val(<?= json_encode(PKB_TIMEOUT_AUTH_ABS) ?>);
	}).addClass('ui-state-highlight');
	$('button.unauthedabsence').button({ icons: { primary: 'ui-icon-circle-close'}, text:false }).click(function(){
		$(this).parent().prev().prev().children('input').val(<?= json_encode(PKB_TIMEOUT_UNAUTH_ABS) ?>);
	}).addClass('ui-state-error');
	
        function addnewrow()
        {
        	$('#attendancelist tbody').append('<tr>'+
			'<td>'+<?=json_encode($studentlist)?>+'</td>'+
			'<td> <input type="text" name="datepicker[]" class="pickdate" value="<?= date('d M, Y')?>" /> </td>'+
			'<td> <input type="text" name="arrived[]" class="picktime" value="15:00" /> </td>'+
			'<td> <input type="text" name="left[]" class="picktime" /> </td>'+
			'<td> <input type="text" name="hourly_charge[]" value="5.00" class="hourlyrate" /> </td>'+
			'<td> <button type="button" class="authedabsence">Notified before noon</button><button type="button" class="unauthedabsence">Notified after noon</button> </td>'+
			//'<td> <input type="checkbox" name="dna[]" value="1" /> </td>'+
		'</tr>');
		$( ".pickdate" ).not('#choosedate').datepicker({ 
			dateFormat: 'd M yy', 
			changeMonth: true, 
			changeYear: true,  
			showOtherMonths: true, 
			selectOtherMonths: true,
			beforeShowDay: $.datepicker.noWeekends
		});
		$('#choosedate').datepicker({ 
			dateFormat: 'DD d M, yy', 
			changeMonth: true, 
			changeYear: true,  
			showOtherMonths: true, 
			selectOtherMonths: true,
			beforeShowDay: $.datepicker.noWeekends
		});
		$('.picktime').timepicker({ hourMin: 12, hourMax: 18, hourGrid: 1, minuteGrid: 10 });
		$('button.authedabsence').button({ icons: { primary: 'ui-icon-close'}, text:false }).click(function(){
			$(this).parent().prev().prev().children('input').val(<?= json_encode(PKB_TIMEOUT_AUTH_ABS) ?>);
		}).addClass('ui-state-highlight');
		$('button.unauthedabsence').button({ icons: { primary: 'ui-icon-circle-close'}, text:false }).click(function(){
			$(this).parent().prev().prev().children('input').val(<?= json_encode(PKB_TIMEOUT_UNAUTH_ABS) ?>);
		}).addClass('ui-state-error');
        }
</script>
