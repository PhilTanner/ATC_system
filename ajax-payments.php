<form onsubmit="return false;" id="inform">
	<input type="hidden" name="student" value="<?=(int)$_GET['student_id']?>" id="studentid" />
	<table id="makeinvoice">
		<caption> Record new payment </caption>
		<thead class="ui-widget-header">
			<tr>
				<th> Reference </th>
				<th> Invoice </th>
				<th> Amount </th>
				<th> Received </th>
				<th> Benefit Payment? </th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td> <input type="text" name="reference" id="reference" value="" /> </td>
				<td>
					<select name="invoice" id="selectinvoice">
						<option value="null" selected="selected">None</option>
						<?php
							require_once 'config.php';
							$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
						
							/* check connection */
							if (mysqli_connect_errno()) {
							    printf("Connect failed: %s\n", mysqli_connect_error());
							    exit();
							}
							
							$query = "
SELECT	student.firstname,
	student.lastname,
	invoice.*,
	(invoice.amount_billed - invoice.amount_paid) AS outstanding
FROM 	student LEFT JOIN invoice
		ON invoice.student_id = student.student_id
		AND (invoice.amount_billed - invoice.amount_paid) > 0
WHERE	student.student_id = ".(int)$_GET['student_id']."
ORDER BY issued_date DESC;";

							if ($result = $mysqli->query($query)) 
								while ($obj = $result->fetch_object()) 
									if( $obj->invoice_id )
										echo '<option value="'.$obj->invoice_id.'" data-outstanding="'.$obj->outstanding.'">'.(strlen(trim($obj->reference))?'<strong>'.$obj->reference.'</strong>':'').' '.date('d M, Y', strtotime($obj->issued_date)).'</option>'."\n";
						?>
					</select>
				</td>
				<td> <input type="text" name="amount" id="amount" value="" class="hourlyrate" required="required" /> </td>
				<td> <input type="text" class="datepicker pickdate" name="date_received" id="date_received" value="<?= date('j M Y') ?>" /> </td>
				<td style="text-align:center;"> 
					<input type="checkbox" name="benefit_payment" id="benefit_payment" value="1" /> <br />
					<input type="text" class="pickdate" name="period_start" id="period_start" value="<?= date('j M Y', strtotime('Last Monday')) ?>" />
					<input type="text" class="pickdate" name="period_end" id="period_end" value="<?= date('j M Y', strtotime('Last Friday')) ?>" />
				</td>
				<td> <button type="button" class="add">Add</button> </td>
			</tr>
		</tbody>
	</table>

	<hr />
	
	<table id="paymentlist">
		<caption>Previous payments</caption>
		<thead class="ui-widget-header">
			<tr>
				<th>#</th>
				<th>Ref.</th>
				<th>Invoice</th>
				<th>Received</th>
				<th>Amount</th>
				<th>Benefit Payment</th>
			</tr>
		</thead>
		<tbody>
			<?php			
				$query = "
SELECT	payments.amount,
	payments.reference,
	payments.date_received,
	payments.benefit_payment,
	payments.period_start,
	payments.period_end,
	invoice.reference AS invoice_ref,
	invoice.issued_date
FROM 	payments LEFT JOIN invoice
		ON payments.invoice_id = invoice.invoice_id 
WHERE 	payments.student_id = ".(int)$_GET['student_id']." 
ORDER BY date_received DESC, payment_id DESC;";
				
				if ($result = $mysqli->query($query)) 
				{
					$n = 1;
					/* fetch object array */
					while ($obj = $result->fetch_object()) 
					{
						echo '				<tr>'."\n";
						echo '					<th>'.$n++.'</th>'."\n";
						echo '					<td>'.$obj->reference.'</td>'."\n";
						echo '					<td>'.($obj->invoice_ref?$obj->invoice_ref:($obj->issued_date?date('d M, Y', strtotime($obj->issued_date)):'')).'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->date_received)).'</td>'."\n";
						echo '					<td class="currency">'.currency_format(MONEYFORMAT, $obj->amount).'</td>'."\n";
						echo '					<td>';
						if( $obj->benefit_payment )
							echo 'Yes <span style="font-size:75%">('.date('d M, Y', strtotime($obj->period_start)).'&ndash;'.date('d M, Y', strtotime($obj->period_end)).')</span>';
						else echo 'No';
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
	$('#selectinvoice').change(function(){
		$('#amount').val($('#selectinvoice option:selected').data('outstanding'));
	});
	$('button.add').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){
		$('#makeinvoice').addClass('ui-state-disabled');
		$.ajax({
			type:	"POST",
			url:	'ajax-invoice-pay.php',
			data:	{ 
					reference:	$('#reference').val(), 
					invoice:	$('#selectinvoice').val(), 
					amount: 	$('#amount').val(), 
					benefit: 	($('#benefit_payment:checked').val()?1:0),
					student:	$('#studentid').val(),
					date_received:	$('#date_received').val(),
					period_start:	($('#benefit_payment:checked')?$('#period_start').val():'N/A'),
					period_end:	($('#benefit_payment:checked')?$('#period_end').val():'N/A')
				},
			success:function(data) 
				{
					refreshstudentlist();
					$('button.receipt[data-student="'+$('#studentid').val()+'"]').click()
				}
		});
	});
	$( ".datepicker" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: $.datepicker.noWeekends
	});
	$('#period_start').datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: enableMondays
	});
	$('#period_end').datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: enableFridays
	});
	function enableFridays(date) { var day = date.getDay(); return [(day == 5), ''];  }
	function enableMondays(date) { var day = date.getDay(); return [(day == 1), ''];  }
	$('#period_start, #period_end').hide();
	$('#benefit_payment').change(function(){ if($(this).is(':checked')) $('#period_start, #period_end').show(); else $('#period_start, #period_end').hide(); });
</script>
