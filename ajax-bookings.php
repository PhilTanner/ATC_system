<form onsubmit="return false;" id="inform">
	<input type="hidden" name="student" value="<?=(int)$_GET['student_id']?>" id="studentid" />
	<table id="expected">
		<caption> Expected bookings </caption>
		<thead class="ui-widget-header">
			<tr>
				<th> # </th>
				<th> Start date </th>
				<th> End date </th>
				<th> Mon </th>
				<th> Tue </th>
				<th> Wed </th>
				<th> Thu </th>
				<th> Fri </th>
				<th> Options </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th> * </th>
				<td> <input type="text" class="datepicker pickdate" name="start_date" id="start_date" /> </td>
				<td> <input type="text" class="datepicker pickdate" name="end_date" id="end_date" /> </td>
				<td style="text-align:center;"> <input type="checkbox" value="1" name="mon" id="mon" /> </td>
				<td style="text-align:center;"> <input type="checkbox" value="1" name="tue" id="tue" /> </td>
				<td style="text-align:center;"> <input type="checkbox" value="1" name="wed" id="wed" /> </td>
				<td style="text-align:center;"> <input type="checkbox" value="1" name="thu" id="thu" /> </td>
				<td style="text-align:center;"> <input type="checkbox" value="1" name="fri" id="fri" /> </td>
				<td> <button type="button" class="add">Add</button> </td>
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
SELECT	* 
FROM 	recurring_booking 
WHERE 	student_id = ".(int)$_GET['student_id']." 
ORDER BY start_date DESC, end_date DESC;";
				
				if ($result = $mysqli->query($query)) 
				{
					$n = 1;
					/* fetch object array */
					while ($obj = $result->fetch_object()) 
					{
						echo '				<tr>'."\n";
						echo '					<th>'.$n++.'</th>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->start_date)).'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->end_date)).'</td>'."\n";
						echo '					<td>'.($obj->mon?'Yes':'No').'</td>'."\n";
						echo '					<td>'.($obj->tue?'Yes':'No').'</td>'."\n";
						echo '					<td>'.($obj->wed?'Yes':'No').'</td>'."\n";
						echo '					<td>'.($obj->thu?'Yes':'No').'</td>'."\n";
						echo '					<td>'.($obj->fri?'Yes':'No').'</td>'."\n";
						echo '					<td>'."\n";
						echo '						<button class="delete" data-recurringbooking="'.$obj->recurring_booking_id.'">Delete</button>'."\n";
						echo '					</td>'."\n";					
						echo '				</tr>'."\n";
					}
					
					/* free result set */
					$result->close();
				} 
			?>
		</tbody>
	</table>
	
	<hr />
	
	<table id="excepted">
		<caption>Known exceptions</caption>
		<thead class="ui-widget-header">
			<tr>
				<th>#</th>
				<th>Reason</th>
				<th>Date</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th> * </th>
				<td> <input type="text" class="pickdate" name="reason" id="reason" /> </td>
				<td> <input type="text" class="datepicker pickdate" name="exception_date" id="exception_date" /> </td>
				<td> <button type="button" class="add">Add</button> </td>
			</tr>
			<?php
				$query = "
SELECT	* 
FROM 	booking_exception 
WHERE 	student_id = ".(int)$_GET['student_id']." 
ORDER BY `date` DESC;";
				
				if ($result = $mysqli->query($query)) 
				{
					$n = 1;
					/* fetch object array */
					while ($obj = $result->fetch_object()) 
					{
						echo '				<tr>'."\n";
						echo '					<th>'.$n++.'</th>'."\n";
						echo '					<td>'.$obj->reason.'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->date)).'</td>'."\n";
						echo '					<td>'."\n";
						echo '						<button class="delete" data-exception="'.$obj->exception_id.'">Delete</button>'."\n";
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
	$('#expected button.add').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){
		$.ajax({
			type:	"POST",
			url:	'ajax-booking-submit.php',
			data:	{ 
					start_date:	$('#start_date').val(), 
					end_date:	$('#end_date').val(), 
					mon:		($('#mon:checked').val()?1:0),
					tue:		($('#tue:checked').val()?1:0),
					wed:		($('#wed:checked').val()?1:0),
					thu:		($('#thu:checked').val()?1:0),
					fri:		($('#fri:checked').val()?1:0),
					student:	$('#studentid').val()
				},
			success:function(data) 
				{
					$('button.booking[data-student="'+$('#studentid').val()+'"]').click()
				}
		});
	});
	$('#excepted button.add').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){
		$.ajax({
			type:	"POST",
			url:	'ajax-booking-submit.php',
			data:	{ 
					reason:	$('#reason').val(), 
					'date':	$('#exception_date').val(),
					student:$('#studentid').val()
				},
			success:function(data) 
				{
					$('button.booking[data-student="'+$('#studentid').val()+'"]').click()
				}
		});
	});	
	$('#expected button.delete').button({ icons: { primary: "ui-icon-trash" }, text: false }).click(function(){
		if( confirm('Are you sure you want to delete this item?') )
			$.ajax({
				type:	"POST",
				url:	'ajax-booking-submit.php',
				data:	{ 
						booking_del:	$(this).data('recurringbooking')
					},
				success:function(data) 
					{
						$('button.booking[data-student="'+$('#studentid').val()+'"]').click()
					}
			});
	});
	$('#excepted button.delete').button({ icons: { primary: "ui-icon-trash" }, text: false }).click(function(){
		if( confirm('Are you sure you want to delete this item?') )
			$.ajax({
				type:	"POST",
				url:	'ajax-booking-submit.php',
				data:	{ 
						exception:	$(this).data('exception')
					},
				success:function(data) 
					{
						$('button.booking[data-student="'+$('#studentid').val()+'"]').click()
					}
			});
	});
	$('.picktime').timepicker({ hourMin: 12, hourMax: 18, hourGrid: 1, minuteGrid: 10 });
	$('.paid[data-outstanding="0"]').addClass('ui-state-disabled');
	$( ".datepicker" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: $.datepicker.noWeekends
	});
        
</script>
