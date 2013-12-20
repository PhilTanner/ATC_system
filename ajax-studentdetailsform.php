<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	 
	$query = "SELECT * FROM student WHERE student_id = ".(int)$_GET['student_id']." LIMIT 1;";
	
	if ($result = $mysqli->query($query)) 
		$obj = $result->fetch_object();
	
	if( !isset($obj->firstname) ) 
	{
		$obj->student_id = 0;
		$obj->firstname = '';
		$obj->lastname = '';
		$obj->is_male = 0;
		$obj->parent_firstname = '';
		$obj->parent_lastname = '';
		$obj->parent_title = '';
		$obj->parent_email = '';
		$obj->balance = 0;
		$obj->display = 1;
	}
?>
<form id="studentdetails" onsubmit="return false;">
	<fieldset>
		<input type="hidden" name="student_id" id="student_id" value="<?=$obj->student_id?>" />
		<p class="formrow">
			<label for="firstname">First name</label>
			<input type="text" name="firstname" id="firstname" value="<?=$obj->firstname?>" required="required" />
		</p>
		<p class="formrow">
			<label for="lastname">Last name</label>
			<input type="text" name="lastname" id="lastname" value="<?=$obj->lastname?>" required="required" />
		</p>
		<p class="formrow">
			<label for="male">Boy</label>
			<input type="radio" name="is_male" id="male" value="1" required="required" <?=($obj->is_male?' checked="checked"':'')?> />
			<label for="female">Girl</label>
			<input type="radio" name="is_male" id="female" value="0" required="required" <?=($obj->is_male?'':' checked="checked"')?> />
		</p>
		<p class="formrow">
			<label for="parent_firstname">Parent first name</label>
			<input type="text" name="parent_firstname" id="parent_firstname" value="<?=$obj->parent_firstname?>" />
		</p>
		<p class="formrow">
			<label for="parent_lastname">Parent last name</label>
			<input type="text" name="parent_lastname" id="parent_lastname" value="<?=$obj->parent_lastname?>" />
		</p>
		<p class="formrow">
			<label for="parent_title">Parent title/salutation</label>
			<input type="text" name="parent_title" id="parent_title" value="<?=$obj->parent_title?>" />
		</p>
		<p class="formrow">
			<label for="parent_email">Parent email address</label>
			<input type="text" name="parent_email" id="parent_email" value="<?=$obj->parent_email?>" />
		</p>
		<p class="formrow">
			<label for="display">Display student?</label>
			<input type="checkbox" name="display" id="display" value="1" <?=($obj->display?'checked="checked" ':'')?> />
		</p>
		<p class="formrow" style="text-align:right;">
			<button type="reset" class="cancel">Cancel</button>
			<button type="submit" class="save">Save</button>
		</p>
	</fieldset>
</form>
<?php
		/* free result set */
	$result->close();
	
	/* close connection */
	$mysqli->close();
?>
<script type="text/javascript">
	$('#studentdetails button.save').button({ icons: { primary: 'ui-icon-disk' } }).click(function(){
		$.ajax({
			url:	"ajax-formsavestudent.php",
			type: 	"POST",
			data: 	$('#studentdetails').serialize(),
			success: function(response){
				eval($(response).text());
			}
		});
	});
	$('#studentdetails button.cancel').button({ icons: { primary: 'ui-icon-cancel' } }).click(function(){ $('#dialog').dialog('close'); });
</script>
