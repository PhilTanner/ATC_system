<?php
	require_once 'config.php';

	// Select the MOnday following the passed date (or today by default)
	$date = (isset($_GET['date'])&&strtotime(str_replace(',','',$_GET['date']))?strtotime(str_replace(',','',$_GET['date'])):strtotime('next Monday'));
	if( date('D', $date) != 'Mon' ) 
		$date = strtotime('next Monday', $date);
?>

<div id="datepicker"></div>

<?php
	$students = studentBookings( date('c', $date) );
	
	$mon = $tue = $wed = $thu = $fri = $monX = $tueX = $wedX = $thuX = $friX = array();
	foreach( $students[0] as $val ) 
		if( $val[0]==BOOKED || $val[0]==REQUEST ) 
			$mon[] = ($val[0]==BOOKED?'B':'R').' - '.$val[1];
		else
			$monX[] = 'A - '.$val[1];
	foreach( $students[1] as $val )
		if( $val[0]==BOOKED || $val[0]==REQUEST ) 
			$tue[] = ($val[0]==BOOKED?'B':'R').' - '.$val[1];
		else
			$tueX[] = 'A - '.$val[1];
	foreach( $students[2] as $val )
		if( $val[0]==BOOKED || $val[0]==REQUEST ) 
			$wed[] = ($val[0]==BOOKED?'B':'R').' - '.$val[1];
		else
			$wedX[] = 'A - '.$val[1];
	foreach( $students[3] as $val )
		if( $val[0]==BOOKED || $val[0]==REQUEST ) 
			$thu[] = ($val[0]==BOOKED?'B':'R').' - '.$val[1];
		else
			$thuX[] = 'A - '.$val[1];
	foreach( $students[4] as $val )
		if( $val[0]==BOOKED || $val[0]==REQUEST ) 
			$fri[] = ($val[0]==BOOKED?'B':'R').' - '.$val[1];
		else
			$friX[] = 'A - '.$val[1];
?>
<button id="download">Download</button>
<div class="attendance" style="float:left; margin-left : 2em;">
	<strong> Week commencing <?= date('d M, Y', $date )?>: </strong>
	<br /><br />
	<button class="dailyattendance" data-people=<?= json_encode(implode("\n", $mon)."\n\n".implode("\n", $monX))?> data-count="<?= count($mon) ?>">Monday</button><br />
	<button class="dailyattendance" data-people=<?= json_encode(implode("\n", $tue)."\n\n".implode("\n", $tueX))?> data-count="<?= count($tue) ?>">Tuesday</button><br />
	<button class="dailyattendance" data-people=<?= json_encode(implode("\n", $wed)."\n\n".implode("\n", $wedX))?> data-count="<?= count($wed) ?>">Wednesday</button><br />
	<button class="dailyattendance" data-people=<?= json_encode(implode("\n", $thu)."\n\n".implode("\n", $thuX))?> data-count="<?= count($thu) ?>">Thursday</button><br />
	<button class="dailyattendance" data-people=<?= json_encode(implode("\n", $fri)."\n\n".implode("\n", $friX))?> data-count="<?= count($fri) ?>">Friday</button><br />
</div>
	
<script type="text/javascript">
	$('.dailyattendance').button({ icons: { primary: "ui-icon-person" } }).click(function(){
		alert($(this).data('people').replace(/\\n/g,'\n'));
	}).css({ width: '12em', textAlign: 'left' });
	$('.dailyattendance[data-count=0]').button( "option", "disabled", true );
	$('.dailyattendance').not('.ui-state-disabled').each(function(){
		$(this).button( "option", "label", $(this).button( "option", "label" )+' ('+$(this).data('count')+')' );
	});
	$('#download').button({ icons: { primary: 'ui-icon-print' }, text:false }).click( function(){
		window.open('downloadattendanceregister.php?date=<?=urlencode(date('c', $date))?>','_blank');
	}).css({ float: 'right' });
	
	$( "#datepicker" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		defaultDate: <?= json_encode(date('j M Y', $date)) ?>,
		beforeShowDay: $.datepicker.noWeekends,
		onSelect:function(dateText, inst) {
				$('#dialog').dialog({ title: 'Bookings' }).html('<p>Please wait... </p>').load('ajax-viewbookings.php?date='+escape(dateText));
			}
	}).css({ float: 'left' });
        
</script>	

