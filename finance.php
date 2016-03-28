<?php
	require_once "atc_finance.class.php";
	$ATC = new ATC_Finance();
	
	if( $_SERVER['REQUEST_METHOD'] == 'POST' )
	{
		try {
			if( !$ATC->user_has_permission(ATC_PERMISSION_FINANCE_EDIT) )
	  		  throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$ATC->add_payment( $_POST['personnel_id'], $_POST['amount'], $_POST['reference'], $_POST['payment_type'], $_POST['term_id'] );
			
		} catch (ATCExceptionInsufficientPermissions $e) {
			header("HTTP/1.0 401 Unauthorised");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		} catch (ATCExceptionDBError $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		} catch (ATCExceptionDBConn $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		} catch (ATCException $e) {
			header("HTTP/1.0 400 Bad Request");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		} catch (Exception $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		exit();
	}
	
	
	$ATC->gui_output_page_header('Finance');
	
	if( !$ATC->user_has_permission(ATC_PERMISSION_FINANCE_VIEW) )
	    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");

	try {
		$missinginvoices = $ATC->get_missing_invoices();
		if( count($missinginvoices) )
		{
?>
			<h2> Cadets missing term invoices for attendance </h2>
			<table class="tablesorter" id="missinginvoices">
				<thead>
					<tr>
						<th rowspan="2"> Rank </th>
						<th rowspan="2"> Name </th>
						<th colspan="2"> Term </th>
					</tr>
					<tr>
						<th> Start date </th>
						<th> End date </th>
				</thead>
				<tbody>
<?php
					foreach( $missinginvoices as $obj )
					{
						echo '<tr>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->enddate)).'</td>';
						if( $ATC->user_has_permission(ATC_PERMISSION_FINANCE_EDIT) )
							echo '	<td><a href="?id=0" personnel_id="'.$obj->personnel_id.'" term_id="'.$obj->term_id.'" class="invoice button new">Create</a></td>';
						echo '</tr>';
					}
?>
				</tbody>
			</table>
<?php
		}
	} catch (ATCExceptionInsufficientPermissions $e) { 
		// We just don't show the error if it was a permission issue, that's fine, we don't know who's logged in, after all 
	}
	

?>
	
	
		<h2> Term fees outstanding </h2>
		<table class="tablesorter" id="termfees">
			<thead>
				<tr>
					<th rowspan="2"> Rank </th>
					<th rowspan="2"> Name </th>
					<th colspan="2"> Term </th>
					<th rowspan="2"> Outstanding </th>
				</tr>
				<tr>
					<th> Start date </th>
					<th> End date </th>
			</thead>
			<tfoot>
				<tr>
					<th colspan="4">Total</th>
					<td style="font-weight:bold; text-align:right;"></td>
				</tr>
			</tfoot>
			<tbody>
<?php
				$termfeesoutstanding = $ATC->get_term_fees_outstanding();
				
				$total = 0;
				foreach( $termfeesoutstanding as $obj )
				{
					echo '<tr>';
					echo '	<td>'.$obj->rank.'</td>';
					echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
					echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
					echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->enddate)).'</td>';
					echo '	<td style="text-align:right">'.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->remaining).'</td>';
					if( $ATC->user_has_permission(ATC_PERMISSION_FINANCE_EDIT) )
							echo '	<td><a href="?id=0" personnel_id="'.$obj->personnel_id.'" term_id="'.$obj->term_id.'" class="invoice button pay">Pay</a></td>';
					echo '</tr>';
					$total += $obj->remaining;
				}
?>
			</tbody>
		</table>
		<script> $('#termfees tfoot td').html('<?= $ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $total ) ?>'); </script>
		
		<h2> Cadets needing to pay for activities </h2>
		<table class="tablesorter" id="activityfees">
			<thead>
				<tr>
					<th> Rank </th>
					<th> Name </th>
					<th> Activity </th>
					<th> Activity date </th>
					<th> Cost </th>
					<th> Outstanding </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="5"> Total </th>
					<td style="font-weight:bold; text-align:right"> </td>
				</tr>
			</tfoot>
			<tbody>
