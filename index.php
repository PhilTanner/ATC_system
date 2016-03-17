<?php
	require_once "atc_documentation.class.php";
	require_once "atc_finance.class.php";
	$ATC = new ATC_Documentation();	
		
	$ATC->gui_output_page_header('Home');
	
	if( $ATC->user_has_permission(ATC_PERMISSION_ACTIVITIES_VIEW) )
		$activities = $ATC->get_activities(date('Y-m-d'), 30);
	else
		$activities = array();
		
	if( count($activities) ) {
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
	</form>
<?php
	}
	
	if( $ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW) )
		$user = $ATC->get_personnel(null, 'ASC', null, 0);
	else
		$user = array();
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
	</form>
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
	</form>
<?php
		}
	}
	
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

	$ATC_Finance = new ATC_Finance();
	$outstandingmoney = $ATC_Finance->get_activity_money_outstanding();
	
	if( $ATC_Finance->user_has_permission(ATC_PERMISSION_FINANCE_VIEW) && count($outstandingmoney) )
	{
?>
		<h2> Cadets needing to pay for activities </h2>
		<table class="tablesorter">
			<thead>
				<tr>
					<th> Rank </th>
					<th> Name </th>
					<th> Activity </th>
					<th> Activity date </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $outstandingmoney as $obj )
					{
						echo '<tr>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="personnel.php?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td><a href="activities.php?id='.$obj->activity_id.'" class="activity edit">'.$obj->title.'</a></td>';
						echo '	<td>'.date(ATC_SETTING_DATE_OUTPUT, strtotime($obj->startdate)).'</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
<?php
	}
	
	if(ATC_DEBUG)
	{
?>
	
	Current user login sessions<br />
	Next/Prev years activity lists<br />
	Document folders<br />
	Activity status (planned/potential/alternative date/complete/etc)<br/>
	Term week 8 with no term fee payments<br />
	Promotion dates. Joiing dates<br />
	User cannot edit key fields for themselves (leaving dates, user levels, etc) - add "sensitive field" values to set_personnel()<br />
	USER_LEVEL_SNCO<br />
	Default personnel list to only unit members<br />
	Change perm structure to user level constants, which *then* map to perm structure to stop DB Unknown issues<br />
	Confirm box on dob entry < 13yrs ago<br />
	
	<h2> Outstanding documentation </h2>
	<ol>
		<li>NZCF11</li>
		<li>NZCF8</li>
		<li>NZCF11a</li>
	</ol>
	
	<h2> Alerts to build </h2>
	<ol>
		<li> Cadets signed up to activities without term fees </li>
		<li> Cadets signed up to activities without NZCF8s </li>
		<li> Cadets who will qualify for uniform </li>
		<li> Activities with unknown start/end dates </li>
		<li> Activities with unknown locations </li>
		<li> Activities with NZCF8 outstanding </li>
		<li> Activities which have passed but have no attendees set up </li>
	</ol>
	
	<h2> Automated emails </h2>
	<ol>
		<li> Email parents/cadets at sign up - link to NZCF8? Incl costs. Reminder, term fees and activity fees </li>
		<li> Email parents/cadets 24hr before. Incl contact numbers &amp; equip list</li>
		<li> Email treasurer at sign on </li>
		<li> Email treasurer at sign out </li>
		<li> Email treasurer at uniform in/out </li>
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