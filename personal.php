<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);

	if( isset( $_POST['personnel_id'] ) && isset( $_GET['id'] ) )
	{
		foreach( $_POST as $var => $val )
			$user->$var = $val;
		if( !isset($_POST['enabled']) || !$_POST['enabled'] )
			$user->enabled = 0;
		
		try {
			$ATC->set_personnel( $user );
		} catch (ATCExceptionInsufficientPermissions $e) {
			header("HTTP/1.0 401 Unauthorised");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			exit();
		} catch (ATCExceptionDBError $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			exit();
		} catch (ATCExceptionDBConn $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			exit();
		} catch (ATCException $e) {
			header("HTTP/1.0 400 Bad Request");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			exit();
		} catch (Exception $e) {
			header("HTTP/1.0 500 Internal Server Error");
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			exit();
		}
	}

	if( is_object($user) && $ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW, $id ) )
	{	
?>

		<form id="personalform" method="post" action="personal.php?id=<?=$user->personnel_id?>#personalform">
			<input type="hidden" name="personnel_id" id="personnel_id" value="" />
			<div style="float:right">
				<label for="created">Date created</label>
				<input type="datetime-local" name="created" id="created" value="" maxlength="50" readonly="readonly" disabled="disabled" style="width:16em;" />
			</div>
			<label for="firstname">First name</label>
			<input type="text" name="firstname" id="firstname" value="" maxlength="50" required="required" placeholder="First name" /> <br />
			<label for="lastname">Last name</label>
			<input type="text" name="lastname" id="lastname" value="" maxlength="100" required="required" placeholder="Last name" /> <br />
			<label for="is_female">Gender</label>
			<select name="is_female" id="is_female">
				<option value="-1"> Female </option>
				<option value="0"> Male </option>
			</select><br />
			<label for="email">Email address</label>
			<input type="email" name="email" id="email" value="" maxlength="255" required="required" placeholder="Email address" /> <br />
			<label for="mobile_phone">Mobile phone n&ordm;</label>
			<input type="tel" name="mobile_phone" id="mobile_phone" value="" maxlength="50" placeholder="Mobile (cell) number" /> <br />
			<label for="password">Password</label>
			<input type="password" name="password" id="password" value="" maxlength="255" required="required" placeholder="Password"  /> <br />
			<label for="allergies">Allergies &amp; Treatment</label>
			<input type="text" name="allergies" id="allergies" value="" maxlength="255" placeholder="Allergies &amp; Treatment (Food &amp; Natural)" /> <br />
			<label for="medical_conditions">Medical Conditions</label>
			<input type="text" name="medical_conditions" id="medical_conditions" value="" maxlength="255" placeholder="Medical Conditions (Asthma, Hay Fever, Migranes etc)" /> <br />
			<label for="medicinal_reactions">Reaction to Medicies</label>
			<input type="text" name="medicinal_reactions" id="medicinal_reactions" value="" maxlength="255" placeholder="Detail" /> <br />
			<label for="dietary_requirements">Dietary</label>
			<input type="text" name="dietary_requirements" id="dietary_requirements" value="" maxlength="255" placeholder="Special Dietary Requirements" /> <br />
			<label for="other_notes">Other notes</label>
			<input type="text" name="other_notes" id="other_notes" value="" maxlength="255" placeholder="Any other notes" /> <br />
			<label for="dob">Date of birth</label>
			<input type="date" name="dob" id="dob" value="" maxlength="50" required="required" /><br />
			<label for="joined_date">Date joined</label>
			<input type="date" name="joined_date" id="joined_date" value="" maxlength="50" required="required" /><br />
			<label for="left_date">Date left</label>
			<input type="date" name="left_date" id="left_date" value="" maxlength="50" /><br />
			<label for="access_rights">Access level</label>
			<select name="access_rights" id="access_rights">
				<option value="<?=ATC_USER_LEVEL_CADET?>"> Cadet </option>
				<option value="<?=ATC_USER_LEVEL_NCO?>"> NCO </option>
				<option value="<?=ATC_USER_LEVEL_SUPOFF?>"> Supplimentary Officer </option>
				<option value="<?=ATC_USER_LEVEL_OFFICER?>"> Officer </option>
				<option value="<?=ATC_USER_LEVEL_ADJUTANT?>"> Adjutant </option>
				<option value="<?=ATC_USER_LEVEL_STORES?>"> Stores </option>
				<option value="<?=ATC_USER_LEVEL_TRAINING?>"> Training </option>
				<option value="<?=ATC_USER_LEVEL_CUCDR?>"> CUCDR </option>
				<option value="<?=ATC_USER_LEVEL_USC?>"> Unit Support Committee Member </option>
				<option value="<?=ATC_USER_LEVEL_TREASURER?>"> Treasurer </option>
				<option value="<?=ATC_USER_LEVEL_EMRG_CONTACT?>"> Emergency Contact </option>
				<option value="<?=ATC_USER_LEVEL_ADMIN?>"> Admin </option>
			</select><br />
			<label for="enabled">Enabled</label>
			<input type="checkbox" name="enabled" id="enabled" value="-1" checked="checked" /><br />
			<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){
				user = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $user )) ?>' );

				$('#personnel_id').val(user['personnel_id']);
				$('#firstname').val(user['firstname']);
				$('#lastname').val(user['lastname']);
				$('#email').val(user['email']);
				$('#mobile_phone').val(user['mobile_phone']);
				$('#allergies').val(user['allergies']);
				$('#medical_conditions').val(user['medical_conditions']);
				$('#dietary_requirements').val(user['dietary_requirements']);
				$('#medicinal_reactions').val(user['medicinal_reactions']);
				$('#other_notes').val(user['other_notes']);
				$('#created').val(user['created']);
				$('#dob').val(user['dob']);
				$('#access_rights').val( user['access_rights']);
				$('#is_female').val( user['is_female']);
				$('#joined_date').val(user['joined_date']);
				$('#left_date').val(user['left_date']);
				if( user['enabled'] == "-1" )
					$('#enabled').prop('checked', true);
				else
					$('#enabled').prop('checked', false);

				// Update our password settings for editing existing users for clarity
				if( user['personnel_id'] )
					$('#password').prop('required', false).prop('placeholder', 'Leave blank to keep current password').prev().html('Change password');

				$('#personalform button[type=submit]').button({icons: { primary: "ui-icon-disk" }});
				$('#personalform').submit(function(e) {
					
					e.preventDefault(); // stop the submit button actually submitting
					
					$.ajax({
						   type: "POST",
						   url: $('#personalform').attr('action'),
						   data: $("#personalform").serialize(),
						   beforeSend: function()
						   {
							$('#personalform').addClass('ui-state-disabled');
						   },
						   complete: function()
						   {
							$('#personalform').removeClass('ui-state-disabled');
						   },
						   success: function(data)
						   {
							// When creating new users, redirect to the user page after creation, to continue editing.
							if( $('#personalform').attr('action') != $(data).filter('#personalform').attr('action') )
								// location.replace used to keep expected back button behaviour
								window.location.replace('personnel.php?id='+$(data).filter('#personalform').attr('action').match(/\d+/)[0]);
							$('<img src="save-ok.png" style="position: absolute; left: 70em; top: 12em" id="save_indicator" />').appendTo('#personalform').fadeOut(1500, function(){ $('#save_indicator').remove(); } );
							return false;
						   },
						   error: function(data)
						   {
							$('<img src="save-fail.png" style="position: absolute; left: 70em; top: 12em" id="save_indicator" />').appendTo('#personalform');
							$('#dialog').html("There has been a problem. The server responded:<br /><br /> <code>"+data.responseText+"</code>").dialog({
							      modal: true,
							      //dialogClass: 'ui-state-error',
							      title: 'Error!',
								  buttons: {
									Close: function() {
									  $( this ).dialog( "close" );
									}
								  },
								  close: function() { 
									$( this ).dialog( "destroy" ); 
									$('#save_indicator').fadeOut(1500, function(){ $('#save_indicator').remove() });
								  },
								  open: function() {
									 $('.ui-dialog-titlebar').addClass('ui-state-error');
								  }
								}).filter('ui-dialog-titlebar');
							return false;
						}
					});
					return false;						
				});
			});
		</script>

<?php
	} 
?>