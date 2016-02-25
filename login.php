<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if( isset( $_POST['username'] ) && isset( $_POST['password'] ) )
		{
			try {
				if( $ATC->login( $_POST['username'], $_POST['password'] ) )
					header('Location: ./', true, 302);
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
	}
	if( $ATC->current_user_id() )
		header('Location: ./', true, 302);
	$ATC->gui_output_page_header('Login');
	
?>
<form method="post">
	<fieldset>
		<legend> Login for access </legend>
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?=(isset($_POST['username'])?htmlentities($_POST['username']):'')?>" required="required" maxlength="255" /><br />
		<label for="password">Password</label>
		<input type="password" name="password" id="password" required="required" /><br />
		<button type="submit" id="login">Login</button>
	</fieldset>
</form>

<script>
	$('#login').button({ icons: { primary: 'ui-icon-locked' }});
</script>
	
<?php
	$ATC->gui_output_page_footer('Login');
?>