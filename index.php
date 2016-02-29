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
		
		<h2> Upcoming events (next 30 days)</h2>
		<table>
			<thead>
				<tr>
					<th rowspan="2"> Activity </th>
					<th rowspan="2"> <acronym title="Officer In Charge">OIC</acronym> </th>
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
						echo '	<td><span class="ui-icon ui-icon-'.($obj->nzcf_status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> '.$obj->title.'</td>';
						echo '	<td>'.$obj->rank.' '.$obj->lastname.', '.$obj->firstname.'</td>';
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
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
	</script>
<?php
	}
	
	if(ATC_DEBUG)
	{
?>
	add mufti<br/>
	Add edit activity permission if it's their activity
	
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