<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if( isset( $_POST['startdate'] ) && strtotime( $_POST['startdate'] ) )
		{
			try {
				$ATC->set_activity( 
					$_POST['activity_id'],
					$_POST['startdate'], 
					$_POST['enddate'], 
					$_POST['title'], 
					$ATC->set_location( $_POST['location_id'], $_POST['location'], null ), 
					$_POST['personnel_id'], 
					$ATC->set_activity_type( $_POST['activity_type_id'], $_POST['activity_type'], null ), 
					$_POST['dress_code'],
					$_POST['attendees'] 
				);
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
		} elseif ( isset($_POST["attendance_register"]) ) {
			try {
				$register = array();
				foreach( $_POST as $key => $value)
				{
					if( substr($key, 0, strlen('attendance_')) == 'attendance_' )
					{
						$foo = explode("_", $key);
						// Exclude the attendance_register entry, only go if we've got a real person record
						if( (int)$foo[1] )
							$register[] = array('personnel_id' => $foo[1], 'attendance' => $value, 'note' => $_POST['note_'.$foo[1]]);
					}
				}
				$ATC->set_activity_attendance( (int)$_POST['activity_id'], $register );
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
		}
		exit();
	}
	
	if( isset($_GET['id']) && isset($_GET['action']) && $_GET['action']=='attendance' )
	{
		$activity = $ATC->get_activity((int)$_GET["id"]);
		$activity = $activity[0];
		$users = $ATC->get_activity_attendance((int)$_GET['id']);
		if( !is_array($users) )
		{
			$foo[] = $users;
			$users = $foo;
		}
	
?>
	<form name="editactivity" id="editactivity" method="POST">
		<input type="hidden" name="attendance_register" value="1" />
		<table>
			<thead>
				<tr>
					<th colspan="2"> Name </th>
					<th> <?= $activity->title ?> Attendance </th>
					<th> Note </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $users as $obj )
					{
						echo '<tr>';	
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td>'.$obj->display_name.'</td>';
						
						echo '<td class="attendance user'.$obj->personnel_id.'">';
						echo '	<input type="hidden" id="activity_id" name="activity_id" value="'.$activity->activity_id.'" />';
						echo '	<select name="attendance_'.$obj->personnel_id.'" id="attendance_'.$obj->personnel_id.'">';
						echo '		<option value=""'.(is_null($obj->personnel_id)?' selected="selected"':'').'></option>';
						echo '		<option value="'.ATC_ATTENDANCE_PRESENT.'"'.($obj->presence===ATC_ATTENDANCE_PRESENT?' selected="selected"':'').'>'.ATC_ATTENDANCE_PRESENT_SYMBOL.'</option>';
						echo '		<option value="'.ATC_ATTENDANCE_ON_LEAVE.'"'.($obj->presence===ATC_ATTENDANCE_ON_LEAVE?' selected="selected"':'').'>'.ATC_ATTENDANCE_ON_LEAVE_SYMBOL.'</option>';
						echo '		<option value="'.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE.'"'.($obj->presence===ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE?' selected="selected"':'').'>'.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE_SYMBOL.'</option>';
						echo '	</select>';
						echo '</td>';
						echo '<td><input type="text" name="note_'.$obj->personnel_id.'" id="note_'.$obj->personnel_id.'" value="'.htmlentities($obj->note).'" maxlength="255" /></td>';
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
		exit();
	} elseif( isset($_GET['id']) ) {
		$activity = $ATC->get_activity((int)$_GET["id"]);
		$activity = $activity[0];
?>
<form name='editactivity' id='editactivity' method='post'>
	<label for='startdate'>Assemble date/time</label><br />
	<input type='datetime-local' id='startdate' name='startdate' value='<?=$activity->startdate?>' required='required' /><br />
	<label for='enddate'>Dispersal date/time</label><br />
	<input type='datetime-local' id='enddate' name='enddate' value='<?=$activity->enddate?>' required='required' /><br />
	<label for='title'>Activity name</label><br />
	<input type='text' id='title' name='title' value='<?=$activity->title?>' required='required' /><br />
	<label for='location'>Activity location</label><br />
	<input type='text' id='location' name='location' value='<?=$activity->name?>' required='required' /><br />
	<label for='activity_type'>Type of activity</label><br />
	<input type='text' id='activity_type' name='activity_type' value='<?=$activity->type?>' required='required' /><br />
	<label for='personnel_id'>Organising Staff</label><br />
	<input type='text' id='personnel_name' name='personnel_name' value='<?=$activity->rank.' '.$activity->lastname.', '.$activity->firstname?>' required='required' /><br />
	<label for='dress_code'>Dress code</label><br />
	<select name='dress_code' id='dress_code'>
		<option value='<?=ATC_DRESS_CODE_BLUES?>'<?=($activity->dress_code==ATC_DRESS_CODE_BLUES?' selected="selected"':'')?>>No 6 Blues</option>
		<option value='<?=ATC_DRESS_CODE_DPM?>'<?=($activity->dress_code==ATC_DRESS_CODE_DPM?' selected="selected"':'')?>>DPM</option>
		<option value='<?=ATC_DRESS_CODE_BLUES_AND_DPM?>'<?=($activity->dress_code==ATC_DRESS_CODE_BLUES_AND_DPM?' selected="selected"':'')?>>Mix</option>
	</select><br />
	<fieldset id='attendees'><legend>Attendees</legend><ol class='dragdrop attendees'></ol></fieldset>
	<fieldset id='non_attendees'><legend>Non-Attendees</legend><ol class='dragdrop attendees'></ol></fieldset>
	<input type='hidden' id='activity_id' name='activity_id' value='<?=$activity->activity_id?>' />
	<input type='hidden' id='location_id' name='location_id' value='<?=$activity->location_id?>' />
	<input type='hidden' id='activity_type_id' name='activity_type_id' value='<?=$activity->activity_type_id?>' />
	<input type='hidden' id='personnel_id' name='personnel_id' value='<?=$activity->personnel_id?>' />
</form>
<script>
	var names = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_activity_names() )) ?>' );
	$('#title').autocomplete({ source: names, minLength: 0 });
	
	var locations = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_locations() )) ?>' );
	$('#location').autocomplete({ 
		minLength: 0,
		source: locations,
		focus: function( event, ui ) {
			$( "#location" ).val( ui.item.name );
			return false;
		},
		select: function( event, ui ) {
			$( "#location" ).val( ui.item.name );
			$( "#location_id" ).val( ui.item.location_id );
			return false;
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.name + (item.address?"<br>(" + item.address + ")":"")+"</a>" )
		.appendTo( ul );
	} 
	
	var activity_types = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_activity_types() )) ?>' );
	$('#activity_type').autocomplete({ 
		minLength: 0,
		source: activity_types,
		focus: function( event, ui ) {
			$( "#activity_type" ).val( ui.item.type );
			return false;
		},
		select: function( event, ui ) {
			$( "#activity_type" ).val( ui.item.type );
			$( "#activity_type_id" ).val( ui.item.activity_type_id );
			return false;
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.type + (item.nzcf_status==<?=ATC_ACTIVITY_RECOGNISED?>?" (Recognised)":" (Authorised)")+"</a>" )
		.appendTo( ul );
	} 
	
	var officers = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_personnel(null,'ASC',ATC_USER_GROUP_OFFICERS) )) ?>' );
	$('#personnel_name').autocomplete({ 
		minLength: 0,
		source: officers,
		focus: function( event, ui ) {
			$( "#personnel_name" ).val( ui.item.lastname+", "+ui.item.firstname );
			return false;
		},
		select: function( event, ui ) {
			$( "#personnel_name" ).val( ui.item.lastname+", "+ui.item.firstname );
			$( "#personnel_id" ).val( ui.item.personnel_id );
			return false;
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.rank + " " + item.lastname + ", " + item.firstname +"</a>" )
		.appendTo( ul );
	}
					
	// Attending personnel
	var personnel = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_personnel(null,'ASC',ATC_USER_GROUP_PERSONNEL) )) ?>' );
	var attendees = jQuery.parseJSON( '<?= json_encode($activity->attendees) ?>' );
	$.each(personnel, function(key, person){ 
		if( person.personnel_id > 0 )
		{
			if( attendees.indexOf( person.personnel_id ) >= 0 )
				$('#attendees ol.dragdrop').append('<li personnel_id="'+person.personnel_id+'">'+person.rank+' '+person.lastname+', '+person.firstname+'</li>'); 
			else
				$('#non_attendees ol.dragdrop').append('<li personnel_id="'+person.personnel_id+'">'+person.rank+' '+person.lastname+', '+person.firstname+'</li>'); 
		}
	});
	$('#attendees ol.dragdrop,#non_attendees ol.dragdrop').sortable({ connectWith: ".dragdrop.attendees" }).disableSelection();
	
