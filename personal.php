<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if( !isset($_GET['action']) )
	{
		$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
		$user = $ATC->get_personnel($id);
	
		if( isset( $_POST['personnel_id'] ) && isset( $_GET['id'] ) )
		{
			foreach( $_POST as $var => $val )
				$user->$var = $val;
			if( !isset($_POST['enabled']) || !$_POST['enabled'] )
				$user->enabled = 0;
			if( !isset($_POST['social_media_approved']) || !$_POST['social_media_approved'] )
				$user->social_media_approved = 0;
		
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
			<fieldset style="clear:right;">
				<legend> Personal details </legend>
			
				<label for="firstname">First name</label>
				<input type="text" name="firstname" id="firstname" value="" maxlength="50" required="required" placeholder="First name" /> <br />
				
				<label for="lastname">Last name</label>
				<input type="text" name="lastname" id="lastname" value="" maxlength="100" required="required" placeholder="Last name" /> <br />
				
				<label for="is_female">Gender</label>
				<select name="is_female" id="is_female">
					<option value="-1"> Female </option>
					<option value="0"> Male </option>
				</select><br />
			
				<label for="mobile_phone">Mobile phone n&ordm;</label>
				<input type="tel" name="mobile_phone" id="mobile_phone" value="" maxlength="50" placeholder="Mobile (cell) number" /> <br />
				
				<label for="email">Email address</label>
				<input type="email" name="email" id="email" value="" maxlength="255" required="required" placeholder="Email address" /> <br />
				
				<label for="password">Password</label>
				<input type="password" name="password" id="password" value="" maxlength="255" required="required" placeholder="Password"  /> <br />
				
				<label for="enabled">Enabled</label>
				<input type="checkbox" name="enabled" id="enabled" value="-1" checked="checked" <?= ($ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_EDIT)?'':' class="uneditable" readonly="readonly" disabled="disabled"')?> /><br />
			</fieldset>
			<fieldset>
				<legend> Medical &amp; notes </legend>
				
				<label for="dob">Date of birth</label>
				<input type="date" name="dob" id="dob" value="" maxlength="50" required="required" /><br />
				
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
				
			</fieldset>
			<fieldset>
				<legend> Squadron details </legend>
				
				<label for="access_rights">Access level</label>
				<select name="access_rights" id="access_rights" <?= ($ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_EDIT)?'':' class="uneditable" readonly="readonly" disabled="disabled"')?> >
					<?php
						foreach( $translations['userlevel'] as $key => $val )
							echo '<option value="'.$key.'"> '.$val.' </option>';
					?>
				</select><br />
				
				<label for="flight">Flight</label>
				<input type="text" name="flight" id="flight" value="" maxlength="15" placeholder="What flight are they in?" /> <br />
				
				<label for="joined_date">Date joined</label>
				<input type="date" name="joined_date" id="joined_date" value="" maxlength="50" required="required" /><br />
				
				<label for="left_date">Date left</label>
				<input type="date" name="left_date" id="left_date" value="" maxlength="50" /><br />
				
				<label for="social_media_approved">Social Media</label>
				<input type="checkbox" name="social_media_approved" id="social_media_approved" value="-1" checked="checked" /><br />
				
			</fieldset>
			<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){
				user = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $user )) ?>' );
				flights = jQuery.parseJSON( '<?= str_replace("'","\\'", json_encode( $ATC->get_flights() )) ?>' );
				
				$('#personalform fieldset legend').button().parent().addClass('ui-corner-all');

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
				$('#flight').autocomplete({ minLength: 0, source: flights }).val(user['flight']);
				$('#dob').val(user['dob']);
				$('#access_rights').val( user['access_rights']);
				$('#is_female').val( user['is_female']);
				$('#joined_date').val(user['joined_date']);
				$('#left_date').val(user['left_date']);
				$('#social_media_approved').prop('checked', (user['social_media_approved']==-1?true:false));
				$('#enabled').prop('checked', (user['enabled']==-1?true:false));

				// Update our password settings for editing existing users for clarity
				if( user['personnel_id'] )
					$('#password').prop('required', false).prop('placeholder', 'Leave blank to keep current password').prev().html('Change password');

				$('#personalform button[type=submit]').button({icons: { primary: "ui-icon-disk" }});
				$('#personalform').submit(function(e) {
					
					e.preventDefault(); // stop the submit button actually submitting
					// Unless we do this, enabled checkbox is not passed thru, disabling any user who edits their profile
					$('#personalform input').removeAttr('readonly').removeAttr('disabled');
					$('#personalform select').removeAttr('readonly').removeAttr('disabled');
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
								$('#personalform input.uneditable').attr('readonly','readonly').attr('disabled','disabled');
								$('#personalform select.uneditable').attr('readonly','readonly').attr('disabled','disabled');
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
	} elseif( isset($_GET['action']) && $_GET['action'] == 'promotion' && isset($_GET['id']) ) {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			try {
				$ATC->add_promotion( $_POST['rank_id'], $_GET['id'], $_POST['date_achieved'] );
			} catch (ATCExceptionInsufficientPermissions $e) {	
				header("HTTP/1.0 401 Unauthorised");
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			} catch (ATCExceptionDBError $e) {
				header("HTTP/1.0 500 Internal Server Error");
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			} catch (ATCExceptionDBConn $e) {
				header("HTTP/1.0 500 Internal Server Error");
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			} catch (ATCException $e) {
				header("HTTP/1.0 400 Bad Request");
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			} catch (Exception $e) {
				header("HTTP/1.0 500 Internal Server Error");
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			exit();
		}
		$promotions  = $ATC->get_promotion_history( $_GET['id'] );
		$ranks = $ATC->get_ranks();
?>
		<table>
			<thead>
				<tr>
					<th> Rank </th>
					<th> Date </th>
					<?= ($ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_EDIT)?'<td><a href="personal.php?id='.$_GET['id'].'&amp;action=promotion" class="button new">New</a></td>':'')?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($promotions as $promotion)
					{
						echo '<tr>';
						echo '	<td> '.$promotion->rank.' </td>';
						echo '	<td> '.date(ATC_SETTING_DATE_OUTPUT.", Y", strtotime($promotion->date_achieved)).' </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		<script>
			$("thead th").button(/*{ icons: { primary: "ui-icon-arrowthick-2-n-s" } }*/).removeClass("ui-corner-all").css({ display: "table-cell" });
			$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false }).click(function(e){
				e.preventDefault(); // stop the link actually firing
				var href = $(this).attr("href");
				$('#dialog').html('<form name="newrank" id="newrank">'+
						'	<label for="rank">New Rank</rank><br />'+
						'	<select name="rank_id" id="rank_id">'+
						'		<?php foreach($ranks as $rank) echo '<option value="'.$rank->rank_id.'">'.$rank->rank.'</option>'; ?>'+
						'	</select><br />'+
						'	<label for="date_achieved">Date achieved</label><br />'+
						'	<input type="date" value="" name="date_achieved" id="date_achieved" />'+
						'</form>').dialog({
					modal: true,
					width: 600,
					title: 'Add new rank record',
					buttons: {
						Cancel: function() {
							$( this ).dialog( "close" );
						},
						Save: function() {
						
							$.ajax({
						 	  type: "POST",
						  	 url: href,
						   	data: $("#newrank").serialize(),
						   	beforeSend: function()
						   	{
							   	$('#newrank').addClass('ui-state-disabled');
						   	},
						   	complete: function()
						   	{
							   	$('#newrank').removeClass('ui-state-disabled');
						   	},
						   	success: function(data)
						   	{
							   	// True to ensure we don't just use a cached version, but get a fresh copy from the server
							   	location.reload(true);
						   	},
						   	error: function(data)
						   	{
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
						 
							$( this ).dialog( "close" );
						}
				  	},
				  	close: function() { 
						$( this ).dialog( "destroy" ); 
				  	},
				  	open: function() {
				
					
					}
				});
				return false;
			});
		</script>
<?php
	}  elseif( isset($_GET['action']) && $_GET['action'] == 'finance' && isset($_GET['id']) ) {
	
		require_once "atc_finance.class.php";
		$ATC_Finance = new ATC_Finance();
	
		if($ATC_Finance->user_has_permission( ATC_PERMISSION_FINANCE_VIEW, $_GET['id']) )
			$payments = $ATC_Finance->get_account_history($_GET['id'], '1970-01-01', date('c'));
		
?>
		<table class="tablesorter">
			<thead>
				<tr>
					<th> Date </th>
					<th> Amount </th>
					<th> Payment type </th>
					<th> Reference </th>
					<th> Recorded by </th>
					<!-- <?= ($ATC->user_has_permission(ATC_PERMISSION_FINANCE_EDIT)?'<td><a href="personal.php?id='.$_GET['id'].'&amp;action=finance" class="button new">New</a></td>':'')?>-->
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th> Total: </th>
					<td colspan="4"></td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					
					$totalamount = 0;
					foreach($payments as $obj)
					{
						$totalamount += $obj->amount;
						echo '<tr>';
						echo '	<td> '.date(ATC_SETTING_DATE_OUTPUT." Y", strtotime($obj->created)).' </td>';
						echo '	<td> '.$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $obj->amount).' </td>';
						echo '	<td> '.htmlentities($translations['paymenttype'][$obj->payment_type]).' </td>';
						echo '	<td> '.htmlentities($obj->reference).' </td>';
						echo '	<td> '.htmlentities($obj->rank.' '.$obj->display_name).' </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		<script>$('tfoot td').html('<?=$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $totalamount)?>');</script>
<?php
		$ATC->gui_output_page_footer(null);
	} elseif( isset($_GET['action']) && $_GET['action'] == 'activities' && isset($_GET['id']) ) {
                $activities = $ATC->get_activities( '2010-01-01', (365.25*20), (int)$_GET['id'] );
?>
                <table>
                        <thead>
                                <tr>
					<th> Date </th>
                                        <th> Activity </th>
					<th> Location </th>
                                        <th> Type </th>
					<th> Off </th>
					<th> CDT </th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                        foreach($activities as $activity)
                                        {
                                                echo '<tr>';
                                                echo '  <td> '.date(ATC_SETTING_DATE_OUTPUT.", Y", strtotime($activity->startdate)).' </td>';
                                                echo '  <td> '.$activity->title.' </td>';
						echo '	<td> '.$activity->location_name.' </td>';
						echo '	<td> '.$activity->type.' </td>';
						echo '	<td> '.$activity->officers_attending.' </td>';
						echo '	<td> '.$activity->cadets_attending.' </td>';
                                                echo '</tr>';
                                        }
                                ?>
                        </tbody>
                </table>
<?php
	}
?>
