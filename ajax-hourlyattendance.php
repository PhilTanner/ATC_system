<?php
	require_once 'config.php';
	require_once 'pkbasc.class.php';

	// Select the MOnday following the passed date (or today by default)
	$date = (isset($_GET['date'])&&strtotime(str_replace(',','',$_GET['date']))?strtotime(str_replace(',','',$_GET['date'])):strtotime('last Monday')-(7*24*60*60));
	if( date('D', $date) != 'Mon' ) 
		$date = strtotime('last Monday', $date);
?>

<div id="datepicker"></div>

<?php
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$query = "
SELECT	`date`,
	arrived,
	`left`
FROM 	attendance
WHERE	`date` <= '".date('c', ($date+(4*24*60*60)))."'
	AND `date` >= '".date('c', $date)."'
	AND NOT `left` = '".PKB_TIMEOUT_AUTH_ABS."' 
	AND NOT `left` = '".PKB_TIMEOUT_UNAUTH_ABS."' 
ORDER BY `date`, arrived, `left`";
//var_dump($query);
	$mon = $tue = $wed = $thu = $fri = array(8=>0,9=>0,10=>0,11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,20=>0);
	$minhour = 15;
	$maxhour = 18;
	if ($result = $mysqli->query($query)) 
	{
		while ($obj = $result->fetch_object()) 
		{
			$arrived = strtotime($obj->date.' '.$obj->arrived);
			$left    = strtotime('-1 minute', strtotime($obj->date.' '.$obj->left));
			for( $i=date('G',$arrived); $i<=date('G',$left); $i++)
				@eval('$'.strtolower(date('D',$arrived)).'['.$i.']++;');
			if( date('G',$arrived) <= $minhour ) $minhour = date('G',$arrived);
			if( date('G',$arrived) >= $maxhour ) $maxhour = date('G',$left);
		}
		/* free result set */
		$result->close();
	}

	/* close connection */
	$mysqli->close();	
?>
<div class="attendance" style="float:left; margin-left : 2em;">
	<table>
		<caption>Week commencing <?= date('d M, Y', $date )?>:</caption>
		<thead>
			<tr>
				<th> Time </th>
				<th> Mon </th>
				<th> Tue </th>
				<th> Wed </th>
				<th> Thu </th>
				<th> Fri </th>
				<th> Total </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th> Total </th>
				<th class="total" data-myclass="mon"> </th>
				<th class="total" data-myclass="tue"> </th>
				<th class="total" data-myclass="wed"> </th>
				<th class="total" data-myclass="thu"> </th>
				<th class="total" data-myclass="fri"> </th>
				<th class="total" data-myclass="total"> </th>
			</tr>
		</tfoot>
		<tbody>
			<?php
				for($i=$minhour; $i<=$maxhour; $i++)
				{
					echo '			<tr>'."\n";
					echo '				<th> '.$i.':00-'.$i.':59 </th>'."\n";
					echo '				<td class="hr'.$i.' mon"> '.((int)$mon[$i]).' </td>'."\n";
					echo '				<td class="hr'.$i.' tue"> '.((int)$tue[$i]).' </td>'."\n";
					echo '				<td class="hr'.$i.' wed"> '.((int)$wed[$i]).' </td>'."\n";
					echo '				<td class="hr'.$i.' thu"> '.((int)$thu[$i]).' </td>'."\n";
					echo '				<td class="hr'.$i.' fri"> '.((int)$fri[$i]).' </td>'."\n";
					echo '				<th class="total" data-myclass="hr'.$i.'">  </th>'."\n";
					echo '			</tr>'."\n";
				}
			?>
		</tbody>
	</table>
</div>
	
<script type="text/javascript">
	$('.attendance tbody th.total').each(function(){
		var total = 0;
		if( myclass = $(this).data('myclass') )
		{
			$('.attendance tbody tr .'+myclass).each(function(){ total += new Number($(this).html()); });
			$(this).html(total);
		}
	});
	$('.attendance tfoot th.total').each(function(){
		var total = 0;
		if( myclass = $(this).data('myclass') )
		{
			$('.attendance tbody tr .'+myclass).each(function(){ total += new Number($(this).html()); });
			$(this).html(total);
		}
	});  
	
	$( "#datepicker" ).datepicker({ 
		dateFormat: 'd M yy', 
		changeMonth: true, 
		changeYear: true,  
		showOtherMonths: true, 
		selectOtherMonths: true,
		defaultDate: <?= json_encode(date('j M Y', $date)) ?>,
		beforeShowDay: $.datepicker.noWeekends,
		onSelect:function(dateText, inst) {
				$('#dialog').dialog({ title: 'Hourly Attendance' }).load('ajax-hourlyattendance.php?date='+escape(dateText));
			}
	}).css({ float: 'left' });
</script>	

