<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$ATC->gui_output_page_header('System');
	
	$ATC->dump_userperms();
?>
	
		
<?php
	$ATC->gui_output_page_footer('Personnel');
?>