</script>
<?php
		
		exit();
	}
	
	$ATC->gui_output_page_header('Activities');
	$activities = $ATC->get_activities();
	
?>
	<form name="activitylist" id="activitylist" method="POST">
		<input type="hidden" name="activitylist" value="1" />
		<table>
			<thead>
				<tr>
					<th rowspan="2"> Activity </th>
					<th rowspan="2"> Lead </th>
					<th colspan="2"> Date </th>
					<th colspan="2"> Attendance </th>
					<?php
						if( !isset($_GET['id']) && $ATC->user_has_permission(ATC_PERMISSION_ACTIVITIES_EDIT) )
							echo '<td><a href="?id=0" class="button new"> New </a></td>';
					?>
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
						echo '	<td>'.date("j M, H:i", strtotime($obj->startdate)).'</td>';
						echo '	<td>'.date("j M, H:i", strtotime($obj->enddate)).'</td>';
						echo '	<td style="text-align:center;">'.$obj->officers_attending.'</td>';
						echo '	<td style="text-align:center;">'.$obj->cadets_attending.'</td>';
						if( !isset($_GET['id']) && $ATC->user_has_permission(ATC_PERMISSION_ACTIVITIES_EDIT) )
						{
							echo '	<td><a href="?id='.$obj->activity_id.'" class="button edit">Edit</a>';
							//echo '<a href="?id='.$obj->activity_id.'" class="button delete">Delete</a>';
							if( $ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW) )
								echo '<a href="?id='.$obj->activity_id.'&action=attendance" class="button attendance">Attendance</a>';
							echo '</td>';
						}
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</form>
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
		
		//$('a.button.delete').button({ icons:{ primary: 'ui-icon-trash' }, text: false }).addClass('ui-state-error');
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
		$('#activitylist a.button.attendance').button({ icons: { primary: 'ui-icon-clipboard' }, text: false });
		$('a.button.edit, a.button.new, #activitylist a.button.attendance').click(function(e){
			e.preventDefault(); // stop the link actually firing
			var href = $(this).attr("href");
			$('#dialog').empty().load(href).dialog({
				modal: true,
				width: 600,
				title: 'Edit activity details',
				buttons: {
					Cancel: function() {
						$( this ).dialog( "close" );
					},
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
							   $('#dialog').dialog('destroy').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
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
				  },
				  open: function() {
				
					
				}
			});
			return false;
		});

	</script>
<?php
	$ATC->gui_output_page_footer('Activities');
?>
