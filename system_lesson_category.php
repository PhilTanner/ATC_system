<?php
	require_once "atc_training.class.php";
	$ATC = new ATC_Training();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try {
			$ATC->set_lesson_category( 
				$_GET['id'], 
				$_POST['category'], 
				$_POST['color'], 
				$_POST['textcolor'], 
				$_POST['code'], 
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
		$categories = $ATC->get_lesson_category();
?>
	
		<table class="tablesorter">
			<thead>
				<tr>
					<th> Code </th>
					<th> Category </th>
					<td> <a href="system_lesson_category.php?id=0" class="button new">New</a>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $categories as $category )
					{
						echo '<tr>';
						echo '	<td style="background-color:'.$category->colour.'; color:'.$category->text_colour.'">'.$category->category_short.'</td>';
						echo '	<td style="background-color:'.$category->colour.'; color:'.$category->text_colour.'">'.$category->category.'</td>';
						if( $ATC->user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
							echo '	<td> <a href="system_lesson_category.php?id='.$category->lesson_category_id.'" class="button edit">Edit</a> </td>';
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
				title: 'Edit lesson category details',
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
		$category = $ATC->get_lesson_category($_GET['id']);
		if( count($category) )
			$category = $category[0];
		else {
			$category = new stdClass();
			$category->lesson_category_id = 0;
			$category->category_short = null;
			$category->category = null;
			$category->colour = '#ffffff';
			$category->text_colour = '#000000';
		}
?>
		<form method="POST">
			<label for="code">Short code</label><br />
			<input type="text" maxlength="9" id="code" name="code" value="<?= htmlentities($category->category_short) ?>" /><br />
			<label for="category">Category</label><br />
			<input type="text" name="category" id="category" maxlength="50" value="<?= htmlentities($category->category) ?>" /><br />
			<label for="color">Colour</label><br />
			<input type="color" name="color" id="color" value="<?= strtoupper(htmlentities($category->colour)) ?>" defaultvalue="<?= strtoupper(htmlentities($category->colour)) ?>" style="width:2em;" /><br />
			<label for="textcolor">Text Colour</label><br />
			<input type="color" name="textcolor" id="textcolor" value="<?= strtoupper(htmlentities($category->text_colour)) ?>" defaultvalue="<?= strtoupper(htmlentities($category->text_colour)) ?>" style="width:2em;" /><br />
		</form>
<?php
	}
	
	$ATC->gui_output_page_footer('');
?>