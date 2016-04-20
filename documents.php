<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	if( isset($_GET['document']) )
	{
		require_once 'documents_'.strtolower($_GET['document']).'.php';
		$ATC->gui_output_page_footer('Documents');
		exit();
	}
	$ATC->gui_output_page_header('Documents');
	
?>
	<h2> What documentation do you want to produce?</h2>
	<ul>
		<li> <a href="?document=NZCF16">NZCF16</a> - Unit personnel record </li>
		<li> <a href="?document=NZCF20">NZCF20</a> - Unit monthly returns </li>
	</ul>
		
<?php
	$ATC->gui_output_page_footer('Documents');
?>