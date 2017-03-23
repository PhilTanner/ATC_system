<?php
	require_once "atc_documentation.class.php";
	require_once "atc_finance.class.php";
	$ATC = new ATC_Documentation();
	$ATC_Finance = new ATC_Finance();
	
	$ATC->gui_output_page_header('Home');


	try {
		$activities = $ATC->get_activities_paperwork(date('Y-m-d'), 30, (ATC_ACTIVITY_PAPERWORK_TYPE_12_CUCDR | ATC_ACTIVITY_PAPERWORK_TYPE_11_CUCDR) );
		
		if( count($activities) ) 
		{
?>
		
			<h2> Paperwork due to CUCDR</h2>
			<table class="tablesorter">
				<thead>
					<tr>
						<th rowspan="2"> Activity </th>
						<th rowspan="2"> Officer In Charge </th>
						<th rowspan="2"> 2<sup>nd</sup> Contact </th>
						<th colspan="4"> Date </th>
					</tr>
					<tr>
						<th> NZCF12 </th>
						<th> NZCF11 </th>
						<th> Assemble </th>
						<th> Dispersal </th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $activities as $obj )
						{
							echo '<tr>';
							echo '	<td'.(array_search($ATC->get_currentuser_id(),explode(',',$obj->attendees))!==false?' class="highlighted"':'').'><!--<span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> --><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
							echo '	<td'.($obj->personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
							echo '	<td'.($obj->twoic_personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->twoic_personnel_id.'">'.$obj->twoic_display_name.'</a></td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf12_to_cucdr)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf11_to_cucdr)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->startdate)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->enddate)).'</td>';
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
	
	
	try {
		$activities = $ATC->get_activities_paperwork(date('Y-m-d'), 30, (ATC_ACTIVITY_PAPERWORK_TYPE_12_CTFSU | ATC_ACTIVITY_PAPERWORK_TYPE_11_CTFSU) );
		
		if( count($activities) ) 
		{
?>
		
			<h2> Paperwork due to CTFSU</h2>
			<table class="tablesorter">
				<thead>
					<tr>
						<th rowspan="2"> Activity </th>
						<th rowspan="2"> Officer In Charge </th>
						<th rowspan="2"> 2<sup>nd</sup> Contact </th>
						<th colspan="4"> Date </th>
					</tr>
					<tr>
						<th> NZCF12 </th>
						<th> NZCF11 </th>
						<th> Assemble </th>
						<th> Dispersal </th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $activities as $obj )
						{
							echo '<tr>';
							echo '	<td'.(array_search($ATC->get_currentuser_id(),explode(',',$obj->attendees))!==false?' class="highlighted"':'').'><!--<span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> --><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
							echo '	<td'.($obj->personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
							echo '	<td'.($obj->twoic_personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->twoic_personnel_id.'">'.$obj->twoic_display_name.'</a></td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf12_to_hq)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf11_to_hq)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->startdate)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->enddate)).'</td>';
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
	
	
	try {
		$activities = $ATC->get_activities_paperwork(date('Y-m-d'), 30, (ATC_ACTIVITY_PAPERWORK_TYPE_8_RETURN | ATC_ACTIVITY_PAPERWORK_TYPE_8_ISSUED) );
		
		if( count($activities) ) 
		{
?>
		
			<h2> NZCF8s Due out/return </h2>
			<table class="tablesorter">
				<thead>
					<tr>
						<th rowspan="2"> Activity </th>
						<th rowspan="2"> Officer In Charge </th>
						<th rowspan="2"> 2<sup>nd</sup> Contact </th>
						<th colspan="4"> Date </th>
					</tr>
					<tr>
						<th> NZCF12 </th>
						<th> NZCF11 </th>
						<th> Assemble </th>
						<th> Dispersal </th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $activities as $obj )
						{
							echo '<tr>';
							echo '	<td'.(array_search($ATC->get_currentuser_id(),explode(',',$obj->attendees))!==false?' class="highlighted"':'').'><!--<span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> --><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
							echo '	<td'.($obj->personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
							echo '	<td'.($obj->twoic_personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->twoic_personnel_id.'">'.$obj->twoic_display_name.'</a></td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf8_issued)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->nzcf8_return)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->startdate)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->enddate)).'</td>';
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
	
	try {
		$activities = $ATC->get_activities(date('Y-m-d'), 30);
		
		if( count($activities) ) 
		{
?>
		
			<h2> Upcoming events</h2>
			<table class="tablesorter">
				<thead>
					<tr>
						<th rowspan="2"> Activity </th>
						<th rowspan="2"> Officer In Charge </th>
						<th rowspan="2"> 2<sup>nd</sup> Contact </th>
						<th colspan="2"> Date </th>
					</tr>
					<tr>
						<th> Assemble </th>
						<th> Dispersal </th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $activities as $obj )
						{
							echo '<tr>';
							echo '	<td'.(array_search($ATC->get_currentuser_id(),explode(',',$obj->attendees))!==false?' class="highlighted"':'').'><!--<span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> --><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
							echo '	<td'.($obj->personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
							echo '	<td'.($obj->twoic_personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'><a href="personnel.php?id='.$obj->twoic_personnel_id.'">'.$obj->twoic_display_name.'</a></td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->startdate)).'</td>';
							echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->enddate)).'</td>';
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
	
	try {
		$user = $ATC->get_personnel(null, 'ASC', null, 0);
	
		if( count($user) ) {
		
			$bdays = array();
			$annivs = array();
					
			foreach( $user as $obj )
			{
				$bday = (int)date("U", strtotime(date('Y').date('-m-d', strtotime($obj->dob))));
				$anniv = (int)date("U", strtotime(date('Y').date('-m-d', strtotime($obj->joined_date))));
				$today = time();
				$nextmonth = time()+(30*24*60*60);
						
				if( $bday > $today && $bday <= $nextmonth )
					$bdays[] = $obj;
				if( $anniv > $today && $anniv <= $nextmonth )
					$annivs[] = $obj;
			}

			// Sort our birthdays and anniversaries into upcoming order.
			// Complicated usort function because we're not sorting on how old they are, but the next b'day THIS year
			usort($bdays, function($a, $b){ return ((int)date("U", strtotime(date('Y').date('-m-d', strtotime($a->dob)))) < (int)date("U", strtotime(date('Y').date('-m-d', strtotime($b->dob)))) ? -1 : 1); });
			// Same for anniversaries.
			usort($annivs, function($a, $b){ return ((int)date("U", strtotime(date('Y').date('-m-d', strtotime($a->joined_date)))) < (int)date("U", strtotime(date('Y').date('-m-d', strtotime($b->joined_date)))) ? -1 : 1); });
		
			if(count($bdays))
			{
?>
				<h2> Upcoming birthdays</h2>
				<table class="tablesorter">
					<thead>
						<tr>
							<th> Name </th>
							<th> Date </th>
							<th> Age </th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach( $bdays as $obj )
							{
								echo '<tr>';
								echo '	<td>'.$obj->display_name.'</td>';
								echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->dob)).'</td>';
								echo '	<td>'.((int)date('Y') - (int)date('Y', strtotime($obj->dob))).'</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
<?php
			}
		
			if(count($annivs))
			{
?>
				<h2> Upcoming anniversaries</h2>
				<table class="tablesorter">
					<thead>
						<tr>
							<th> Name </th>
							<th> Date </th>
							<th> Years </th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach( $annivs as $obj )
							{
								echo '<tr>';
								echo '	<td>'.$obj->display_name.'</td>';
								echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->joined_date)).'</td>';
								echo '	<td>'.((int)date('Y') - (int)date('Y', strtotime($obj->joined_date))).'</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
<?php
			}
		}	
	} catch (ATCExceptionInsufficientPermissions $e) { 
		// We just don't show the error if it was a permission issue, that's fine, we don't know who's logged in, after all 
	}
	
	try {
		$missingnok = $ATC->get_cadet_without_nok();
		if( count( $missingnok ) )
		{
?>
			<h2> Cadets missing Next of Kin records </h2>
			<table class="tablesorter">
				<thead>
					<tr>
						<th> Rank </th>
						<th> Name </th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $missingnok as $obj )
						{
							echo '<tr>';
							echo '	<td>'.$obj->rank.'</td>';
							echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
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
	/*
	try {
		$missinginvoices = $ATC_Finance->get_missing_invoices();
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
		
	try {
		$termfeesoutstanding = $ATC_Finance->get_term_fees_outstanding();
		if( count($termfeesoutstanding) )
		{
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
					$total = 0;
					foreach( $termfeesoutstanding as $obj )
					{
						
						$class = (strtotime("+5 weeks", strtotime($obj->startdate))<time()?"ui-state-error":(strtotime("+6 weeks", strtotime($obj->startdate))<time()?"ui-state-highlight":''));
						echo '<tr class="'.$class.'">';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->enddate)).'</td>';
						echo '	<td style="text-align:right">'.$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->remaining).'</td>';
						echo '</tr>';
						$total += $obj->remaining;
					}
?>
				</tbody>
			</table>
			<script> $('#termfees tfoot td').html('<?= $ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $total ) ?>'); </script>
<?php
		}
	} catch (ATCExceptionInsufficientPermissions $e) { 
		// We just don't show the error if it was a permission issue, that's fine, we don't know who's logged in, after all 
	}
	
	
	try {
		$activitiesoutstanding = $ATC_Finance->get_activity_money_outstanding();
		
		if( count($activitiesoutstanding) )
		{
?>
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
					$total=0;
					foreach( $activitiesoutstanding as $obj )
					{
						echo '<tr>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
						echo '	<td style="text-align:right">'.$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->due).'</td>';
						echo '	<td style="text-align:right">'.$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT,$obj->remaining).'</td>';
						echo '</tr>';
						$total += $obj->remaining;
					}
?>
				</tbody>
			</table>
			<script> $('#activityfees tfoot td').html('<?= $ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $total ) ?>'); </script>

<?php
		}
	} catch (ATCExceptionInsufficientPermissions $e) { 
		// We just don't show the error if it was a permission issue, that's fine, we don't know who's logged in, after all 
	}
	*/
	try {
		$cadetsriskingsignout = $ATC->get_cadets_risking_sign_off();
		if( count($cadetsriskingsignout) )
		{
?>
			<h2> Cadets risking being signed out (BETA - might not be accurate)</h2>
			<table class="tablesorter" id="cadetsriskingsignout">
				<thead>
					<tr>
						<th> Rank </th>
						<th> Name </th>
						<th> Parade nights missed </th>
					</tr>
				</thead>
				<tbody>
<?php
					foreach( $cadetsriskingsignout as $obj )
					{
						echo '<tr>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td>'.$obj->missed_nights.'</td>';
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
	
	
	
	if(ATC_DEBUG)
	{
?>
	<hr />
	<h2> Future enhancement ideas </h2>
	<ol>
		<li>Current user login sessions<br />
		<li>Next/Prev years activity lists<br />
		<li>Document folders<br />
		<li>Activity status (planned/potential/alternative date/complete/etc)<br/>
		<li>User cannot edit key fields for themselves (leaving dates, user levels, etc) - add "sensitive field" values to set_personnel()<br />
		<li>Change perm structure to user level constants, which *then* map to perm structure to stop DB Unknown issues<br />
		<li>Confirm box on dob entry < 13yrs ago<br />
		<li>Cadet alternate email<br />
		<li>Popups/links standardised into single JS file<br /> 
		<li> New check - teachers on leave </li>
		<li> New check - teachers double booked </li>
		<li> new check - trainig locations double booked </li>
		
		<li>
			<h3> Outstanding documentation </h3>
			<ol>
				<li>NZCF11</li>
				<li>NZCF8</li>
				<li>NZCF11a</li>
			</ol>
		</li>
		<li>
			<h3> Alerts to build </h3>
			<ol>
				<li> Cadets signed up to activities without term fees </li>
				<li> Cadets signed up to activities without NZCF8s </li>
				<li> Cadets who will qualify for uniform </li>
				<li> Activities with unknown start/end dates </li>
				<li> Activities with unknown locations </li>
				<li> Activities with NZCF8 outstanding </li>
				<li> Activities which have passed but have no attendees set up </li>

			</ol>
		</li>
		<li>
			<h3> Automated emails </h3>
			<ol>
				<li> Email parents/cadets at sign up - link to NZCF8? Incl costs. Reminder, term fees and activity fees </li>
				<li> Email parents/cadets 24hr before. Incl contact numbers &amp; equip list</li>
				<li> Email treasurer at sign on </li>
				<li> Email treasurer at sign out </li>
				<li> Email treasurer at uniform in/out </li>
			</ol>
		</li>
	</ol>	
	
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
	}

	$ATC->gui_output_page_footer('Home');
?>