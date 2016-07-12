<?php
	require_once "atc_training.class.php";
	$ATC = new ATC_Training();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try {			
			$_POST['nzqa_qualifies'] = (isset($_POST['nzqa_qualifies'])?1:0);
			
			$ATC->set_lesson( 
				$_GET['id'], 
				$_POST['lesson_category_id'], 
				$_POST['code'], 
				$_POST['description'], 
				$_POST['dress_code'],
				$_POST['nzqa_qualifies'], 
				$_POST['level'], 
				7
			);
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
	}
	
	if( !isset($_GET['id']) )
	{
		$lessons = $ATC->get_lesson();
?>
	
		<table class="tablesorter">
			<thead>
				<tr>
					<th> Category </th>
					<th> Code </th>
					<th> NZQA </th>
					<th> Group </th>
					<td> <a href="system_lesson.php?id=0" class="button new">New</a>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $lessons as $lesson )
					{
						echo '<tr>';
						echo '	<td style="background-color:'.$lesson->colour.'">'.$lesson->category_short.'</td>';
						echo '	<td style="background-color:'.$lesson->colour.'">'.$lesson->code.'</td>';
						echo '	<td>'.($lesson->nzqa_qualifies?'Y':'').'</td>';
						echo '	<td> '. $translations['training_level'][$lesson->level];
						/*
						echo (($lesson->level & ATC_LESSON_LEVEL_ADVANCED)?'Advanced ':'');
						echo (($lesson->level & ATC_LESSON_LEVEL_PROFICIENT)?'Proficient ':'');
						echo (($lesson->level & ATC_LESSON_LEVEL_BASIC)?'Basic ':'');
						*/
						echo '	</td>';
						if( $ATC->user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
							echo '	<td> <a href="system_lesson.php?id='.$lesson->lesson_id.'" class="button edit">Edit</a> </td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		
		<script>
			$('a.edit, a.button.new, #activitylist a.button.attendance').click(function(e){
			e.preventDefault(); // stop the link actually firing
			var href = $(this).attr("href");
			$('#dialog').empty().load(href).dialog({
				modal: true,
				width: 600,
				title: 'Edit lesson details',
				buttons: {
					Cancel: function() {
						$( this ).dialog( "close" );
					}<?php if( $ATC->user_has_permission(ATC_PERMISSION_SYSTEM_EDIT) ) { ?> ,
					Save: function() {
						
						$.ajax({
						   type: "POST",
						   url: href,
						   data: $("#dialog form").serialize(),
						   beforeSend: function()
						   {
							   $('#dialog form').addClass('ui-state-disabled');
						   },
						   complete: function()
						   {
							   $('#dialog form').removeClass('ui-state-disabled');
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
									},
									open: function() {
										$('.ui-dialog-titlebar').addClass('ui-state-error');
									}
								}).filter('ui-dialog-titlebar');
								return false;
							}
						});
					} <?php } ?>
				},
				close: function() { 
					$( this ).dialog( "destroy" ); 
				}
			});
			return false;
		});
	</script>
<?php
	} else {
		$lesson = $ATC->get_lesson($_GET['id']);
		$categories = $ATC->get_lesson_category();
		if( count($lesson) )
			$lesson = $lesson[0];
		else {
			$lesson = new stdClass();
			$lesson->lesson_id = 0;
			$lesson->lesson_category_id = 0;
			$lesson->code = null;
			$lesson->nzqa_qualifies = 0;
			$lesson->level = 0;
			$lesson->description = null;
		}
?>
		<form method="POST">
			<label for="lesson_category_id">Lesson category</label><br />
			<select name="lesson_category_id" id="lesson_category_id">
				<?php
					foreach( $categories as $category )
					{
						echo '<option value="'.$category->lesson_category_id.'"'.($category->lesson_category_id==$lesson->lesson_category_id?' selected="selected"':'').'>';
						echo $category->category_short.' &ndash; '.$category->category;
						echo '</option>';
					}
				?>
			</select><br />
			<label for="code">Lesson Code</label><br />
			<input type="text" maxlength="10" id="code" name="code" value="<?= htmlentities($lesson->code) ?>" /><br />
			<label for="description">Description</label><br />
			<input type="text" name="description" id="description" maxlength="255" value="<?= htmlentities($lesson->description) ?>" /><br />
			<label for="nzqa_qualifies">NZQA qualifies</label><br />
			<input type="checkbox" name="nzqa_qualifies" id="nzqa_qualifies" value="1" <?= ($lesson->nzqa_qualifies?' checked="checked"':'') ?> /><br />
			<label for="level">Level</label><br />
			<select name="level" id="level">
<?php
				foreach(  $translations['training_level'] as $key => $value )
					echo '<option value="'.$key.'"'.(($lesson->level == $key)?' selected="selected"':'').'>'.$value.'</option>';
?>
<!--
				<option value="<?= ATC_LESSON_LEVEL_ADVANCED ?>"<?= (($lesson->level == ATC_LESSON_LEVEL_ADVANCED)?' selected="selected"':'') ?>>Advanced</option>
				<option value="<?= ATC_LESSON_LEVEL_PROFICIENT ?>"<?= (($lesson->level == ATC_LESSON_LEVEL_PROFICIENT)?' selected="selected"':'') ?>>Proficient</option>
				<option value="<?= ATC_LESSON_LEVEL_BASIC ?>"<?= (($lesson->level == ATC_LESSON_LEVEL_BASIC)?' selected="selected"':'') ?>>Basic</option>
-->
			</select><br />
			<label for='dress_code'>Dress code</label><br />
			<select name='dress_code' id='dress_code'>
				<option value='<?=ATC_DRESS_CODE_BLUES?>'<?=($lesson->dress_code==ATC_DRESS_CODE_BLUES?' selected="selected"':'')?>><?=htmlentities(ATC_DRESS_CODE_BLUES_NAME)?></option>
				<option value='<?=ATC_DRESS_CODE_DPM?>'<?=($lesson->dress_code==ATC_DRESS_CODE_DPM?' selected="selected"':'')?>><?=htmlentities(ATC_DRESS_CODE_DPM_NAME)?></option>
				<option value='<?=ATC_DRESS_CODE_BLUES_AND_DPM?>'<?=($lesson->dress_code==ATC_DRESS_CODE_BLUES_AND_DPM?' selected="selected"':'')?>><?=htmlentities(ATC_DRESS_CODE_BLUES_AND_DPM_NAME)?></option>
				<option value='<?=ATC_DRESS_CODE_MUFTI?>'<?=($lesson->dress_code==ATC_DRESS_CODE_MUFTI?' selected="selected"':'')?>><?=htmlentities(ATC_DRESS_CODE_MUFTI_NAME)?></option>
			</select><br />
		</form>
<?php
	}
	
	$ATC->gui_output_page_footer('');
?>
