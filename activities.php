<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if( isset( $_POST['startdate'] ) && strtotime( $_POST['startdate'] ) )
	{
		try {
			$ATC->add_activity( $_POST['startdate'],$_POST['enddate'], $_POST['title'], $_POST['location'], $_POST['status'] );
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

	if( !isset($_GET['id']) )
		$ATC->gui_output_page_header('Activities');
	
?>
	<form name="activitylist" id="activitylist" method="POST">
		<input type="hidden" name="activitylist" value="1" />
		<table>
			<thead>
				<tr>
					<th rowspan="2"> Activity </th>
					<th colspan="2"> Date </th>
					<th colspan="2"> Attendance </th>
					<?php
						if( !isset($_GET['id']) && $ATC->user_has_permission(ATC_USER_PERMISSION_ACTIVITIES_EDIT) )
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
						echo '	<td>'.date("j M, H:i", strtotime($obj->startdate)).'</td>';
						echo '	<td>'.date("j M, H:i", strtotime($obj->enddate)).'</td>';
						echo '	<td style="text-align:center;">'.$obj->officers_attending.'</td>';
						echo '	<td style="text-align:center;">'.$obj->cadets_attending.'</td>';
						if( !isset($_GET['id']) && $ATC->user_has_permission(ATC_USER_PERMISSION_ACTIVITIES_EDIT) )
							echo '	<td><a href="?id='.$obj->activity_id.'" class="edit">Edit</a></td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</form>
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
		
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false }).click(function(){
			$('#dialog').html("<form name='newactivity' id='newactivity' method='post'>"+
				"<label for='startdate' style='width:auto;'>Assemble date/time</label><br />"+
				"<input type='datetime-local' id='startdate' name='startdate' value='' style='width:auto' required='required' /><br />"+
				"<label for='enddate' style='width:auto;'>Dispersal date/time</label><br />"+
				"<input type='datetime-local' id='enddate' name='enddate' value='' style='width:auto' required='required' /><br />"+
				"<label for='title' style='width:auto;'>Activity name</label><br />"+
				"<input type='text' id='title' name='title' value='' style='width:auto' required='required' /><br />"+
				"<label for='location' style='width:auto;'>Activity location</label><br />"+
				"<input type='text' id='location' name='location' value='' style='width:auto' required='required' /><br />"+
				"<label for='status' style='width:auto;'>Status</label><br />"+
				"<select name='status' id='status' required='required' style='width:auto' >"+
				"<option value='<?=ATC_ACTIVITY_RECOGNISED?>'>Recognised</option>"+
				"<option value='<?=ATC_ACTIVITY_AUTHORISED?>'>Authorised</option>"+
				"</select><br />"+
				"</form>").dialog({
			  modal: true,
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
			  },
			  open: function() {
			  	  /*
			  	  $('#startdate').datetimepicker({ 
			  	  	dateFormat: 'dd/mm/yy',
					showOn: "button",
					buttonImage: "calendar.gif",
					buttonImageOnly: true,
					buttonText: "Select date" 
				  });
			  	  $('#enddate').datetimepicker({ 
			  	  	dateFormat: 'yy-mm-dd H:i',
					showOn: "button",
					buttonImage: "calendar.gif",
					buttonImageOnly: true,
					buttonText: "Select date" 
				  });
				  */
			  	  var names = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_activity_names() )) ?>' );
				  $('#title').autocomplete({ source: names });
			  	  var locations = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_locations() )) ?>' );
				  $('#location').autocomplete({ source: locations });
			  }
			})
			return false;
		});

	</script>
<?php
	if( !isset($_GET['id']) )
		$ATC->gui_output_page_footer('Activities');
?>
