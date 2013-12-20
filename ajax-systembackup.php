<?php
	require_once 'config.php';
	require_once './pkbasc.class.php';

	$PKBASC = new PKBASC();
	
	if( isset($_GET['automatic']) && $_GET['automatic'] )
		$PKBASC->backup(true);
	else
		$PKBASC->backup(false);
	
	echo '<p>Database backed up.</p>';
?>

