<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	$user = $ATC->get_personnel($id);
	
	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<form id="personnelform" method="post">
			<h2> Personal details </h2>
			<fieldset id="personal">
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
				<button type="submit">Save</button>
			</fieldset>

			<h2> Access Rights </h2>
			<fieldset id="accessrights">
				<input type="checkbox" name="access_rights" id="access_rights_admin" value="" />
				<label for="access_rights_admin">Admin</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_cadet" value="" />
				<label for="access_rights_cadet">Cadet</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_jnco" value="" />
				<label for="access_rights_jnco">Junior NCO</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_snco" value="" />
				<label for="access_rights_snco">Senior NCO</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_officer" value="" />
				<label for="access_rights_officer">Officer</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_adjutant" value="" />
				<label for="access_rights_adjutant">Adjutant</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_stores" value="" />
				<label for="access_rights_stores">Stores</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_training" value="" />
				<label for="access_rights_training">Training</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_cucdr" value="" />
				<label for="access_rights_cucdr">Cadet Unit Commander</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_supoff" value="" />
				<label for="access_rights_supoff">Supplimentary Officer</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_treasurer" value="" />
				<label for="access_rights_treasurer">Treasurer</label><br />
				<input type="checkbox" name="access_rights" id="access_rights_usc" value="" />
				<label for="access_rights_usc">Unit Support Committee</label><br />
				<button type="submit">Save</button>
			</fieldset>
			
			<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){
					user = jQuery.parseJSON( '<?= json_encode( $user ) ?>' );

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