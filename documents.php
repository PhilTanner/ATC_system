<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$ATC->gui_output_page_header('Documents');
	$datepicked = strtotime("-1 month");
	if( isset($_GET['date']) && strtotime($_GET['date']) )
		$datepicked = strtotime($_GET['date']);
	$year = date("Y", $datepicked);
	$month = date("m", $datepicked);
	
	
	$nzcf20 = $ATC->nzcf20_stats( $year, $month );
	
	$ATC->dump_userperms();
?>
	
	<form name="datepicker" id="datepicker">
		<fieldset>
			<legend>Choose date</legend>
			<label for="month">Pick a date:</label>
			<input type="date" name="date" id="date" value="<?=date( ATC_SETTING_DATE_INPUT, $datepicked)?>" /><br />
			<button type="submit" class="update">Update</button>			
		</fieldset>
	</form>
	
	<h1> NZCF20 </h1>
	
	<div style="width:40%; float: left;">
		<table>
			<thead>
				<tr>
					<th colspan="6"> 1. Enrolled Cadet Strength at Month's End </th>
				</tr>
				<tr>
					<th></th>
					<th>Male</th>
					<th></th>
					<th>Female</th>
					<th></th>
					<th>Total</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th> Totals </th>
					<td align="center"> <?= $nzcf20[0][6][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][6][0]; ?></td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][6][1]+$nzcf20[0][6][0]; ?></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td> WO / WOII / W/O </td>
					<td align="center"> <?= $nzcf20[0][0][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][0][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][0][1]+$nzcf20[0][0][0]; ?> </td>
				</tr>
				<tr>
					<td> CPO / SSGT / F/S </td>
					<td align="center"> <?= $nzcf20[0][1][1]; ?></td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][1][0]; ?></td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][1][1]+$nzcf20[0][1][0]; ?> </td>
				</tr>
				<tr>
					<td> PO / SGT </td>
					<td align="center"> <?= $nzcf20[0][2][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][2][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][2][1]+$nzcf20[0][2][0]; ?> </td>
				</tr>
				<tr>
					<td> LCDT / CPL </td>
					<td align="center"> <?= $nzcf20[0][3][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][3][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][3][1]+$nzcf20[0][3][0]; ?> </td>
				</tr>
				<tr>
					<td> ABCDT / LCPL / LACDT </td>
					<td align="center"> <?= $nzcf20[0][4][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][4][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][4][1]+$nzcf20[0][4][0]; ?> </td>
				</tr>
				<tr>
					<td> CDTs </td>
					<td align="center"> <?= $nzcf20[0][5][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][5][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][5][1]+$nzcf20[0][5][0]; ?>  </td>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="5"> 2. Cadet Attendance (Week night parades) </th>
				</tr>
				<tr>
					<th> Week 1 </th>
					<th> Week 2 </th>
					<th> Week 3 </th>
					<th> Week 4 </th>
					<th> Week 5 </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> <?= (isset($nzcf20[1][0]->count)?$nzcf20[1][0]->count:'')?> </td>
					<td> <?= (isset($nzcf20[1][1]->count)?$nzcf20[1][1]->count:'')?> </td>
					<td> <?= (isset($nzcf20[1][2]->count)?$nzcf20[1][2]->count:'')?>  </td>
					<td> <?= (isset($nzcf20[1][3]->count)?$nzcf20[1][3]->count:'')?>  </td>
					<td> <?= (isset($nzcf20[1][4]->count)?$nzcf20[1][4]->count:'')?>  </td>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="3"> 3. NZCF Officer &amp; Under Officer Attendance<br />Activity Days = Activities Authorised &amp; Recognised </th>
				</tr>
				<tr>
					<th> Rank / First Name / Surname<br />(Inc those on: Sup List, Leave &amp; Attached) </th>
					<th> Activity Days </th>
					<th> Parade Hours </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $nzcf20[2] as $officer )
					{
						echo '<tr>';
						echo '	<td> '.$officer->rank.' '.$officer->firstname.' '.$officer->lastname.' </td>';
						echo '	<td> '.$officer->activity_days.' </td>';
						echo '	<td> '.((float)$officer->parade_hours+(float)$officer->activity_hours).' </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="3"> 4. Supplementary Staff </th>
				</tr>
				<tr>
					<th> Name &amp; Position </th>
					<th> Days </th>
					<th> Hours </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $nzcf20[3] as $officer )
					{
						echo '<tr>';
						echo '	<td> '.$officer->rank.' '.$officer->firstname.' '.$officer->lastname.' </td>';
						echo '	<td> '.$officer->activity_days.' </td>';
						echo '	<td> '.((float)$officer->parade_hours+(float)$officer->activity_hours).' </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>		
	</div>
	<div style="width:40%; float: left;margin-left:1em;">
		<table>
			<thead>
				<tr>
					<th colspan="4"> 5. Activity Record &amp; Dates </th>
				</tr>
				<tr>
					<th rowspan="2">Activities: Authorised &amp; Recognised<br /> (Include Officers, Under Officers &amp; Cadets on Courses and Dates) </th>
					<th colspan="3">Attendance</th>
				</tr>
				<tr>
					<th>Offrs</th>
					<th>UOs</th>
					<th>Cdts</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach( $nzcf20[4] as $activity )
					{
						$activity->startdate = strtotime($activity->startdate);
						$activity->enddate = strtotime($activity->enddate);
						$length = ($activity->enddate-$activity->startdate)/60/60/24;
						$attendees = $ATC->get_activity_attendance($activity->activity_id);
						
						$attending_officers = $attending_cadets = $attending_uos = 0;
						foreach($attendees as $attendee)
							if($attendee->presence === ATC_ATTENDANCE_PRESENT && array_search($attendee->access_rights, explode(',', ATC_USER_GROUP_OFFICERS )) !== false ) $attending_officers++;
							elseif($attendee->presence === ATC_ATTENDANCE_PRESENT && array_search($attendee->access_rights, explode(',', ATC_USER_LEVEL_NCO )) !== false ) $attending_uos++;
							elseif($attendee->presence === ATC_ATTENDANCE_PRESENT && array_search($attendee->access_rights, explode(',', ATC_USER_GROUP_CADETS )) !== false ) $attending_cadets++;
							
						
						echo '<tr>';
						echo '	<td> '.date('M d', $activity->startdate).($length>1?'-'.date('M d', $activity->enddate):'').' '.$activity->title.' </td>';
						echo '	<td> '.$attending_officers.' </td>';
						echo '	<td> '.$attending_uos.' </td>';
						echo '	<td> '.$attending_cadets.'</td>';
						//echo '	<td> '.$officer->activity_days.' </td>';
						//echo '	<td> '.((float)$officer->parade_hours+(float)$officer->activity_hours).' </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		<table>
			<thead>
				<tr>
					<th colspan="2"> 6. Forecast of Activities and Training </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
				</tr>
			</tfoot>
			<tbody>
				<?php
					$n = 0;
					foreach( $nzcf20[5] as $activity )
					{
						$activity->startdate = strtotime($activity->startdate);
						$activity->enddate = strtotime($activity->enddate);
						$length = ($activity->enddate-$activity->startdate)/60/60/24;
						$n++;
						
						if( ($n % 2) )
							echo '</tr><tr>';
						echo '	<td> '.date('M d', $activity->startdate).($length>1?'-'.date('M d', $activity->enddate):'').' '.$activity->title.' </td>';
						
					}
				?>
			</tbody>
		</table>
	</div>
	
	<script>
		$('table').css({ width: '100%', marginBottom: '1em' });
		$('button.update').button({ icons: { primary: 'ui-icon-refresh' } });
	</script>
		
<?php
	$ATC->gui_output_page_footer('Personnel');
?>