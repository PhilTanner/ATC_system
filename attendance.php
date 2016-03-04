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
	} elseif( isset( $_POST['attendance_register'] ) && $_POST['attendance_register'] )
	{
		try {
			foreach($_POST as $entry => $status )
			{
				$foo = explode("|", $entry);
				if( count($foo) == 2 )
					$ATC->set_attendance_register( $foo[0], $foo[1], $status );
			}
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
	} elseif( isset( $_POST['personnel_id'] ) ) {
		try {
			foreach($_POST['personnel_id'] as $awol )
				$ATC->set_attendance_register( $awol, $_POST['date'], ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE, $_POST['comment_'.$awol] );
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

	$dates = $ATC->get_attendance( date('Y').'-01-01', date('Y').'-12-31' );
	$users = $ATC->get_personnel((isset($_GET['id'])?(int)$_GET['id']:null), 'ASC', (isset($_GET['id'])?null:ATC_USER_GROUP_PERSONNEL), false );
	if( !is_array($users) )
	{
		$foo[] = $users;
		$users = $foo;
	}
	$calendar = $ATC->get_attendance_register( date('Y').'-01-01', date('Y').'-12-31' );

	if( !isset($_GET['id']) )
		$ATC->gui_output_page_header('Attendance');
	
?>
	<form name="attendanceregister" id="attendanceregister" method="POST">
		<input type="hidden" name="attendance_register" value="1" />
		<table>
			<thead>
				<tr>
					<th colspan="2"> Name </th>
					<?php
						foreach( $dates as $paradenight )
							echo '<th style="font-size:70%">'.date('M j', strtotime($paradenight->date)).'</th>'."\n".'				';
						if( !isset($_GET['id']) )
							echo '<td><a href="?id=0" class="button new"> New </a></td>';
					?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?=count($dates)+2?>"><?= ($ATC->user_has_permission( ATC_PERMISSION_ATTENDANCE_EDIT )?'<button type="submit" class="save">Save</button>':'')?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach( $users as $obj )
					{
						echo '<tr>';	
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td>'.$obj->lastname.', '.$obj->firstname.'</td>';
						$missednights = 0;
						foreach( $dates as $night )
						{
							echo '<td class="attendance user'.$obj->personnel_id.' date'.$night->date.'"><select name="'.$obj->personnel_id.'|'.$night->date.'" id="'.$obj->personnel_id.'_'.$night->date.'">';
							echo '	<option value="" selected="selected"></option>';
							echo '	<option value="'.ATC_ATTENDANCE_PRESENT.'">'.ATC_ATTENDANCE_PRESENT_SYMBOL.'</option>';
							echo '	<option value="'.ATC_ATTENDANCE_ON_LEAVE.'">'.ATC_ATTENDANCE_ON_LEAVE_SYMBOL.'</option>';
							echo '	<option value="'.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE.'">'.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE_SYMBOL.'</option>';
							echo '</select></td>';
						}
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</form>
	
<?php
	$date = date('Y-m-d', time());
	$nonattendingcadets = $ATC->get_awol($date);
	if( count($nonattendingcadets) )
	{
?>
	<form name="missingcadets" id="missingcadets" method="post" style="margin-top:2em;">
		<input type="hidden" name="date" value="<?=htmlentities($date)?>" />
		<table>
			<caption> Absent cadets &mdash; <?=date( ATC_SETTING_DATE_OUTPUT, strtotime($date))?> </caption>
			<thead>
				<tr>
					<th> Name </th>
					<th> Contact number </th>
					<th> Next of Kin contact </th>
					<th> Reason for absence </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4"> <button type="submit" class="save">Save</button> </td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach($nonattendingcadets as $mia)
					{
						echo '<tr>';
						echo '	<td> ';
						echo '<input type="hidden" name="personnel_id[]" value="'.$mia->personnel_id.'" />';
						echo $mia->display_name.'</td>';
						echo '	<td> '.$mia->mobile_phone.'</td>';
						echo '	<td>';
						$n=0;
						foreach( $mia->nok as $nok )
						{
							$n++;
							echo $nok->firstname.' '.$nok->lastname;
							switch($nok->relationship)
							{
								case ATC_NOK_TYPE_MOTHER:
									echo ' (Mother)';
									break;
								case ATC_NOK_TYPE_STEPMOTHER:
									echo ' (Step-Mother)';
									break;
								case ATC_NOK_TYPE_GRANDMOTHER:
									echo ' (Grandmother)';
									break;
								case ATC_NOK_TYPE_FATHER:
									echo ' (Father)';
									break;
								case ATC_NOK_TYPE_STEPFATHER:
									echo ' (Step-Father)';
									break;
								case  ATC_NOK_TYPE_GRANDFATHER:
									echo ' (Grandfather)';
									break;
								case  ATC_NOK_TYPE_SPOUSE:
									echo ' (Spouse)';
									break;
								case  ATC_NOK_TYPE_DOMPTNR:
									echo ' (Domestic Partner)';
									break;
								case  ATC_NOK_TYPE_SIBLING:
									echo ' (Sibling)';
									break;
								default:
									echo ' (Unknown/Other)';
							}
							echo '<br />'.$nok->mobile_number.' ('.$nok->home_number.')';
							if( $n != count($mia->nok) )
								echo '<hr />';
						}
						echo '	</td>';
						echo '	<td> <input type="text" maxlength="255" name="comment_'.$mia->personnel_id.'" id="comment_'.$mia->personnel_id.'" /> </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</form>
<?php
	}
?>	
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
		$('button.save').button({ icons: { primary: 'ui-icon-disk' } });
		$('#attendanceregister button.save').click(function(e){
			e.preventDefault(); // stop the submit button actually submitting
			$.ajax({
				type: "POST",
				url: "attendance.php",
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

		var attendance = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $calendar )) ?>' );
		
		if( <?= $ATC->user_has_permission( ATC_PERMISSION_ATTENDANCE_EDIT ) ?> )
		{
			$.each(attendance, function(index, value){
				$('#'+value['personnel_id']+'_'+value['date']).val(value['presence']);
			});
		} else if( <?= $ATC->user_has_permission( ATC_PERMISSION_ATTENDANCE_VIEW ) ?> ) {
			$('td.attendance').empty();
			$.each(attendance, function(index, value){
				var symbol="";
				switch(value['presence'])
				{
					case "<?=ATC_ATTENDANCE_PRESENT?>":
						symbol = "<?=ATC_ATTENDANCE_PRESENT_SYMBOL?>";
						break;
					case "<?=ATC_ATTENDANCE_ON_LEAVE?>":
						symbol = "<?=ATC_ATTENDANCE_ON_LEAVE_SYMBOL?>";
						break;
					case "<?=ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE?>":
						symbol = "<?=ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE_SYMBOL?>";
						break;
					casedefault:
						symbol = value['presence'];
				}
				$('td.user'+value['personnel_id']+'.date'+value['date']).html(symbol);
			});
		}
		
		var missingnights = 0;
		var curruser = 0;
		$.each(attendance, function(index, value){
			// Don't carry missing nights forward;
			if( curruser != value['personnel_id'] )
			{
				curruser = value['personnel_id'];
				//missingnights=0;
			}
			switch(value['presence'])
			{
				case "<?=ATC_ATTENDANCE_PRESENT?>":
				case "<?=ATC_ATTENDANCE_ON_LEAVE?>":
					missingnights = 0;
					break;
				case "<?=ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE?>":
					missingnights+=1;
					break;
				default:
					missingnights++;
			}
			// Cadets who miss 4 parade nights are eligible to be signed off the books
			if( missingnights >= 2 ) 
				$('td.user'+value['personnel_id']+'.date'+value['date']).parent().children().addClass('ui-state-highlight').attr('title','Cadet needs to attend, after 4 missed nights, they will be signed out of the unit!');
			if( missingnights >= 4 ) 
				$('td.user'+value['personnel_id']+'.date'+value['date']).parent().children().addClass('ui-state-error').attr('title','Cadet missed 4 parade nights - sign them off the books');					
		});
	</script>
	
<?php
	if( !isset($_GET['id']) )
		$ATC->gui_output_page_footer('Attendance');
?>
