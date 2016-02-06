<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);

	if( isset( $_POST['personnel_id'] ) && isset( $_GET['id'] ) )
	{
		foreach( $_POST as $var => $val )
			$user->$var = $val;
		
		$ATC->set_personnel( $user );
	}
	
	
	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<form id="personnelform" method="post" action="?id=<?=$user->personnel_id?>">
			<h2 href="personal.php?id=<?=$user->personnel_id?>"> Personal details </h2>
			<fieldset id="personal">
			</fieldset>

			<h2 href="attendance.php?id=<?=$user->personnel_id?>"> Attendance </h2>
			<fieldset id="accessrights">
				<button type="submit">Save</button>
			</fieldset>

			<h2> Activites </h2>
			<fieldset id="accessrights">
				<button type="submit">Save</button>
			</fieldset>

			<h2> Finance </h2>
			<fieldset id="accessrights">
				<button type="submit">Save</button>
			</fieldset>

			<h2> Stores </h2>
			<fieldset id="accessrights">
				<button type="submit">Save</button>
			</fieldset>
			
			<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){
					user = jQuery.parseJSON( '<?= json_encode( $user ) ?>' );

					$('#personnel_id').val(user['personnel_id']);
					$('#firstname').val(user['firstname']);
					$('#lastname').val(user['lastname']);
					$('#email').val(user['email']);
					$('#created').val(user['created']);
					$('#dob').val(user['dob']);
					$('#access_rights').val(user['access_rights']);
					if( user['personnel_id'] )
						$('#password').prop('required', false).prop('placeholder', 'Leave blank to keep current password').prev().html('Change password');
	
					$('#personnelform').accordion({ 
						header: 'h2', 
						changestart:function( event, ui )
									{
										console.log(ui.newHeader.attr('href'));
										ui.newContent.load(ui.newHeader.attr('href'), function(){ setTimeout( function(){ $('#personnelform').accordion('resize'); },500); });
									} 
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