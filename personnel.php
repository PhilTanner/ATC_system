<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);
	
	var_dump($user);
	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<form id="personnelform">
			<fieldset id="personal">
				<legend> Personal details </legend>
				<input type="hidden" name="personnel_id" value="0" />
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
			</fieldset>

			<fieldset id="accessrights">
				<legend> Access Rights </legend>
				<input type="checkbox" name="access_rights_admin" id="access_rights_admin" value="" />
				<label for="access_rights_admin">Admin</label><br />
				<input type="checkbox" name="access_rights_cadet" id="access_rights_cadet" value="" />
				<label for="access_rights_cadet">Cadet</label><br />
				<input type="checkbox" name="access_rights_jnco" id="access_rights_jnco" value="" />
				<label for="access_rights_jnco">Junior NCO</label><br />
				<input type="checkbox" name="access_rights_snco" id="access_rights_snco" value="" />
				<label for="access_rights_snco">Senior NCO</label><br />
				<input type="checkbox" name="access_rights_officer" id="access_rights_officer" value="" />
				<label for="access_rights_officer">Officer</label><br />
				<input type="checkbox" name="access_rights_adjutant" id="access_rights_adjutant" value="" />
				<label for="access_rights_adjutant">Adjutant</label><br />
				<input type="checkbox" name="access_rights_stores" id="access_rights_stores" value="" />
				<label for="access_rights_stores">Stores</label><br />
				<input type="checkbox" name="access_rights_training" id="access_rights_training" value="" />
				<label for="access_rights_training">Training</label><br />
				<input type="checkbox" name="access_rights_cucdr" id="access_rights_cucdr" value="" />
				<label for="access_rights_cucdr">Cadet Unit Commander</label><br />
				<input type="checkbox" name="access_rights_supoff" id="access_rights_supoff" value="" />
				<label for="access_rights_supoff">Supplimentary Officer</label><br />
				<input type="checkbox" name="access_rights_treasurer" id="access_rights_treasurer" value="" />
				<label for="access_rights_treasurer">Treasurer</label><br />
			</fieldset>
			
			<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){
					user = jQuery.parseJSON( '<?= json_encode( $user ) ?>' );

					$('#firstname').val(user['firstname']);
					$('#larstname').val(user['lastname']);
					$('#email').val(user['email']);
					$('#created').val(user['created']);
	
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
			});
		</script>

<?php
	} elseif( is_array( $user ) ) {
?>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>