<?php
	require_once "atc_training.class.php";
	$ATC = new ATC_Training();
	
	$ATC->gui_output_page_header('Training');
	
	//$ATC->dump_userperms();
?>
	
		
<?php
	$ATC->gui_output_page_footer('Training');
?>