<?php
				$activitiesoutstanding = $ATC->get_activity_money_outstanding();
				$total=0;
				foreach( $activitiesoutstanding as $obj )
				{
					echo '<tr>';
					echo '	<td>'.$obj->rank.'</td>';
					echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
					echo '	<td><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
					echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
					echo '	<td style="text-align:right">'.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->due).'</td>';
					echo '	<td style="text-align:right">'.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->remaining).'</td>';
					if( $ATC->user_has_permission(ATC_PERMISSION_FINANCE_EDIT) )
							echo '	<td><a href="?id=0" personnel_id="'.$obj->personnel_id.'" activity_id="'.$obj->activity_id.'" class="invoice button pay">Pay</a></td>';
					echo '</tr>';
					$total += $obj->remaining;
				}
?>
			</tbody>
		</table>
		
		
		<script> $('#activityfees tfoot td').html('<?= $ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $total ) ?>'); </script>
		
		<script>
			$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
			$('a.button.pay').button({ icons: { primary: 'ui-icon-arrowthick-1-sw' }, text: false });

			$('#missinginvoices a.button.new').click(function(e){
				e.preventDefault(); // stop the link actually firing
				var href = $(this).attr("href");
				var term_id = $(this).attr("term_id");
				var personnel_id = $(this).attr("personnel_id");
				$('#dialog').html('<form method="post" id="dialogform"><input type="hidden" name="personnel_id" value="'+personnel_id+'" /><input type="hidden" name="term_id" value="'+term_id+'" /><input type="hidden" name="payment_type" value="<?= ATC_PAYMENT_TYPE_INVOICE_TERM_FEE ?>" /><label for="reference">Invoice #</label><br /><input type="text" maxlength="35" name="reference" id="reference" /><br /><label for="amount">Amount</label><br /><input type="number" min="0.1" step="0.1" name="amount" id="amount" value="<?= ATC_SETTING_FINANCE_TERM_FEES ?>" /></form>').dialog({
					modal: true,
					width: 600,
					title: 'Enter Invoice',
					buttons: {
						Cancel: function() {
							$( this ).dialog( "close" );
						},
						Save: function() {
							$.ajax({
						 	  type: "POST",
						  	 url: href,
						   	data: $("#dialogform").serialize(),
						   	beforeSend: function()
						   	{
							   	$('#newrank').addClass('ui-state-disabled');
						   	},
						   	complete: function()
						   	{
							   	$('#newrank').removeClass('ui-state-disabled');
						   	},
						   	success: function(data)
						   	{
							   	// True to ensure we don't just use a cached version, but get a fresh copy from the server
							   	location.reload(true);
						   	},
						   	error: function(data)
						   	{
							   	$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
								  	modal: true,
								  	//dialogClass: 'ui-state-error',
								  	title: 'Error!',
								  	buttons: {
										Close: function() {
									 	 $( this ).dialog( "close" );
										}
								 	 },
								 	 close: function() { 
										$( this ).dialog( "destroy" ); 
										$('#save_indicator').fadeOut(1500, function(){ $('#save_indicator').remove() });
								 	 },
								  	open: function() {
									 	$('.ui-dialog-titlebar').addClass('ui-state-error');
								  	}
									}).filter('ui-dialog-titlebar');
							   	return false;
						   	}
						 	});
						 
							$( this ).dialog( "close" );
						}
				  	},
				  	close: function() { 
						$( this ).dialog( "destroy" ); 
				  	}
				});
				return false;
			});
			
			$('#termfees a.button.pay').click(function(e){
				e.preventDefault(); // stop the link actually firing
				var href = $(this).attr("href");
				var term_id = $(this).attr("term_id");
				var personnel_id = $(this).attr("personnel_id");
				$('#dialog').html('<form method="post" id="dialogform"><input type="hidden" name="personnel_id" value="'+personnel_id+'" /><input type="hidden" name="term_id" value="'+term_id+'" /><input type="hidden" name="payment_type" value="<?= ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE ?>" /><label for="reference">Receipt/reference #</label><br /><input type="text" maxlength="35" name="reference" id="reference" /><br /><label for="amount">Amount</label><br /><input type="number" min="0.1" step="0.1" name="amount" id="amount" value="<?= ATC_SETTING_FINANCE_TERM_FEES ?>" /></form>').dialog({
					modal: true,
					width: 600,
					title: 'Record payment',
					buttons: {
						Cancel: function() {
							$( this ).dialog( "close" );
						},
						Save: function() {
							$.ajax({
						 	  type: "POST",
						  	 url: href,
						   	data: $("#dialogform").serialize(),
						   	beforeSend: function()
						   	{
							   	$('#newrank').addClass('ui-state-disabled');
						   	},
						   	complete: function()
						   	{
							   	$('#newrank').removeClass('ui-state-disabled');
						   	},
						   	success: function(data)
						   	{
							   	// True to ensure we don't just use a cached version, but get a fresh copy from the server
							   	location.reload(true);
						   	},
						   	error: function(data)
						   	{
							   	$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
								  	modal: true,
								  	//dialogClass: 'ui-state-error',
								  	title: 'Error!',
								  	buttons: {
										Close: function() {
									 	 $( this ).dialog( "close" );
										}
								 	 },
								 	 close: function() { 
										$( this ).dialog( "destroy" ); 
										$('#save_indicator').fadeOut(1500, function(){ $('#save_indicator').remove() });
								 	 },
								  	open: function() {
									 	$('.ui-dialog-titlebar').addClass('ui-state-error');
								  	}
									}).filter('ui-dialog-titlebar');
							   	return false;
						   	}
						 	});
						 
							$( this ).dialog( "close" );
						}
				  	},
				  	close: function() { 
						$( this ).dialog( "destroy" ); 
				  	}
				});
				return false;
			});
			
			$('#activityfees a.button.pay').click(function(e){
				e.preventDefault(); // stop the link actually firing
				var href = $(this).attr("href");
				var term_id = $(this).attr("activity_id");
				var personnel_id = $(this).attr("personnel_id");
				$('#dialog').html('<form method="post" id="dialogform"><input type="hidden" name="personnel_id" value="'+personnel_id+'" /><input type="hidden" name="term_id" value="'+term_id+'" /><input type="hidden" name="payment_type" value="<?= ATC_PAYMENT_TYPE_RECEIPT_ACTIVITY_FEE ?>" /><label for="reference">Receipt/reference #</label><br /><input type="text" maxlength="35" name="reference" id="reference" /><br /><label for="amount">Amount</label><br /><input type="number" min="0.1" step="0.1" name="amount" id="amount" /></form>').dialog({
					modal: true,
					width: 600,
					title: 'Record payment',
					buttons: {
						Cancel: function() {
							$( this ).dialog( "close" );
						},
						Save: function() {
							$.ajax({
						 	  type: "POST",
						  	 url: href,
						   	data: $("#dialogform").serialize(),
						   	beforeSend: function()
						   	{
							   	$('#newrank').addClass('ui-state-disabled');
						   	},
						   	complete: function()
						   	{
							   	$('#newrank').removeClass('ui-state-disabled');
						   	},
						   	success: function(data)
						   	{
							   	// True to ensure we don't just use a cached version, but get a fresh copy from the server
							   	location.reload(true);
						   	},
						   	error: function(data)
						   	{
							   	$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
								  	modal: true,
								  	//dialogClass: 'ui-state-error',
								  	title: 'Error!',
								  	buttons: {
										Close: function() {
									 	 $( this ).dialog( "close" );
										}
								 	 },
								 	 close: function() { 
										$( this ).dialog( "destroy" ); 
										$('#save_indicator').fadeOut(1500, function(){ $('#save_indicator').remove() });
								 	 },
								  	open: function() {
									 	$('.ui-dialog-titlebar').addClass('ui-state-error');
								  	}
									}).filter('ui-dialog-titlebar');
							   	return false;
						   	}
						 	});
						 
							$( this ).dialog( "close" );
						}
				  	},
				  	close: function() { 
						$( this ).dialog( "destroy" ); 
				  	}
				});
				return false;
			});
		
		</script>
<?php
	
	$ATC->gui_output_page_footer('Finance');
?>