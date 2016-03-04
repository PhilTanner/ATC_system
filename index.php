<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$ATC->gui_output_page_header('Home');
	
	if( $ATC->user_has_permission(ATC_PERMISSION_ACTIVITIES_VIEW) )
		$activities = $ATC->get_activities(date('Y-m-d'), 30);
	else
		$activities = array();
	if( count($activities) ) {
?>
		
		<h2> Upcoming events</h2>
		<table>
			<thead>
				<tr>
					<th rowspan="2"> Activity </th>
					<th rowspan="2"> Officer In Charge </th>
					<th rowspan="2"> 2<sup>nd</sup> Contact </th>
					<th colspan="2"> Date </th>
					<th colspan="2"> Attendance </th>
				</tr>
				<tr>
					<th> Assemble </th>
					<th> Dispersal </th>
					<th> OFF </th>
					<th> CDT </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $activities as $obj )
					{
						echo '<tr>';
						echo '	<td'.(array_search($ATC->get_currentuser_id(),explode(',',$obj->attendees))!==false?' class="highlighted"':'').'><span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> '.$obj->title.'</td>';
						echo '	<td'.($obj->personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'>'.$obj->display_name.'</td>';
						echo '	<td'.($obj->twoic_personnel_id==$ATC->get_currentuser_id()?' class="highlighted"':'').'>'.$obj->twoic_display_name.'</td>';
						echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->startdate)).'</td>';
						echo '	<td>'.date(ATC_SETTING_DATETIME_OUTPUT, strtotime($obj->enddate)).'</td>';
						echo '	<td style="text-align:center;">'.$obj->officers_attending.'</td>';
						echo '	<td style="text-align:center;">'.$obj->cadets_attending.'</td>';
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
		<table>
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
		<table>
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

	
	if(ATC_DEBUG)
	{
?>
	
	Current user login sessions<br />
	New user type - emergency contact (Trudi holding 8s)<br />
	Next/Prev years activity lists<br />
	Autocomplete searches<br>
	Fixed height user sortables - scrollable<br/>
	Flights<br />
	Document folders<br />
	
	<h2> Outstanding documentation </h2>
	<ol>
		<li>NZCF11</li>
		<li>NZCF8</li>
		<li>NZCF11a</li>
		<li>NZCF20</li>
	</ol>
	
	<h2> Alerts to build </h2>
	<ol>
		<li> Cadets signed up to activities without paying </li>
		<li> Cadets signed up to activities without term fees </li>
		<li> Cadets signed up to activities without NZCF8s </li>
		<li> Cadets who will qualify for uniform </li>
		<li> Cadets who've not attended in 2/3 weeks </li>
		<li> Activities with unknown start/end dates </li>
		<li> Activities with unknown locations </li>
		<li> Activities with NZCF8 outstanding </li>
		<li> Activities which have passed but have no attendees set up </li>
	</ol>
	
	<h2> Automated emails </h2>
	<ol>
		<li> Email parents/cadets at sign up - link to NZCF8? Incl costs. Reminder, term fees and activity fees </li>
		<li> Email parents/cadets night before. Incl contact numbers </li>
		<li> Activity organiser the emergency contact sheet </li>
		<li> Email treasurer at sign on </li>
		<li> Email treasurer at sign out </li>
		<li> Email treasurer at uniform in/out </li>
	</ol>
	
<?php
	}

	$ATC->gui_output_page_footer('Home');
?>