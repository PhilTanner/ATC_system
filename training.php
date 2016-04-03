<?php
	require_once "atc_training.class.php";
	$ATC = new ATC_Training();
	
	$ATC->gui_output_page_header('Training');
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try {
			$ATC->set_timetable( 
				$_POST['lesson_id'], 
				$ATC->set_location( $_POST['location_id'], $_POST['location'], null ), 
				$_POST['personnel_id'], 
				$_POST['date'],
				date( 'Y-m-d H:i', strtotime('+45 minutes', strtotime($_POST['date'])) ), 
				$_POST['group']
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
	}
	
	
	
	$terms = $ATC->get_terms();
	$lessons = $ATC->get_lesson();
	
?>
	<table class="tablesorter timetable">
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
							echo '<th>'.date(ATC_SETTING_DATE_OUTPUT, $night).'</th>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_ADVANCED.'" date="'.date('Y-m-d',$night).' 19:00">&nbsp;</td>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_ADVANCED.'" date="'.date('Y-m-d',$night).' 20:00">&nbsp;</td>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_PROFICIENT.'" date="'.date('Y-m-d',$night).' 19:00">&nbsp;</td>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_PROFICIENT.'" date="'.date('Y-m-d',$night).' 20:00">&nbsp;</td>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_BASIC.'" date="'.date('Y-m-d',$night).' 19:00">&nbsp;</td>';
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
							echo '<td class="lesson_block" level="'.ATC_LESSON_LEVEL_BASIC.'" date="'.date('Y-m-d',$night).' 20:00">&nbsp;</td>';
							$night = strtotime( "next ".ATC_SETTING_PARADE_NIGHT, $night);
						}
					}
				?>
			</tr>
		</tbody>
	</table>
	
	<h2> Lessons not in the schedule </h2>
	<ul class="lessonholder">
		<?php
			foreach($lessons as $lesson)
			{
				echo '<li class="lesson_timetable" group="'.$lesson->level.'" lesson_id="'.$lesson->lesson_id.'">';
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
	</ul>
	<br style="clear:both;" />
	
	<script>
		// Make all lesson boxes same height
		var height=width=0;
		$.each( $('.lesson_timetable'), function(){
			if($(this).height() > height ) height = $(this).height();
			if($(this).width() > width ) width = $(this).width();
		});
		$('.lesson_timetable, td.lesson_block').height(height).css({ minWidth: width });
		
		// Make them movable between unassigned and the timetable
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
			}
		});
		// Allow us to return lessons to the unassigned block
		$( ".lessonholder" ).droppable();
		
		// Limit users to drop lessons into their respective levels
		$('.timetable tbody td.lesson_block[level="<?= ATC_LESSON_LEVEL_ADVANCED ?>"]').droppable({ accept: '[group="<?= ATC_LESSON_LEVEL_ADVANCED ?>"]' });
		$('.timetable tbody td.lesson_block[level="<?= ATC_LESSON_LEVEL_PROFICIENT ?>"]').droppable({ accept: '[group="<?= ATC_LESSON_LEVEL_PROFICIENT ?>"]' });
		$('.timetable tbody td.lesson_block[level="<?= ATC_LESSON_LEVEL_BASIC ?>"]').droppable({ accept: '[group="<?= ATC_LESSON_LEVEL_BASIC ?>"]' });
		
		// When they're dropped, prompt for the location and teacher to be saved
		$('.timetable tbody td.lesson_block').on("drop", function( event, ui ){
			var html = '<form>';
			html += '	<p>Lesson: '+ui.draggable.find('p.code').html()+', '+$(this).attr('date')+'</p>';
			html += '	<p>'+ui.draggable.find('p.description').html()+'</p>';
			html += '	<input type="hidden" name="lesson_id" value="'+ui.draggable.attr('lesson_id')+'" /> ';
			html += '	<input type="hidden" name="group" value="'+ui.draggable.attr('group')+'" /> ';
			html += '	<input type="hidden" name="date" value="'+$(this).attr('date')+'" /> ';
			html += '	<input type="hidden" name="location_id" id="location_id" value="" /> ';
			html += '	<input type="hidden" name="personnel_id" id="personnel_id" value="" /> ';
			html += '	<label for="personnel_id">Teacher</label><br />';
			html += '	<input type="text" name="personnel_name" id="personnel_name" /><br />';
			html += '	<label for="location">Location</label><br />';
			html += '	<input type="text" name="location" id="location" />';
			html += '</html>';
			
			$('#dialog').html(html).dialog({
				modal: true,
				width: 600,
				title: 'Edit activity details',
				buttons: {
					Cancel: function() { $( this ).dialog( "close" ); },
					Save: function() {
						$.ajax({
							type: "POST",
							url: 'training.php',
							data: $("#dialog form").serialize(),
							success: function(data)
							{
								// True to ensure we don't just use a cached version, but get a fresh copy from the server
								//location.reload(true);
								ui.draggable.find('p.location').html($('#location').val()).removeClass('unknown');
								ui.draggable.find('p.teacher').html($('#personnel_name').val()).removeClass('unknown');
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
					$( this ).dialog( "destroy" ); 
				},
				open: function() {
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
		});
		
	</script>
		
<?php
	$ATC->gui_output_page_footer('Training');
?>