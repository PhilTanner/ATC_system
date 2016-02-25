<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	if( $ATC->logout() )
		header('Location: ./', true, 302);
	$ATC->gui_output_page_header('Logout');
	
?>
<h1> Goodbye </h1>
<p> You have been logged out </h1>
	
<?php
	$ATC->gui_output_page_footer('Logout');
?>