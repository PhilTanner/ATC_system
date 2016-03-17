<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	if( !isset($_GET['showall']) )
			$_GET['showall'] = 0;
	$user = $ATC->get_personnel($id, 'ASC', null, (int)$_GET['showall']);

	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<div id="personnelform">
			<h2 href="personal.php?id=<?=$user->personnel_id?>"> Personal details &mdash; <?=$user->rank.' '.$user->display_name?> </h2>
			<div id="personal">
			</div>
	
			<h2 href="nok.php?id=<?=$user->personnel_id?>"> Next of Kin </h2>
			<div id="nok">
			</div>
	
			<h2 href="attendance.php?id=<?=$user->personnel_id?>"> Attendance </h2>
			<div id="attendance">
			</div>
			
			<h2 href="personal.php?id=<?=$user->personnel_id?>&amp;action=promotion"> Promotions </h2>
			<div id="promotion">
			</div>
	<!--
			<h2> Activites </h2>
			<div id="activities">
			</div>
	
			<h2> Finance </h2>
			<div id="finance">
			</div>
	
			<h2> Stores </h2>
			<div id="stores">
			</div>

			<h2> Training </h2>
			<div id="training">
			</div>
			
			
			<h2> Current browser sessions </h2>
			<div id="browsersessions">
			</div>
			-->
		</div>
		
		<script>
			$(function(){					
				$('#personnelform div:first').load( $('#personnelform h2:first').attr('href'), function(){
					$('#personnelform').accordion({ 
						header: 'h2', 
						changestart:function( event, ui )
						{
							// There's a delay after load to set height, to allow the DOM to update properly
							ui.newContent.load(ui.newHeader.attr('href'), function(){ setTimeout( function(){ $('#personnelform').accordion('resize'); },500); });
						} 
					});
				});
			});
			
		</script>

<?php
	} elseif( is_array( $user ) ) {
?>
	<table class="tablesorter">
		<thead>
			<tr>
				<th> Flight </th>
				<th> Rank </th>
				<th> Name </th>
				<th> Contact N&ordm; </th>
				<th> Access rights </th>
				<td> <?= ( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT )?'<a href="?id=0" class="button new"> New </a>':'')?> </td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="5"> <form><label for="showall">Show all personnel?</label><input type="checkbox" name="showall" value="1" <?=($_GET['showall']?' checked="checked"':'')?> onchange="$(this).parent().submit();" /></form></th>
				
			</tr>
		</tfoot>
		<tbody>
			<?php
				foreach( $user as $obj )
				{
					if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $obj->personnel_id ) )
					{
						echo '<tr'.($obj->enabled?'':' class="ui-state-disabled"').'>';
						//echo '	<th>'.$obj->personnel_id.'</th>';
						echo '	<td>'.$obj->flight.'</td>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="?id='.$obj->personnel_id.'">'.$obj->lastname.', '.$obj->firstname.'</a></td>';
						echo '	<td>'.$obj->mobile_phone.'</td>';
						switch( $obj->access_rights )
						{
							case ATC_USER_LEVEL_ADMIN:
								echo '<td> <strong>Admin</strong> </td>';
								break;
							case ATC_USER_LEVEL_CADET:
								echo '<td> Cadet </td>';
								break;
							case ATC_USER_LEVEL_SNCO:
								echo '<td> <acronym title="Senior Non-Commissioned Officer">SNCO</acronym> </td>';
								break;
							case ATC_USER_LEVEL_ADJUTANT:
								echo '<td> Adjutant </td>';
								break;
							case ATC_USER_LEVEL_STORES:
								echo '<td> Stores Officer </td>';
								break;
							case ATC_USER_LEVEL_TRAINING:
								echo '<td> Training Officer </td>';
								break;
							case ATC_USER_LEVEL_CUCDR:
								echo '<td> Unit Commander </td>';
								break;
							case ATC_USER_LEVEL_SUPOFF:
								echo '<td> Supplimentary Officer </td>';
								break;
							case ATC_USER_LEVEL_OFFICER:
								echo '<td> Officer </td>';
								break;
							case ATC_USER_LEVEL_EMRG_CONTACT:
								echo '<td class="ui-state-highlight"> Emergency Contact </td>';
								break;
							case ATC_USER_LEVEL_TREASURER:
								echo '<td class="ui-state-highlight"> Treasurer </td>';
								break;
							case ATC_USER_LEVEL_USC:
								echo '<td class="ui-state-highlight"> Unit Support Committee </td>';
								break;
							default:
								echo '<td class="ui-state-error">Unknown</td>';
						}
						if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $obj->personnel_id ) )
							echo '	<td> <a href="?id='.$obj->personnel_id.'" class="button edit">Edit</a> </td>';
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
	<script>
		//$("thead th").button({ icons: { primary: "ui-icon-arrowthick-2-n-s" } }).removeClass("ui-corner-all").css({ display: "table-cell" });
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
		$('td.ui-state-highlight').removeClass('ui-state-highlight').parent().addClass('ui-state-highlight');
	</script>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>