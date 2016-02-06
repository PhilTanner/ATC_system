<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);

	if( isset( $_POST['personnel_id'] ) && isset( $_GET['id'] ) )
	{
		$accessrights = 0;
		foreach( $_POST['access_rights'] as $permission )
			$accessrights+=(int)$permission;
		$_POST['access_rights'] = $accessrights;
		
		foreach( $_POST as $var => $val )
			$user->$var = $val;
		
		$ATC->set_personnel( $user );
		var_dump($user);
	}
	
	
	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<form id="personnelform" method="post" action="?id=<?=$user->personnel_id?>">
			<h2> Personal details </h2>
			<fieldset id="personal">
				<input type="hidden" name="personnel_id" value="" />
				<label for="firstname">First name</label>
				<input type="text" name="firstname" id="firstname" value="" maxlength="50" required="required" placeholder="First name" /> <br />
				<label for="lastname">Last name</label>
				<input type="text" name="lastname" id="lastname" value="" maxlength="100" required="required" placeholder="Last name" /> <br />
				<label for="email">Email address</label>
				<input type="email" name="email" id="email" value="" maxlength="255" required="required" placeholder="Email address" /> <br />
				<label for="password">Password</label>
				<input type="password" name="password" id="password" value="" maxlength="255" required="required" placeholder="Password"  /> <br />
				<label for="created">Date created</label>
				<input type="text" name="created" id="created" value="" maxlength="50" required="required" readonly="readonly" disabled="disabled" /><br />
				<button type="submit">Save</button>
			</fieldset>

			<h2> Access Rights </h2>
			<fieldset id="accessrights">
				<input type="checkbox" name="access_rights[]" id="access_rights_admin" value="<?=ATC_USER_LEVEL_ADMIN?>" />
				<label for="access_rights_admin">Admin</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_cadet" value="<?=ATC_USER_LEVEL_CADET?>" />
				<label for="access_rights_cadet">Cadet</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_jnco" value="<?=ATC_USER_LEVEL_JNCO?>" />
				<label for="access_rights_jnco">Junior NCO</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_snco" value="<?=ATC_USER_LEVEL_SNCO?>" />
				<label for="access_rights_snco">Senior NCO</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_officer" value="<?=ATC_USER_LEVEL_OFFICER?>" />
				<label for="access_rights_officer">Officer</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_adjutant" value="<?=ATC_USER_LEVEL_ADJUTANT?>" />
				<label for="access_rights_adjutant">Adjutant</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_stores" value="<?=ATC_USER_LEVEL_STORES?>" />
				<label for="access_rights_stores">Stores</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_training" value="<?=ATC_USER_LEVEL_TRAINING?>" />
				<label for="access_rights_training">Training</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_cucdr" value="<?=ATC_USER_LEVEL_CUCDR?>" />
				<label for="access_rights_cucdr">Cadet Unit Commander</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_supoff" value="<?=ATC_USER_LEVEL_SUPOFF?>" />
				<label for="access_rights_supoff">Supplimentary Officer</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_treasurer" value="<?=ATC_USER_LEVEL_TREASURER?>" />
				<label for="access_rights_treasurer">Treasurer</label><br />
				<input type="checkbox" name="access_rights[]" id="access_rights_usc" value="<?=ATC_USER_LEVEL_USC?>" />
				<label for="access_rights_usc">Unit Support Committee</label><br />
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
					if( user['personnel_id'] )
						$('#password').prop('required', false).prop('placeholder', 'Leave blank to keep current password').prev().html('Change password');
	
					if( user['access_rights'] & <?= ATC_USER_LEVEL_ADMIN ?> )
						$('#access_rights_admin').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_CADET ?> )
						$('#access_rights_cadet').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_JNCO ?> )
						$('#access_rights_jnco').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_SNCO ?> )
						$('#access_rights_snco').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_OFFICER ?> )
						$('#access_rights_officer').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_ADJUTANT ?> )
						$('#access_rights_adjutant').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_STORES ?> )
						$('#access_rights_stores').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_TRAINING ?> )
						$('#access_rights_training').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_CUCDR ?> )
						$('#access_rights_cucdr').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_SUPOFF ?> )
						$('#access_rights_supoff').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_TREASURER ?> )
						$('#access_rights_treasurer').prop('checked', true);
					if( user['access_rights'] & <?= ATC_USER_LEVEL_USC ?> )
						$('#access_rights_usc').prop('checked', true);
					
					$('#personnelform').accordion({ header: 'h2' });
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