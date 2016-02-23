<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
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
		exit();
	}
	
	$activities = $ATC->get_activities();

	if( isset($_GET['id']) )
	{
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
							echo '<td><span href="?id=0" class="button new"> New </span></td>';
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
							echo '<a href="?id='.$obj->activity_id.'" class="button delete">Delete</a></td>';
						}
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</form>
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
		
		$('a.button.delete').button({ icons:{ primary: 'ui-icon-trash' }, text: false }).addClass('ui-state-error');
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false }).click(function(e){
			e.preventDefault(); // stop the link actually firing
			var href = $(this).attr("href");
			$('#dialog').load(href).dialog({
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

		$('span.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false }).click(function(e){
			e.preventDefault(); // stop the link actually firing
			$('#dialog').load('?id=0').dialog({
				  modal: true,
				width: 600,
				  title: 'Create new activity',
				  buttons: {
					Cancel: function() {
					  $( this ).dialog( "close" );
					},
					Save: function() {
						$.ajax({
						   type: "POST",
						   url: 'activities.php',
						   data: $("#newactivity").serialize(),
						   beforeSend: function()
						   {
							   $('#newactivity').addClass('ui-state-disabled');
						   },
						   complete: function()
						   {
							   $('#newactivity').removeClass('ui-state-disabled');
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
				  }
			});
			return false;
		});

	</script>
<?php
	$ATC->gui_output_page_footer('Activities');
?>
