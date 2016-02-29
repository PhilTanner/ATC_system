<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );

	if( isset( $_POST['firstname_0'] ) && isset( $_GET['id'] ) )
	{
		try {
			foreach( $_POST['nok_id'] as $i )
			{
				if( strlen(trim($_POST['firstname_'.$i])) && strlen(trim($_POST['lastname_'.$i])) )
				{
					$ATC->set_next_of_kin( 
						$i,
						$_GET['id'],
						$_POST['firstname_'.$i],
						$_POST['lastname_'.$i],
						$_POST['relationship_'.$i],
						$_POST['email_'.$i],
						$_POST['mobile_'.$i],
						$_POST['home_'.$i],
						$_POST['address1_'.$i],
						$_POST['address2_'.$i],	
						$_POST['city_'.$i],
						$_POST['postcode_'.$i]
					);
				}
			}
			
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

	$nok = $ATC->get_nok($id);
	
	if( is_array($nok) && $ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW, $id ) )
	{	
		echo '<form id="nokform" method="post" action="nok.php?id='.(int)$_GET['id'].'#nokform">';
		foreach( $nok as $user )
		{ 
?>
		<fieldset>
			<legend><?= $user->firstname ?> <?= $user->lastname ?> </legend>
			<input type="hidden" name="nok_id[]" value="<?= $user->kin_id ?>" />
			<label for="firstname_<?= $user->kin_id ?>">Name</label>
			<input type="text" name="firstname_<?= $user->kin_id ?>" id="firstname_<?= $user->kin_id ?>" value="<?= htmlentities($user->firstname)?>" maxlength="50" placeholder="First name" class="halfsize" />
			<input type="text" name="lastname_<?= $user->kin_id ?>" id="lastname_<?= $user->kin_id ?>" value="<?= htmlentities($user->lastname)?>" maxlength="50" placeholder="Last name" class="halfsize" /> <br />
			<label for="relationship_<?= $user->kin_id ?>">Relationship</label>
			<select name="relationship_<?= $user->kin_id ?>" id="relationship_0">
				<option value="<?=ATC_NOK_TYPE_MOTHER?>"<?=($user->relationship==ATC_NOK_TYPE_MOTHER?' selected="selected""':'')?>> Mother </option>
				<option value="<?=ATC_NOK_TYPE_STEPMOTHER?>"<?=($user->relationship==ATC_NOK_TYPE_STEPMOTHER?' selected="selected"':'')?>> Step-Mother </option>
				<option value="<?=ATC_NOK_TYPE_GRANDMOTHER?>"<?=($user->relationship==ATC_NOK_TYPE_GRANDMOTHER?' selected="selected""':'')?>> Grandmother </option>
				<option value="<?=ATC_NOK_TYPE_FATHER?>"<?=($user->relationship==ATC_NOK_TYPE_FATHER?' selected="selected"':'')?>> Father </option>
				<option value="<?=ATC_NOK_TYPE_STEPFATHER?>"<?=($user->relationship==ATC_NOK_TYPE_STEPFATHER?' selected="selected"':'')?>> Step-Father </option>
				<option value="<?=ATC_NOK_TYPE_GRANDFATHER?>"<?=($user->relationship==ATC_NOK_TYPE_GRANDFATHER?' selected="selected""':'')?>> Grandfather </option>
				<option value="<?=ATC_NOK_TYPE_SPOUSE?>"<?=($user->relationship==ATC_NOK_TYPE_SPOUSE?' selected="selected"':'')?>> Spouse </option>
				<option value="<?=ATC_NOK_TYPE_DOMPTNR?>"<?=($user->relationship==ATC_NOK_TYPE_DOMPTNR?' selected="selected"':'')?>> Domestic Partner </option>
				<option value="<?=ATC_NOK_TYPE_SIBLING?>"<?=($user->relationship==ATC_NOK_TYPE_SIBLING?' selected="selected"':'')?>> Sibling </option>
				<option value="<?=ATC_NOK_TYPE_OTHER?>"<?=($user->relationship==ATC_NOK_TYPE_OTHER?' selected="selected"':'')?>> Other </option>
			</select><br />
			<label for="email_<?= $user->kin_id ?>">Email address</label>
			<input type="email" name="email_<?= $user->kin_id ?>" id="email_<?= $user->kin_id ?>" value="<?= htmlentities($user->email)?>" maxlength="255" placeholder="Email address" /> <br />
			<label for="mobile_<?= $user->kin_id ?>">Phone numbers</label>
			<input type="tel" name="mobile_<?= $user->kin_id ?>" id="mobile_<?= $user->kin_id ?>" value="<?= htmlentities($user->mobile_number)?>" maxlength="20" placeholder="Mobile (cell)" class="halfsize"  />
			<input type="tel" name="home_<?= $user->kin_id ?>" id="home_<?= $user->kin_id ?>" value="<?= htmlentities($user->home_number)?>" maxlength="20" placeholder="Home phone (Optional)"  class="halfsize" /> <br />
			<label for="address1_<?= $user->kin_id ?>" style="height:8em;float:left;">Address</label>
			<input type="text" name="address1_<?= $user->kin_id ?>" id="address1_<?= $user->kin_id ?>" value="<?= htmlentities($user->address1)?>" maxlength="150" placeholder="Address line 1" /> <br />
			<input type="text" name="address2_<?= $user->kin_id ?>" id="address2_<?= $user->kin_id ?>" value="<?= htmlentities($user->address2)?>" maxlength="150" placeholder="Address line 2 (Optional)" /> <br />
			<input type="text" name="city_<?= $user->kin_id ?>" id="city_<?= $user->kin_id ?>" value="<?= htmlentities($user->city)?>" maxlength="50" placeholder="Address (city)" /> <br />
			<input type="number" name="postcode_<?= $user->kin_id ?>" id="postcode_<?= $user->kin_id ?>" value="<?= htmlentities($user->postcode)?>" maxlength="4" placeholder="Post Code (Optional)" /> <br />
			
		</fieldset>
<?php 
		}
?>
		<fieldset>
			<legend class="new">New Next Of Kin entry</legend>
			<input type="hidden" name="nok_id[]" value="0" />
			<label for="firstname_0">Name</label>
			<input type="text" name="firstname_0" id="firstname_0" value="" maxlength="50" placeholder="First name" class="halfsize" />
			<input type="text" name="lastname_0" id="lastname_0" value="" maxlength="50" placeholder="Last name" class="halfsize" /> <br />
			<label for="relationship_0">Relationship</label>
			<select name="relationship_0" id="relationship_0">
				<option value="<?=ATC_NOK_TYPE_MOTHER?>"> Mother </option>
				<option value="<?=ATC_NOK_TYPE_STEPMOTHER?>"> Step-Mother </option>
				<option value="<?=ATC_NOK_TYPE_GRANDMOTHER?>"> Grandmother </option>
				<option value="<?=ATC_NOK_TYPE_FATHER?>"> Father </option>
				<option value="<?=ATC_NOK_TYPE_STEPFATHER?>"> Step-Father </option>
				<option value="<?=ATC_NOK_TYPE_GRANDFATHER?>"> Grandfather </option>
				<option value="<?=ATC_NOK_TYPE_SPOUSE?>"> Spouse </option>
				<option value="<?=ATC_NOK_TYPE_DOMPTNR?>"> Domestic Partner </option>
				<option value="<?=ATC_NOK_TYPE_SIBLING?>"> Sibling </option>
				<option value="<?=ATC_NOK_TYPE_OTHER?>"> Other </option>
			</select><br />
			<label for="email_0">Email address</label>
			<input type="email" name="email_0" id="email_0" value="" maxlength="255" placeholder="Email address" /> <br />
			<label for="mobile_0">Phone number</label>
			<input type="tel" name="mobile_0" id="mobile_0" value="" maxlength="20" placeholder="Mobile (cell)"  class="halfsize" />
			<input type="tel" name="home_0" id="home_0" value="" maxlength="20" placeholder="Home phone (Optional)" class="halfsize"  /> <br />
			<label for="address1_0" style="height:8em;float:left;">Address</label>
			<input type="text" name="address1_0" id="address1_0" value="" maxlength="150" placeholder="Address line 1" /> <br />
			<input type="text" name="address2_0" id="address2_0" value="" maxlength="150" placeholder="Address line 2 (Optional)" /> <br />
			<input type="text" name="city_0" id="city_0" value="" maxlength="50" placeholder="Address (city)" /> <br />
			<input type="number" name="postcode_0" id="postcode_0" value="" maxlength="4" placeholder="Post Code (optional)" /> <br />
			
		</fieldset><br />
		<button type="submit">Save</button>
		</form>
		
		<script>
			$(function(){

				$('#nokform button[type=submit]').button({icons: { primary: "ui-icon-disk" }}).css({ clear:'left', marginTop: '1em' });
				$('#nokform fieldset legend').button({ icons: { primary: "ui-icon-person" }}).parent().addClass('ui-corner-all').children('fieldset legend.new').addClass('ui-state-highlight');
				$('#nokform').sortable().submit(function(e) {
					e.preventDefault(); // stop the submit button actually submitting
					
					$.ajax({
						   type: "POST",
						   url: $('#nokform').attr('action'),
						   data: $("#nokform").serialize(),
						   beforeSend: function()
						   {
							$('#nokform').addClass('ui-state-disabled');
						   },
						   complete: function()
						   {
							$('#nokform').removeClass('ui-state-disabled');
						   },
						   success: function(data)
						   {
							   // True to ensure we don't just use a cached version, but get a fresh copy from the server
							   location.reload(true);
							return false;
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
								  },
								  open: function() {
									 $('.ui-dialog-titlebar').addClass('ui-state-error');
								  }
								});
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