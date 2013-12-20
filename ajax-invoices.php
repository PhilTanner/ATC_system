<form onsubmit="return false;" id="inform">
	<input type="hidden" name="student" value="<?=(int)$_GET['student_id']?>" id="studentid" />
	<table id="makeinvoice">
		<caption> Create new invoice </caption>
		<thead class="ui-widget-header">
			<tr>
				<th> Reference </th>
				<th> Start date </th>
				<th> End date </th>
				<!-- <th> Incl. Prev. Outstanding </th> -->
				<th></th>
			</tr>
		</thead>
		<?php
			require_once './config.php';
			//$PKBASC = new PKBASC();
			$lastinvoice = $PKBASC->getLastInvoiceForStudent( (int)$_GET['student_id'] );
			if( $lastinvoice && isset($lastinvoice->end_date) ) 
				$lastinvoiceddate = strtotime( $lastinvoice->end_date );
			else 	$lastinvoiceddate = 0;
		?>
		<tbody>
			<tr>
				<td> <input type="text" class="pickdate" name="reference" id="reference" /> </td>
				<td> <input type="text" class="datepicker pickdate" name="startdate" id="startdate" value="<?= ( $lastinvoice?date('d M Y', $lastinvoiceddate+60*60*24):'' ) ?>" /> </td>
				<td> <input type="text" class="datepicker pickdate" name="enddate" id="enddate" /> </td>
				<!-- <td style="text-align:center;"> <input type="checkbox" value="1" name="includeoutstanding" checked="checked" id="includeoutstanding" /> </td> -->
				<td> <button type="button" class="generateinvoice">Invoice</button> </td>
			</tr>
		</tbody>
	</table>
	
	<hr />
	
	<table id="invoicelist">
		<caption>Previous Invoices</caption>
		<thead class="ui-widget-header">
			<tr>
				<th>#</th>
				<th>Ref.</th>
				<th>Issued</th>
				<th>Start</th>
				<th>End</th>
				<th>Billed</th>
				<th>Oustanding</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
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
FROM 	invoice INNER JOIN student
		ON invoice.student_id = student.student_id 
WHERE 	student.student_id = ".(int)$_GET['student_id']." 
ORDER BY issued_date DESC;";
				
				if ($result = $mysqli->query($query)) 
				{
					$n = 1;
					/* fetch object array */
					while ($obj = $result->fetch_object()) 
					{
						echo '				<tr>'."\n";
						echo '					<th>'.$n++.'</th>'."\n";
						echo '					<td>'.$obj->reference.'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->issued_date)).'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->start_date)).'</td>'."\n";
						echo '					<td>'.date('d M, Y', strtotime($obj->end_date)).'</td>'."\n";
						echo '					<td class="currency">'.currency_format(MONEYFORMAT_TEXTUAL, $obj->amount_billed).'</td>'."\n";
						echo '					<td class="outstanding currency">'.currency_format(MONEYFORMAT_TEXTUAL, ($obj->amount_billed-$obj->amount_paid)).'</td>'."\n";
						echo '					<td>'."\n";
						if( !is_null($obj->PDF) || file_exists('invoices/'.$obj->invoice_id.'.pdf') )
						{
							echo '						<button class="print" data-invoice="'.$obj->invoice_id.'">Download</button>'."\n";
							echo '						<button class="delete" data-invoice="'.$obj->invoice_id.'">Delete</button>'."\n";
						}
						/*
						echo '						<button class="email" data-emailto="'.$obj->parent_email.'">Email</button>'."\n";
						*/
						//echo '						<button class="paid" data-invoice="'.$obj->invoice_id.'" data-outstanding="'.($obj->amount_billed-$obj->amount_paid).'">Mark paid</button>'."\n";
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
	$('.print').button({ icons: { primary: "ui-icon-print" }, text: false }).click(function(){
		window.open('downloadinvoice.php?invoice='+$(this).data('invoice'),'_blank');
	});
	$('#invoicelist .delete').button({ icons: { primary: "ui-icon-trash" }, text: false }).click(function(){
		if( confirm('Are you REALLY sure you want to delete this invoice?') )
			$.ajax({
				type:	"POST",
				url:	'ajax-invoice-delete.php',
				data:	{ 
						invoice: $(this).data('invoice')
					},
				success:function(data) 
					{
						refreshstudentlist();
						$('button.invoice[data-student="'+$('#studentid').val()+'"]').click();
					}
			});
	}).addClass('ui-state-disabled');
	$('.email').button({ icons: { primary: "ui-icon-mail-closed" }, text: false }).click(function(){
		window.location = 'mailto:'+$(this).data('emailto');
	});
	$('.paid').button({ icons: { primary: "ui-icon-arrowthickstop-1-s" }, text: false }).click(function(){
		if( !$(this).hasClass('ui-state-disabled') )
		{
			var button = $(this);
			$.ajax({
				type:	"POST",
				url:	'ajax-invoice-pay.php',
				data:	{ 
						invoice: $(this).data('invoice'), 
						amount: $(this).data('outstanding'), 
						student: $('#studentid').val()
					},
				success:function(data) 
					{
						refreshstudentlist();
						button.parent().parent().addClass('ui-state-disabled').children('.outstanding').html('0.00');
						button.addClass('ui-state-disabled');
					}
			});
		}
	});
	$('.generateinvoice').button({ icons: { primary: "ui-icon-copy" } }).click(function(){
		$('#makeinvoice').addClass('ui-state-disabled');
		$.ajax({
			type:	"POST",
			url:	'ajax-invoice-generate.php',
			data:	{ 
					student:$('#studentid').val(), 
					start: 	$('#startdate').val(), 
					end: 	$('#enddate').val(), 
					reference: $('#reference').val()
				},
			success:function(data) 
				{
					refreshstudentlist();
					$('button.invoice[data-student="'+$('#studentid').val()+'"]').click();
				}
		});
	});
	$('.picktime').timepicker({ hourMin: 12, hourMax: 18, hourGrid: 1, minuteGrid: 10 });
	$('.paid[data-outstanding="0"]').addClass('ui-state-disabled');
	/*$( ".datepicker" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: $.datepicker.noWeekends
	});*/
	$('#startdate').datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: enableMondays
	});
	$('#enddate').datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		beforeShowDay: enableSundays
	});
	function enableSundays(date) { var day = date.getDay(); return [(day == 0), ''];  }
	function enableMondays(date) { var day = date.getDay(); return [(day == 1), ''];  }
        
</script>
