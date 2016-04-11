<?php
	require_once "atc_training.class.php";
	$ATC = new ATC_Training();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try {
			if( isset($_GET['action']) && $_GET['action'] == 'delete' )
			{
				$ATC->delete_lesson_from_timetable( 
					$_POST['lesson_id'], 
					$_POST['year']
				);
			} else {
				$ATC->set_timetable( 
					$_POST['lesson_id'], 
					$ATC->set_location( $_POST['location_id'], $_POST['location'], null ), 
					$_POST['personnel_id'], 
					$_POST['date'],
					date( 'Y-m-d H:i', strtotime('+45 minutes', strtotime($_POST['date'])) ), 
					$_POST['group'],
					$_POST['dress_code']
				);
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
	}
	
	$ATC->gui_output_page_header('Training');
	
	$_GET['year'] = (isset($_GET['year'])&&(int)$_GET['year'])?(int)$_GET['year']:date('Y');
	
	$terms = $ATC->get_terms( date('Y-m-d', strtotime((int)$_GET['year'].'-01-01')), date('Y-m-d', strtotime((int)$_GET['year'].'-12-31')) );
	$timetable = $ATC->get_timetable( date('Y-m-d', strtotime((int)$_GET['year'].'-01-01')), date('Y-m-d', strtotime((int)$_GET['year'].'-12-31')) );
	$lessons = $ATC->get_lesson();
	
?>
	<form>
		<fieldset>
			<legend>Choose a year</legend>
			<label for="year">Pick a year:</label>
			<select name="year" id="year">
				<?php
					for($i=2016; $i<=(date('Y')+5); $i++ )
						echo '<option value="'.$i.'"'.($i==$_GET['year']?' selected="selected"':'').'>'.$i.'</option>';
				?>
			</select>
			<button type="submit" class="update">Update</button>			
		</fieldset>
	</form>
	<br />
	<table class="timetable">
		<thead>
			<tr>
				<th>Level</th>
				<?php
					$n=1;
					foreach( $terms as $term )
					{
						$n++;
						echo '<td style="font-size:20%">&nbsp;</td>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							$n++;
							echo '<th'.($night<time()-(24*60*60)?' class="ui-state-disabled"':'').'>'.date(ATC_SETTING_DATE_OUTPUT, $night).'</th>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th rowspan="2"> Advanced </th>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_ADVANCED.'" date="'.date('Y-m-d',$night).' 19:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
			<tr>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_ADVANCED.'" date="'.date('Y-m-d',$night).' 20:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
			<tr> <th colspan="<?= $n; ?>" style="font-size:50%">&nbsp;</th> </tr>
			<tr> <th colspan="<?= $n; ?>" style="font-size:50%">&nbsp;</th> </tr>
			<tr>
				<th rowspan="2"> Proficient </th>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_PROFICIENT.'" date="'.date('Y-m-d',$night).' 19:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
			<tr>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_PROFICIENT.'" date="'.date('Y-m-d',$night).' 20:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
			<tr> <th colspan="<?= $n; ?>" style="font-size:50%">&nbsp;</th> </tr>
			<tr> <th colspan="<?= $n; ?>" style="font-size:50%">&nbsp;</th> </tr>
			<tr>
				<th rowspan="2"> Basic </th>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_BASIC.'" date="'.date('Y-m-d',$night).' 19:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
			<tr>
				<?php
					foreach( $terms as $term )
					{
						echo '<th style="font-size:20%">&nbsp;</th>';
						
						$night = $term->startdate;
						while( $night <= $term->enddate )
						{
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_BASIC.'" date="'.date('Y-m-d',$night).' 20:00"></td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
		</tbody>
	</table>
	
	<h2> Lessons not in the schedule: </h2>
	<ul class="lessonholder" style="width: 90%; border: 1px solid silver;">
		<?php
			foreach($lessons as $lesson)
			{
				echo '<li class="lesson_timetable" group="'.$lesson->level.'" lesson_id="'.$lesson->lesson_id.'" personnel_id="" location_id="" dress_code="'.$lesson->dress_code.'">';
				echo '	<div>';
				echo '		<h2 style="background-color:'.$lesson->colour.'; color:'.$lesson->text_colour.'">'.htmlentities($lesson->category_short).'</h2>';
				echo '		<p class="code">'.htmlentities($lesson->category_short).' '.htmlentities($lesson->code).'</p>';
				echo '		<p class="location unknown"> Unknown location </p>';
				echo '		<p class="teacher unknown"> Unknown teacher </p>';
				echo '		<p class="dresscode dresscode'.$lesson->dress_code.'">';
				switch($lesson->dress_code)
				{
					case ATC_DRESS_CODE_BLUES:
						echo ATC_DRESS_CODE_BLUES_NAME;
						break;
					case ATC_DRESS_CODE_DPM:
						echo ATC_DRESS_CODE_DPM_NAME;
						break;
					case ATC_DRESS_CODE_BLUES_AND_DPM:
						echo ATC_DRESS_CODE_BLUES_AND_DPM_NAME;
						break;
					case ATC_DRESS_CODE_MUFTI:
						echo ATC_DRESS_CODE_MUFTI_NAME;
						break;
					default:
						echo 'Unknown';
				}
				echo '		</p>';
				echo '		<p class="description">'.htmlentities($lesson->description).'</p>';
				echo '	</div>';
				echo '</li>';
			}
		?>
		<br style="clear:both;" />
	</ul>
	
	
	<script src="jquery.simulate.js">
	// Needed to move the already defined lessons into place at page load.	
	</script>
	<script>
		
		// Make all lesson boxes same height/width
		var height=width=0;
		$.each( $('.lesson_timetable'), function(){
			if($(this).height() > height ) height = $(this).height();
			if($(this).width() > width ) width = $(this).width();
		});
		$('.lesson_timetable, td.lesson_block').height(height).css({ minWidth: width });
		
		// Make them draggable around the screen
		$( ".lessonholder li" ).draggable({ 
			revert: "invalid", 
			snap: 'td.lesson_block', 
			snapMode: 'outer',
			start: function( event, ui ){
				// Highlight cells you can't drop the lesson into
				$('td.lesson_block').not('[level="'+$(this).attr('group')+'"]').addClass('ui-state-error').fadeTo(0,0.1);
			},
			stop:  function( event, ui ){
				$('td.lesson_block').removeClass('ui-state-error').fadeTo(0,1);
			},
			accept: function(draggable) {
				return $(this).find("*").length == 0;
			}
		});
		// Allow us to return lessons to the unassigned block
		$( ".lessonholder" ).droppable().css({ minHeight: (height*1.1), backgroundColor:'#efefef' }).addClass('ui-corner-all');
		
		// Allow timetable grid to accept lessons
		$('.timetable tbody td.lesson_block[level]').droppable({ 
			// Limit us to drop lessons into their respective levels, 
			accept: function(ui){ 
				return $(this).attr("level") == ui.attr('group'); 
			},
			// and when a lesson is dropped onto it, lock the cell to prevent others being included.
			drop: function(event, ui) { 
				$(this).droppable('option', 'accept', ui.draggable); 
			}, 
			// Until you drag the lesson out of the cell, where it allows that cell to accept another lesson of the right level
			out: function(event, ui){ 
				$(this).droppable('option', 'accept', '[group="'+$(this).attr("level")+'"]');
			}    
		});
		
		// When they're dropped onto the timetable, prompt for the location and teacher to be saved
		$('.timetable tbody td.lesson_block').on("drop", function( event, ui ){
			// snap our lesson neatly into the block the user dropped it onto.
			ui.draggable.offset($(this).offset());
			
			/*
			var data = "";
			data['action'] = 'clearlessonslot';
			data['level'] = $(this).attr('level');
			data['date'] = $(this).attr('date');
			
			$.ajax({
				type: "POST",
				url: 'training.php',
				data: $("#dialog form").serialize()
			});
			*/
			
			// If we don't have a teacher for a location for this lesson, we'll prompt for them.
			if( !ui.draggable.attr('personnel_id') || !ui.draggable.attr('location_id') )
			{
			
				// Draw up our form to ask for the details for this lesson plan
				var html = '<form>';
				html += '	<p>Lesson: '+ui.draggable.find('p.code').html()+', '+$(this).attr('date')+'</p>';
				html += '	<p>'+ui.draggable.find('p.description').html()+'</p>';
				html += '	<input type="hidden" name="lesson_id" value="'+ui.draggable.attr('lesson_id')+'" /> ';
				html += '	<input type="hidden" name="group" value="'+ui.draggable.attr('group')+'" /> ';
				html += '	<input type="hidden" name="date" value="'+$(this).attr('date')+'" /> ';
				// html += '	<input type="hidden" name="level" value="'+$(this).attr('level')+'" /> ';
				html += '	<input type="hidden" name="location_id" id="location_id" value="" /> ';
				html += '	<input type="hidden" name="personnel_id" id="personnel_id" value="" /> ';
				html += '	<label for="personnel_id">Teacher</label><br />';
				html += '	<input type="text" name="personnel_name" id="personnel_name" /><br />';
				html += '	<label for="location">Location</label><br />';
				html += '	<input type="text" name="location" id="location" /><br />';
				html += '	<label for="dress_code">Dress code</label><br />';
				html += '	<select name="dress_code" id="dress_code">';
				html += '		<option value="<?=ATC_DRESS_CODE_BLUES?>"><?=htmlentities(ATC_DRESS_CODE_BLUES_NAME)?></option>';
				html += '		<option value="<?=ATC_DRESS_CODE_DPM?>"><?=htmlentities(ATC_DRESS_CODE_DPM_NAME)?></option>';
				html += '		<option value="<?=ATC_DRESS_CODE_BLUES_AND_DPM?>"><?=htmlentities(ATC_DRESS_CODE_BLUES_AND_DPM_NAME)?></option>';
				html += '		<option value="<?=ATC_DRESS_CODE_MUFTI?>"><?=htmlentities(ATC_DRESS_CODE_MUFTI_NAME)?></option>';
				html += '	</select>';
				html += '</html>';
			
				$('#dialog').html(html).dialog({
					modal: true,
					width: 600,
					title: 'Edit activity details',
					buttons: {
						Cancel: function() { $( this ).dialog( "close" ); },
						Save: function() {
							// Input checking
							if( !$('#personnel_id').val() )
							{
								alert('Please select a tutor for this lesson from the list');
								$('#personnel_name').focus();
								return false;
							}
							// New locations get created on demand - so we don't need a specific ID set (unlike for trainers)
							if( !$('#location').val().length )
							{
								alert('Please select a location for this lesson');
								$('#location').focus();
								return false;
							}
							$.ajax({
								type: "POST",
								url: 'training.php',
								data: $("#dialog form").serialize(),
								success: function(data)
								{
									ui.draggable.attr('location_id', $('#location_id').val()).find('p.location').removeClass('unknown').html($('#location').val());
									ui.draggable.attr('personnel_id', $('#personnel_id').val()).find('p.teacher').removeClass('unknown').html($('#personnel_name').val());
									ui.draggable.attr('dress_code', $('#dress_code').val()).find('p.dresscode').removeClass('unknown').html($('#dress_code option:selected').text());
									$( '#dialog' ).dialog( "close" );
								},
								error: function(data)
								{
									$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
										modal: true,
										title: 'Error!',
										buttons: { Close: function() { $( this ).dialog( "close" );  } },
										close: function() { $( this ).dialog( "destroy" ); },
										open: function() { $('.ui-dialog-titlebar').addClass('ui-state-error');  }
									});
									return false;
						 		}
							});
						}
					},
					close: function() { 
						// If we've closed the dialog but haven't set the values properly, drag the lesson back to the holding pen
						if( ui.draggable.find('p.unknown').size() )
							ui.draggable.offset($('.lessonholder').offset());
					
						$( this ).dialog( "destroy" );
					},
					open: function() {
						$('#dress_code').val(ui.draggable.attr('dress_code'));
						var locations = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_locations() )) ?>' );
						// Autocompletes need a label field to search against
						$.each(locations, function(){
							$(this)[0].label = $(this)[0].name + " " + $(this)[0].address;
						});
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
					
						var officers = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_personnel(null,'ASC',ATC_USER_GROUP_TRAINERS) )) ?>' );
						// Autocompletes need a label field to search against
						$.each(officers, function(){
							$(this)[0].label = $(this)[0].rank + " " + $(this)[0].lastname + ", " + $(this)[0].firstname;
						});
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
					}
				});
			// The lesson already has the values defined, let's just use them
			} else {
				
				var data = { 
					'lesson_id': ui.draggable.attr('lesson_id'), 
					'group': $(this).attr('level'),
					'date': $(this).attr('date'),
					'personnel_id': ui.draggable.attr('personnel_id'),
					'location_id': ui.draggable.attr('location_id'),
					'dress_code': ui.draggable.attr('dress_code'),
					'location': ui.draggable.find('p.location').html()
				};
				
				$.ajax({
					type: "POST",
					url: 'training.php',
					data: $.param(data),
					error: function(data)
					{
						$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
							modal: true,
							title: 'Error!',
							buttons: { Close: function() { $( this ).dialog( "close" );  } },
							close: function() { $( this ).dialog( "destroy" ); },
							open: function() { $('.ui-dialog-titlebar').addClass('ui-state-error');  }
						});
						return false;
					}
				});
				
			}
		});
		
		// When lessons are removed from the timetable and dropped onto the lesson holder, clear them from the DB and reset variables
		$('.lessonholder').on("drop", function( event, ui ){			
			ui.draggable.attr('location_id', '').find('p.location').html('Unknown location').addClass('unknown');
			ui.draggable.attr('personnel_id', '').find('p.teacher').html('Unknown teacher').addClass('unknown');
			ui.draggable.attr('dress_code', '').find('p.dresscode').html('Unknown dress').addClass('unknown');
			
			var data = { 
				'lesson_id': ui.draggable.attr('lesson_id'), 
				'group': ui.draggable.attr('group'),
				'year': '<?= $_GET['year'] ?>'
			};
				
			$.ajax({
				type: "POST",
				url: 'training.php?action=delete',
				data: $.param(data),
				error: function(data)
				{
					$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
						modal: true,
						title: 'Error!',
						buttons: { Close: function() { $( this ).dialog( "close" );  } },
						close: function() { $( this ).dialog( "destroy" ); },
						open: function() { $('.ui-dialog-titlebar').addClass('ui-state-error');  }
					});
					return false;
				}
			});
			
		});
		
		// And now, finally, pick up our lessons, move them to their correct slots onto the timetable.
		function simulate_drop( lesson, block )
		{
			lessonOffset = lesson.offset(),
			blockOffset = block.offset(),
			dx = blockOffset.left - lessonOffset.left,
			dy = blockOffset.top - lessonOffset.top;
			
			lesson.simulate("drag", { dx: dx, dy: dy });
		}
		
		<?php
			foreach( $timetable as $plannedlesson )
			{
				// Set our variables on our lesson, so that when we drop them onto the grid in the predefined slots, we're not going to fire 240 different dialogs.
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"]").attr("personnel_id", "'.$plannedlesson->personnel_id.'");'."\n";
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"]").attr("location_id", "'.$plannedlesson->location_id.'");'."\n";
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"]").attr("dress_code", "'.$plannedlesson->dress_code.'");'."\n";
				
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"] p").filter("p.teacher").html("'.htmlentities($plannedlesson->display_name).'").removeClass("unknown");'."\n";
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"] p").filter("p.location").html("'.htmlentities($plannedlesson->name).'").removeClass("unknown");'."\n";
				
				echo '$("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"] p").filter("p.dresscode").html("';
				switch($plannedlesson->dress_code)
				{
					case ATC_DRESS_CODE_BLUES:
						echo ATC_DRESS_CODE_BLUES_NAME;
						break;
					case ATC_DRESS_CODE_DPM:
						echo ATC_DRESS_CODE_DPM_NAME;
						break;
					case ATC_DRESS_CODE_BLUES_AND_DPM:
						echo ATC_DRESS_CODE_BLUES_AND_DPM_NAME;
						break;
					case ATC_DRESS_CODE_MUFTI:
						echo ATC_DRESS_CODE_MUFTI_NAME;
						break;
					default:
						echo 'Unknown';
				}
				
				echo '").removeClass("unknown");'."\n";
				
				// And now we've updated our values, simulate a drag-drop so things are in the right place.
				echo 'simulate_drop( $("li.lesson_timetable[lesson_id=\"'.$plannedlesson->lesson_id.'\"]"), $("td[level=\"'.$plannedlesson->group.'\"]").filter("[date=\"'.date('Y-m-d H:i', strtotime($plannedlesson->startdate)).'\"]") );'."\n";
			}
		?>
		
	</script>
	
		
<?php
	$ATC->gui_output_page_footer('Training');
?>