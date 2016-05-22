<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		try {
			if( isset( $_POST['username'] ) && strlen(trim($_POST['username']))  )
			{
				try {
					
					$foo = $ATC->forgot_password_generate($_POST['username']);
					
				} catch(Exception $e) {
					var_dump($e);
				}
				//forgot_password_generate
				//header('Location: ./', true, 302);
				echo 'reset if valid';
			}
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
	
	$ATC->gui_output_page_header('Password Reset');
?>
<form method="post">
	<fieldset>
		<legend> Your details </legend>
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?=(isset($_POST['username'])?htmlentities($_POST['username']):(isset($_GET['username'])?htmlentities($_GET['username']):''))?>" required="required" maxlength="255" /><br />
		
		<button type="submit" id="reset">Email reset code</button>
	</fieldset>
</form>

<script>
	$('#reset').button({ icons: { primary: 'ui-icon-locked' }});
</script>
	
<?php
	$ATC->gui_output_page_footer('Password Reset');
?>