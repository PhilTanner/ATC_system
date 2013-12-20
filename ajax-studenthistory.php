<?php
	require_once './config.php';
	//$PKBASC = new PKBASC();
	/*
	$lastinvoice = $PKBASC->getLastInvoiceForStudent( (int)$_GET['student_id'] );
	if( $lastinvoice && isset($lastinvoice->end_date) ) 
		$lastinvoiceddate = strtotime( $lastinvoice->end_date );
	else 	$lastinvoiceddate = 0;
	*/
	$history = $PKBASC->getBalanceForStudent( 
		(int)$_GET['student_id'], 
		date('c', strtotime('1970-01-01')), 
		date('c'), 
		PKB_INVOICE_BOTH // We only want to include charges that have been invoiced
	);
?>
<h1> History for Student ID #<?=(int)$_GET['student_id']?> &mdash; <?= date('D j M y',strtotime($history['CorrectAsOf']))?> </h1>
<table>
	<caption>Attendances (<?=(count($history['Charges'])-1)?>)</caption>
	<thead class="ui-widget-header">
		<tr>
			<th>#</th>
			<th>Ref.</th>
			<th>Date</th>
			<th>Arrived</th>
			<th>Left</th>
			<th>Duration</th>
			<th>Hourly Charge</th>
			<th>Amount Charged</th>
			<th>Invoice</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$n = 1;
			foreach( $history['Charges'] as $charge )
				if( $charge->charge )
				{
					echo '		<tr>'."\n";
					echo '			<th>'.$n++.'</th>'."\n";
					echo '			<td>'.$charge->attendance_id.'</td>'."\n";
					echo '			<td>'.date('D j M y', strtotime($charge->date)).'</td>'."\n";
					echo '			<td>'.substr($charge->arrived,0,5).'</td>'."\n";
					echo '			<td>'.substr($charge->left,0,5).'</td>'."\n";
					echo '			<td>'.$charge->duration.'</td>'."\n";
					echo '			<td>'.currency_format(MONEYFORMAT,$charge->hourly_charge).'</td>'."\n";
					echo '			<td>'.currency_format(MONEYFORMAT,$charge->charge).'</td>'."\n";
					echo '			<td>'.$charge->invoiced_id.'</td>'."\n";
					echo '		</tr>'."\n";
				}
		?>
	</body>
</table>

<table>
	<caption>Payments (<?=(count($history['Payments'])-1)?>)</caption>
	<thead class="ui-widget-header">
		<tr>
			<th>#</th>
			<th>Int. Ref.</th>
			<th>Ref.</th>
			<th>Date</th>
			<th>Amount</th>
			<th>Invoice</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4"> Total amount paid:</th>
				<th><?=currency_format(MONEYFORMAT,$history['AmountPaid'])?></th>
			<th></th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			$n = 1;
			foreach( $history['Payments'] as $payment )
				if( $payment->amount )
				{
					echo '		<tr>'."\n";
					echo '			<th>'.$n++.'</th>'."\n";
					echo '			<td>'.$payment->payment_id.'</td>'."\n";
					echo '			<td>'.$payment->reference.'</td>'."\n";
					echo '			<td>'.date('D j M y', strtotime($payment->date_received)).'</td>'."\n";
					echo '			<td>'.currency_format(MONEYFORMAT,$payment->amount).'</td>'."\n";
					echo '			<td>'.$payment->invoiced_id.'</td>'."\n";
					echo '		</tr>'."\n";
				}
		?>
	</body>
</table>
<?php
	if( (count($history['WINSPayments'])-1) )
	{
?>
<table>
	<caption title="Overpayments: <?=currency_format(MONEYFORMAT,$history['WINSOverpayments'])?>">WINZ Payments (<?=(count($history['WINSPayments'])-1)?>)</caption>
	<thead class="ui-widget-header">
		<tr>
			<th>#</th>
			<th>Int. Ref.</th>
			<th>Ref.</th>
			<th>Received</th>
			<th>Amount</th>
			<th>Covering</th>
			<th>Invoice</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4">Amount of WINZ credit used:</th>
			<th><?=currency_format(MONEYFORMAT,$history['WINSDiscount'])?></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			$n = 1;
			foreach( $history['WINSPayments'] as $payment )
				if( $payment->amount )
				{
					echo '		<tr>'."\n";
					echo '			<th>'.$n++.'</th>'."\n";
					echo '			<td>'.$payment->payment_id.'</td>'."\n";
					echo '			<td>'.$payment->reference.'</td>'."\n";
					echo '			<td>'.date('D j M y', strtotime($payment->date_received)).'</td>'."\n";
					echo '			<td>'.currency_format(MONEYFORMAT,$payment->amount).'</td>'."\n";
					echo '			<td>'.date('D j M', strtotime($payment->period_start)).'&mdash;'.date('D j M y', strtotime($payment->period_end)).'</td>'."\n";
					echo '			<td>'.$payment->invoiced_id.'</td>'."\n";
					echo '		</tr>'."\n";
				}
		?>
	</body>
</table>
<?php	} ?>
