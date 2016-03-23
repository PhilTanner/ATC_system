<?php
	require_once "atc_finance.class.php";
	$ATC = new ATC_Finance();
	
	$ATC->gui_output_page_header('Finance');
?>
	
	
		<h2> Term fees outstanding </h2>
		<table class="tablesorter" id="termfees">
			<thead>
				<tr>
					<th> Rank </th>
					<th> Name </th>
					<th> Amount outstanding </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="2">Total</th>
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
					echo '	<td style="text-align:right">'.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,($obj->amount_paid-$obj->amount_due)).'</td>';
					echo '</tr>';
					$total += ($obj->amount_paid-$obj->amount_due);
				}
?>
			</tbody>
		</table>
		<script> $('#termfees tfoot td').html('<?= $ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $total ) ?>'); </script>
		
		<h2> Cadets needing to pay for activities </h2>
		<table class="tablesorter">
			<thead>
				<tr>
					<th> Rank </th>
					<th> Name </th>
					<th> Activity </th>
					<th> Activity date </th>
					<th> Outstanding </th>
				</tr>
			</thead>
			<tbody>
<?php
				$activitiesoutstanding = $ATC->get_activity_money_outstanding();
				foreach( $activitiesoutstanding as $obj )
				{
					echo '<tr>';
					echo '	<td>'.$obj->rank.'</td>';
					echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
					echo '	<td><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
					echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
					echo '	<td>'.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,($obj->cost-$obj->amount_paid)).'</td>';
					echo '</tr>';
				}
?>
			</tbody>
		</table>
	
	
	
	<script>
		$('a.activity.edit').click(function(e){
			e.preventDefault(); // stop the link actually firing
			var href = $(this).attr("href");
			$('#dialog').empty().load(href).dialog({
				modal: true,
				width: 600,
				title: 'Edit activity details',
				buttons: {
					Cancel: function() {
						$( this ).dialog( "close" );
					}<?php if( $ATC->user_has_permission(ATC_PERMISSION_ACTIVITIES_EDIT) ) { ?> ,
					Save: function() {
						var attendees = '&attendees=0';
						$('#attendees ol.dragdrop li').each(function(index){
							attendees += ","+$(this).attr('personnel_id');
						});
						
						$.ajax({
						   type: "POST",
						   url: 'activities.php',
						   data: $("#editactivity").serialize()+attendees,
						   beforeSend: function()
						   {
							   $('#editactivity').addClass('ui-state-disabled');
						   },
						   complete: function()
						   {
							   $('#editactivity').removeClass('ui-state-disabled');
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
					} <?php } ?>
				  },
				  close: function() { 
					$( this ).dialog( "destroy" ); 
				  },
				  open: function() {
				
					
				}
			});
			return false;
		});
	</script>
<?php
	
	$ATC->gui_output_page_footer('Finance');
?>