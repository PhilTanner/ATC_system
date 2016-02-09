<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$dates = $ATC->get_attendance( date('Y').'-01-01', date('Y').'-12-31' );
	$users = $ATC->get_personnel(null);

	$calendar = $ATC->get_attendance_register( date('Y').'-01-01', date('Y').'-12-31' );

	$ATC->gui_output_page_header('Attendance');
	
?>
	<table>
		<thead>
			<tr>
				<th colspan="2"> Name </th>
				<?php
					foreach( $dates as $obj )
						echo '<th style="font-size:70%">'.date('M j', strtotime($obj->date)).'</th>'."\n".'				';
				?>
				<td> 
					<a href="?id=0" class="button new"> New </a>
					
				</td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?=count($calendar)+3?>"><?= ($ATC->user_has_permission( ATC_USER_PERMISSION_ATTENDANCE_EDIT )?'<button type="submit" class="save">Save</button>':'')?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
				foreach( $users as $obj )
				{
					echo '<tr>';	
					echo '	<td>'.$obj->rank.'</td>';
					echo '	<td>'.$obj->lastname.', '.$obj->firstname.'</td>';
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
	<script>
		$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
		$('button.save').button({ icons: { primary: 'ui-icon-disk' } });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });

		var attendance = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $calendar )) ?>' );
		
		if( <?= $ATC->user_has_permission( ATC_USER_PERMISSION_ATTENDANCE_EDIT ) ?> )
		{
			$.each(attendance, function(index, value){
				$('#'+value['personnel_id']+'_'+value['date']).val(value['presence']);
			});
		} else if( <?= $ATC->user_has_permission( ATC_USER_PERMISSION_ATTENDANCE_VIEW ) ?> ) {
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
	</script>
<?php
	$ATC->gui_output_page_footer('Attendance');
?>