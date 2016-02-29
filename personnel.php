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
			<h2 href="personal.php?id=<?=$user->personnel_id?>"> Personal details &mdash; <?=$user->display_name?> </h2>
			<div id="personal">
			</div>
	
			<h2 href="nok.php?id=<?=$user->personnel_id?>"> Next of Kin </h2>
			<div id="nok">
			</div>
	
			<h2 href="attendance.php?id=<?=$user->personnel_id?>"> Attendance </h2>
			<div id="attendance">
			</div>
	
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
	<table>
		<thead>
			<tr>
				<th colspan="2"> Name </th>
				<td> <a href="?id=0" class="button new"> New </a> </td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="3"> <form><label for="showall">Show all personnel?</label><input type="checkbox" name="showall" value="1" <?=($_GET['showall']?' checked="checked"':'')?> onchange="$(this).parent().submit();" /></form></th>
				
			</tr>
		</tfoot>
		<tbody>
			<?php
				foreach( $user as $obj )
				{
					echo '<tr'.($obj->enabled?'':' class="ui-state-disabled"').'>';
					//echo '	<th>'.$obj->personnel_id.'</th>';
					echo '	<td>'.$obj->rank.'</td>';
					echo '	<td><a href="?id='.$obj->personnel_id.'">'.$obj->lastname.', '.$obj->firstname.'</a></td>';
					echo '	<td> <a href="?id='.$obj->personnel_id.'" class="button edit">Edit</a> </td>';
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
	<script>
		$("thead th").button({ icons: { primary: "ui-icon-arrowthick-2-n-s" } }).removeClass("ui-corner-all").css({ display: "table-cell" });
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
	</script>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>