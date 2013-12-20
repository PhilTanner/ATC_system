<!doctype html>
<html lang="us">
	<head>
		<meta charset="utf-8">
		<title>PKBASC</title>
		<link href="jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" rel="stylesheet">
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-1.8.3.js"></script>
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
		<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
		
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		
		<script type="text/javascript">
			$(function(){
				$('button.attendance').button({ icons: { primary: "ui-icon-clipboard" } }).click(function(){
					$('#dialog').dialog({ title: 'Time out sheet' }).load('ajax-recordtimeoutsheet.php');
				});
				$('button.backup').button({ icons: { primary: "ui-icon-disk" } }).click(function(){
					$('#dialog').dialog({ title: 'Backup system' }).html('<p>Please wait... Backing up</p>').load('ajax-systembackup.php');
				});
				$('button.restore').button({ icons: { primary: "ui-icon-arrowrefresh-1-s" } }).click(function(){
					$('#dialog').dialog({ title: 'Restore system' }).load('ajax-systemrestore.php');
				});
				$('button.bookings').button({ icons: { primary: "ui-icon-calendar" } }).click(function(){
					$('#dialog').dialog({ title: 'Bookings' }).load('ajax-viewbookings.php');
				});
				$('button.hourlyattendance').button({ icons: { primary: "ui-icon-clock" } }).click(function(){
					$('#dialog').dialog({ title: 'Hourly Attendance' }).load('ajax-hourlyattendance.php');
				});
				$('#showhidden').change( function(){ 
					if( $('#showhidden:checked').length )
						$('#studentlist tbody tr[data-display="0"]').show();
					else
						$('#studentlist tbody tr[data-display="0"]').hide();
					
					$('#studentlist tbody tr').children('td, th').css({ backgroundColor: 'White' });
					$('#studentlist tbody tr:visible:odd').children('td, th').css({ backgroundColor: <?= (strpos($_SERVER['SCRIPT_NAME'], 'tmp') !== false?"'#ffa0a0'":"'#e0e0ff'")?> });
				});
				
				refreshstudentlist();
				
				$('#dialog').dialog({ 
					modal:true, 
					width: 730,
					close: 	function(){
							$('#ui-datepicker-div').hide();
							$(this).html('');
							//refreshstudentlist();
						},
					open:	function(){
							$(this).html('<p>Please wait... </p>');
						},
					buttons: { 'Close': function(){ $(this).dialog('close'); } }
				}).dialog('close');
			});
			
			function refreshstudentlist()
			{
				$('#studenttable').addClass('ui-state-disabled').load('ajax-studentlist.php',function(){ $('#studenttable').removeClass('ui-state-disabled'); $('#showhidden').change(); });
			}
			
			function newstudentform()
			{
				$('#dialog').dialog({ title: 'Add new student' }).load('ajax-studentdetailsform.php?student_id=0');
			}
		</script>
		
		<style type="text/css">
			body
			{
				font-family: 'Lucida Sans', 'Lucida Sans Unicode', Verdana, Arial, sans-serif;
				font-size:80%;
				background: url("logo.png") no-repeat fixed right bottom White
			}
			
			td.currency { text-align: right; }
			table { border-left: 1px solid silver; border-spacing:0; border-collapse:collapse; }
			tr th, tbody tr td { border: 1px solid silver; border-left:0px; }
			th, td { padding: 0.25ex 1ex; }
			thead th { padding: 1ex 1ex; }

			.ui-widget { font-size: 90%; }
			.ui-button-text { margin-left : 0.75ex; }
			.picktime, .hourlyrate { width: 4em; }
			.pickdate { width: 8em; }
			.navoptions { float: right; }
			.navoptions button { margin-bottom: 5px; }
			.winspayments { font-size:66%; color:Red; font-weight:bold; }
		</style>

	</head>
	<body>
		<div class="navoptions">
			<button class="attendance" type="button">Time Out Sheet</button><br />
			<button class="bookings" type="button">View bookings</button><br />
			<button class="hourlyattendance" type="button">Hourly attendance</button><br />
			<hr />
			<button class="backup" type="button">Backup database</button><br />
			<button class="restore" type="button">Restore database</button><br />
			<hr />
			<input type="checkbox" id="showhidden" value="1" /> <label for="showhidden">Show all students?</label>
		</div>
		<h1> Pukerua Bay After School Care </h1>
		<div id="studenttable">
			<table id="studentlist">
				<thead class="ui-widget-header">
					<tr>
						<th>#</th>
						<th>Name</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th> <button class="add" id="newstudentbutton">Add student</button>
					</tr>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="dialog"></div>
	</body>
</html>
<?php
	// Backup the database at the start of each session
	ob_start();
	$_GET['automatic'] = true;
	include 'ajax-systembackup.php';
	ob_end_clean();  
?>
