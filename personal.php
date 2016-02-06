<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);

	if( is_object($user) )
	{	
?>
		<input type="hidden" name="personnel_id" id="personnel_id" value="" />
		<label for="firstname">First name</label>
		<input type="text" name="firstname" id="firstname" value="" maxlength="50" required="required" placeholder="First name" /> <br />
		<label for="lastname">Last name</label>
		<input type="text" name="lastname" id="lastname" value="" maxlength="100" required="required" placeholder="Last name" /> <br />
		<label for="email">Email address</label>
		<input type="email" name="email" id="email" value="" maxlength="255" required="required" placeholder="Email address" /> <br />
		<label for="password">Password</label>
		<input type="password" name="password" id="password" value="" maxlength="255" required="required" placeholder="Password"  /> <br />
		<label for="dob">Date of birth</label>
		<input type="date" name="dob" id="dob" value="" maxlength="50" required="required" /><br />
		<label for="created">Date created</label>
		<input type="datetime-local" name="created" id="created" value="" maxlength="50" readonly="readonly" disabled="disabled" /><br />
		<label for="access_rights">Access level</label>
		<select name="access_rights" id="access_rights">
			<option value="<?=ATC_USER_LEVEL_CADET?>"> Cadet </option>
			<option value="<?=ATC_USER_LEVEL_NCO?>"> NCO </option>
			<option value="<?=ATC_USER_LEVEL_SUPOFF?>"> Supplimentary Officer </option>
			<option value="<?=ATC_USER_LEVEL_ADJUTANT?>"> Adjutant </option>
			<option value="<?=ATC_USER_LEVEL_STORES?>"> Stores </option>
			<option value="<?=ATC_USER_LEVEL_TRAINING?>"> Training </option>
			<option value="<?=ATC_USER_LEVEL_CUCDR?>"> CUCDR </option>
			<option value="<?=ATC_USER_LEVEL_USC?>"> Unit Support Committee Member </option>
			<option value="<?=ATC_USER_LEVEL_TREASURER?>"> Treasurer </option>
			<option value="<?=ATC_USER_LEVEL_ADMIN?>"> Admin </option>
		</select><br />
		<button type="submit">Save</button>
		
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
	
					$('#personnelform').accordion({ header: 'h2' });
			});
		</script>

<?php
	} 
?>