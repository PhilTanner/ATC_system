<?php
	require_once 'config.php';
	
	if( !isset($_GET['entry']) )
	{
		
		if ($handle = opendir('./backups/')) 
		{
			echo '<h2> Choose a backup to restore: </h2>'."\n";
			$files = array();
			while (false !== ($entry = readdir($handle))) 
				if( substr($entry,0,6) == "pkbasc" ) 
					$files[] = $entry;
			closedir($handle);
	
			arsort($files);
			$n = 0;
			foreach($files as $entry)
			{
				$n++;
				echo '<button class="restore" type="button" data-entry="'.$entry.'">'.date('d M, Y H:i:s', substr($entry, 7, 10) )."</button><br />\n";
				if($n > 12) break;
			}
	
			echo '<script type="text/javascript">'."\n";
			echo '	$("#dialog button.restore").button({ icons: { primary: "ui-icon-arrowrefresh-1-s" } }).click(function(){'."\n";
			echo '		if( confirm( "Are you sure you want to restore the system to this time?\n\nThere is no undo function!") )'."\n";
			echo '			$("#dialog").dialog({ title: "Restore system" }).html("<p>Please wait... </p>").load("ajax-systemrestore.php?entry="+$(this).data("entry"));'."\n";
			echo '	});'."\n";
			echo '</script>'."\n";
		}
	} else {
		ob_start();
		$_GET['automatic'] = true;
		include 'ajax-systembackup.php';
		ob_end_clean();  
		$cmd = CMD_RESTORE.$_GET['entry'];
		exec($cmd);
		echo '<h2> Database restored </h2>'."\n";
		echo '<script type="text/javascript">'."\n";
		echo '	refreshstudentlist();'."\n";
		echo '</script>'."\n";
	}
?>

