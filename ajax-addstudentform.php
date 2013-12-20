<form id="newstudent" onsubmit="return false;">
	<fieldset>
		<p class="formrow">
			<label for="firstname">First name</label>
			<input type="text" name="firstname" id="firstname" required="required" />
		</p>
		<p class="formrow">
			<label for="lastname">Last name</label>
			<input type="text" name="lastname" id="lastname" required="required" />
		</p>
		<p class="formrow">
			<label for="male">Boy</label>
			<input type="radio" name="is_male" id="male" value="1" required="required" />
			<label for="female">Girl</label>
			<input type="radio" name="is_male" id="female" value="0" required="required" />
		</p>
		<p class="formrow" style="text-align:right;">
			<button type="reset" class="cancel">Cancel</button>
			<button type="submit" class="save">Save</button>
		</p>
	</fieldset>
</form>
<script type="text/javascript">
	$('#newstudent button.save').button({ icons: { primary: 'ui-icon-disk' } }).click(function(){
		$.ajax({
			url:	"ajax-formsavestudent.php",
			type: 	"POST",
			data: 	$('#newstudent').serialize(),
			dataType: "html"
		});
	});
	$('#newstudent button.cancel').button({ icons: { primary: 'ui-icon-cancel' } });
</script>
