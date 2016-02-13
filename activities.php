<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if( isset( $_POST['newdate'] ) && strtotime( $_POST['newdate'] ) )
	{
		try {
			$ATC->add_parade_night( strtotime($_POST['newdate']) );
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
					<th> Arrive </th>
					<th> Depart </th>
					<th> OFF </th>
					<th> CDT </th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $activities as $obj )
					{
						echo '<tr>';
						echo '	<td><span class="ui-icon ui-icon-'.($obj->status==ATC_ACTIVITY_RECOGNISED?'radio-off" title="Recognised Activity"':'bullet" title="Authorised Activity"').'" style="float:left">A</span> '.$obj->title.'</td>';
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
		$('button.save').button({ icons: { primary: 'ui-icon-disk' } }).click(function(e){
			e.preventDefault(); // stop the submit button actually submitting
			$.ajax({
				type: "POST",
				url: "activity.php",
				data: $("#attendanceregister").serialize(),
				beforeSend: function()
				{
					$('#attendanceregister').addClass('ui-state-disabled');
				},
				complete: function()
				{
					$('#attendanceregister').removeClass('ui-state-disabled');
				},
				success: function(data)
				{
					// True to ensure we don't just use a cached version, but get a fresh copy from the server
					//location.reload(true);
				},
				error: function(data)
				{
					$('<img src="save-fail.png" style="position: absolute; left: 70em; top: 12em" id="save_indicator" />').appendTo('#personalform');
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
			return false;						
		});
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false }).click(function(){
			$('#dialog').html("<form name='newparadenight' id='newparadenight' method='post'><label for='newdate' style='width:auto;'>New parade night date</label><input type='date' id='newdate' name='newdate' value='<?=date("Y-m-d",strtotime('next '.ATC_SETTING_PARADE_NIGHT,(isset($paradenight)?strtotime($paradenight->date):time())))?>' style='width:auto' /></form>").dialog({
			  modal: true,
			  title: 'Enter new parade night date',
			  buttons: {
				Cancel: function() {
				  $( this ).dialog( "close" );
				},
				Save: function() {
					$.ajax({
					   type: "POST",
					   url: 'attendance.php',
					   data: $("#newparadenight").serialize(),
					   beforeSend: function()
					   {
						   $('#newparadenight').addClass('ui-state-disabled');
					   },
					   complete: function()
					   {
						   $('#newparadenight').removeClass('ui-state-disabled');
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
			  	  $('#newdate').datepicker({ 
			  	  	dateFormat: 'yy-mm-dd',
					showOn: "button",
					buttonImage: "calendar.gif",
					buttonImageOnly: true,
					buttonText: "Select date" 
				  });
			  }
			})
			return false;
		});

	</script>
<?php
	if( !isset($_GET['id']) )
		$ATC->gui_output_page_footer('Activities');
?>
