<form onsubmit="return false;" id="attendanceform">
	<input type="hidden" name="student" value="<?=(int)$_GET['student_id']?>" />
	<table id="studentlist">
		<thead class="ui-widget-header">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>In</th>
				<th>Out</th>
				<th>Rate p/h</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>*</th>
				<td> <input type="text" id="datepicker" name="datepicker" class="pickdate" value="<?= date('d M, Y')?>" /> </td>
				<td> <input type="text" id="arrived" name="arrived" class="picktime" value="15:00" /> </td>
				<td> <input type="text" id="left" name="left" class="picktime" /> </td>
				<td> <input type="text" id="hourly_charge" name="hourly_charge" value="5.00" class="hourlyrate" /> </td>
				<td> <button type="button" id="addattendance">Add</button> </td>
			</tr>
			<?php
				require_once 'config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
		
				/* check connection */
				if (mysqli_connect_errno()) {
				    printf("Connect failed: %s\n", mysqli_connect_error());
				    exit();
				}
				 
				$query = "
SELECT	attendance.*,
	invoice.reference,
	invoice.issued_date
FROM 	attendance 
	LEFT JOIN invoice
		ON invoice.invoice_id = attendance.invoiced_id
WHERE 	attendance.student_id = ".(int)$_GET['student_id']." 
ORDER BY `date` DESC 
LIMIT 30;";
				
				if ($result = $mysqli->query($query)) 
				{
					$n = 1;
					/* fetch object array */
					while ($obj = $result->fetch_object()) 
					{
						echo '				<tr>'."\n";
						echo '					<th>'.$n++.'</th>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->date)).'</td>'."\n";
						echo '					<td>'.substr($obj->arrived, 0, 5).'</td>'."\n";
						echo '					<td>'.substr($obj->left, 0, 5).'</td>'."\n";
						echo '					<td>'.currency_format(MONEYFORMAT, $obj->hourly_charge).'</td>'."\n";
						echo '					<td>'."\n";
						if( !$obj->issued_date )
							echo '						<button class="delete" data-student="'.$obj->student_id.'" data-date="'.$obj->date.'" data-arrived="'.$obj->arrived.'" data-left="'.$obj->left.'" style="font-size:60%">Delete attendance #'.$obj->attendance_id.'</button>'."\n";
						else
							echo '						<button class="print" data-invoice="'.$obj->invoiced_id.'" style="font-size:60%">Download invoice #'.$obj->invoiced_id.'</button>'."\n";
						echo '					</td>'."\n";
						echo '				</tr>'."\n";
					}
					
					/* free result set */
					$result->close();
				} 
				
				/* close connection */
				$mysqli->close();
			?>
		</tbody>
	</table>
</form>	
<script type="text/javascript">
	$('#addattendance').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){
		$('#attendanceform').addClass('ui-state-disabled');
		$.ajax({
			type:	"POST",
			url:	'ajax-studentattendance-submit.php',
			data:	$('#attendanceform').serialize(),
			success:function(data) 
				{
					$('#dialog').load('ajax-studentattendance.php?student_id=<?=(int)$_GET['student_id']?>');
				}
		});
	});
	$('.delete').button({ icons: { primary: "ui-icon-trash" }, text: false }).click(function(){
		if( confirm('Are you sure you want to delete this record?\n\nThis action cannot be undone.') )
		{
			$('#attendanceform').addClass('ui-state-disabled');
			$.ajax({
				type:	"POST",
				url:	'ajax-studentattendance-delete.php',
				data:	{ student: $(this).data('student'), date: $(this).data('date'), arrived: $(this).data('arrived'), left: $(this).data('left') },
				success:function(data) 
					{
						$('#dialog').load('ajax-studentattendance.php?student_id=<?=(int)$_GET['student_id']?>');
					}
			});
		}
	}).addClass('ui-state-disabled');
	$( ".pickdate" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: $.datepicker.noWeekends
	});
	$('.picktime').timepicker({ hourMin: 12, hourMax: 18, hourGrid: 1, minuteGrid: 10 });
	$('.print').button({ icons: { primary: "ui-icon-print" }, text: false }).click(function(){
		window.open('downloadinvoice.php?invoice='+$(this).data('invoice'),'_blank');
	});
        
</script>
