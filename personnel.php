<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);

	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<div id="personnelform">
			<h2 href="personal.php?id=<?=$user->personnel_id?>"> Personal details </h2>
			<div id="personal">
			</div>
	
			<h2> Contact details </h2>
			<div id="contact">
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
				<th> ID </th>
				<th> Name </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="2"></th>
				<th> <a href="?id=0" class="button new"> New </a> </th>
			</tr>
		</tfoot>
		<tbody>
			<?php
				foreach( $user as $obj )
				{
					echo '<tr>';
					echo '	<th>'.$obj->personnel_id.'</th>';
					echo '	<td>'.$obj->firstname.' '.$obj->lastname.'</td>';
					echo '	<td> <a href="?id='.$obj->personnel_id.'">Edit</a> </td>';
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>