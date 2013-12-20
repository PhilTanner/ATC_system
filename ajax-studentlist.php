<?php
	require_once 'config.php';
	require_once './pkbasc.class.php';

	$PKBASC = new PKBASC();
	
?>
		<table id="studentlist">
			<thead class="ui-widget-header">
				<tr>
					<th rowspan="2">#</th>
					<th rowspan="2">Name</th>
					<th colspan="2">Outstanding</th>
					<th rowspan="2">Options</th>
				</tr>
				<tr>
					<th> Invoiced </th>
					<th> Current </th>
					<!--
					<th> Calc </th>
					<th> Old </th>
					-->
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="4"></th>
					<th> <button class="add" id="newstudentbutton">Add student</button>
				</tr>
			</tfoot>
			<tbody>
				<?php
					$n = 1;
					foreach( $PKBASC->getStudentList() as $obj )
					{
						if( isset($obj->student_id) )
						{

							// Opening balances are shown as of the last invoiced date - or never (i.e. we've not invoiced them, they don't know they owe anything)
							$lastinvoice = $PKBASC->getLastInvoiceForStudent( $obj->student_id );
							//echo '<pre>';var_dump($lastinvoice);echo '</pre>';
							if( $lastinvoice && isset($lastinvoice->end_date) ) 
								$lastinvoiceddate = strtotime( $lastinvoice->end_date );
							else 	$lastinvoiceddate = 0;
							$invoiced = $PKBASC->getBalanceForStudent( 
								$obj->student_id, 
								date('c', strtotime('1970-01-01')), 
								date('c', $lastinvoiceddate ), 
								PKB_INVOICE_INCLUDE // Only list charges that they've been invoiced for
							);
							$current = $PKBASC->getBalanceForStudent( 
								$obj->student_id, 
								date('c', 0), 
								date('c'), 
								PKB_INVOICE_BOTH // List all charges, regardless of their invoiced status 
							);
							$calc = ($invoiced['FinalBalance']-$invoiced['AmountPaid'])+$current['AmountPaid'];
							
							echo '				<tr data-display="'.$obj->display.'" data-studentid="'.$obj->student_id.'">'."\n";
							echo '					<th>'.$n++.'</th>'."\n";
							echo '					<td';
							if( isset( $lastinvoice->amount_billed ) && currency_format(MONEYFORMAT,$lastinvoice->amount_billed) != currency_format(MONEYFORMAT,$invoiced['FinalBalance']) )
								echo ' class="ui-state-error" title="Last invoice: '.$lastinvoice->amount_billed.', Balance '.$invoiced['FinalBalance'].'"';
							else
								echo ' title="WINS overpayments: '.currency_format(MONEYFORMAT, 0-$invoiced['WINSOverpayments']).'"';
							echo '>'.$obj->lastname.', '.$obj->firstname.'</td>'."\n";
							echo '					<td class="currency"> <span title="Invoiced '.currency_format(MONEYFORMAT, 0-$invoiced['FinalBalance']).' on '.date('jS M Y', strtotime($invoiced['CorrectAsOf'])).'">';
							echo currency_format(MONEYFORMAT, 0-$calc).'</span>';
							//if( $invoiced['WINSOverpayments'] > 0 )
							//	echo ' / <span class="winspayments">'.currency_format(MONEYFORMAT, 0-$invoiced['WINSOverpayments']).'</span>';
							echo '					</td>'."\n";
							echo '					<td class="currency"> <span class="ui-state-disabled"title="'.date('jS M Y', strtotime($current['CorrectAsOf'])).'">';
							echo currency_format(MONEYFORMAT, 0-$current['FinalBalance']).'</span>';
							//if( $current['WINSOverpayments'] > 0 )
							//	echo ' / <span class="winspayments">'.currency_format(MONEYFORMAT, 0-$current['WINSOverpayments']).'</span>';
							echo '					</td>'."\n";
							
							/*
							echo '					<td class="currency"> <span class="ui-state-disabled" title="'.$invoiced['CorrectAsOf'].'">';
							echo currency_format(MONEYFORMAT, 0-$calc).'</span>';
							//if( $current['WINSOverpayments'] > 0 )
							//	echo ' / <span class="winspayments">'.currency_format(MONEYFORMAT, 0-$current['WINSOverpayments']).'</span>';
							echo '					</td>'."\n";
							echo '					<td class="currency">';
							if( currency_format(MONEYFORMAT, 0-$obj->balance) != currency_format(MONEYFORMAT, 0-$calc) ) echo '<strong style="color:red;">';
							echo currency_format(MONEYFORMAT, 0-$obj->balance);
							//if( $invoiced['WINSOverpayments'] > 0 )
							//	echo ' / <span class="winspayments">'.currency_format(MONEYFORMAT, 0-$invoiced['WINSOverpayments']).'</span>';
							echo '					</strong></td>'."\n";
							*/
							
							echo '					<td>'."\n";
							echo '						<button class="edit" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Edit</button>'."\n";
							echo '						<button class="attendance" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Attendance</button>'."\n";
							echo '						<button class="booking" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Bookings</button>'."\n";
							echo '						<button class="invoice" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Invoice</button>'."\n";
							echo '						<button class="receipt" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Record payment</button>'."\n";
							echo '						<button class="history" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">History</button>'."\n";
							echo '						&nbsp;&nbsp;'."\n";
							echo '						<button class="delete" data-student="'.$obj->student_id.'" data-studentname="'.$obj->firstname.' '.$obj->lastname.'">Delete</button>'."\n";
							echo '					</td>'."\n";
							//echo '<td><pre>'; var_dump($invoiced); echo '</pre></td>';
							echo '				</tr>'."\n";
							
							
							if( $obj->student_id == 32 )
							{
								/*
								$endbalance   = $PKBASC->getBalanceForStudent( 
									$student->student_id, 
									date('c', strtotime('2013-02-18')), 
									date('c', strtotime('2013-03-31')), 
									PKB_INVOICE_EXCLUDE // We only want to include charges that have not yet been invoiced
								);
								*/
								//echo '<script>console.log('.json_encode($invoiced).');'."\n".'console.log('.json_encode($current).');</script>';
							}
							
						}
					}
				?>
			</tbody>
		</table>
		
		<script type="text/javascript">
			$('button.edit').button({ icons: { primary: "ui-icon-pencil" }, text:false }).click(function(){
				$('#dialog').dialog({ title: 'Amend student' }).load('ajax-studentdetailsform.php?student_id='+$(this).data('student'));
			});
			$('#studentlist button.attendance').button({ icons: { primary: "ui-icon-clipboard" }, text:false }).click(function(){
				$('#dialog').dialog({ title: 'Attendance record for '+$(this).data('studentname') }).load('ajax-studentattendance.php?student_id='+$(this).data('student'));
			});
			$('button.booking').button({ icons: { primary: "ui-icon-calendar" }, text:false }).click(function(){
				$('#dialog').dialog({ title: 'Bookings for '+$(this).data('studentname') }).load('ajax-bookings.php?student_id='+$(this).data('student'));
			});
			$('button.invoice').button({ icons: { primary: "ui-icon-script" }, text:false }).click(function(){
				$('#dialog').dialog({ title: 'Invoices for '+$(this).data('studentname') }).load('ajax-invoices.php?student_id='+$(this).data('student'));
			});
			$('button.receipt').button({ icons: { primary: "ui-icon-arrowthickstop-1-s" }, text:false }).click(function(){ 
				$('#dialog').dialog({ title: 'Record payment for '+$(this).data('studentname') }).load('ajax-payments.php?student_id='+$(this).data('student'));
			});
			$('button.history').button({ icons: { primary: "ui-icon-document" }, text:false }).click(function(){ 
				$('#dialog').dialog({ title: 'History for '+$(this).data('studentname') }).load('ajax-studenthistory.php?student_id='+$(this).data('student'));
			});
			$('button.delete').button({ icons: { primary: "ui-icon-trash" }, text:false }).click(function(){ 
				$('#dialog').dialog({ title: 'Delete all records for '+$(this).data('studentname')+'?' }).load('ajax-deletestudent.php?student_id='+$(this).data('student'));
			}).addClass('ui-state-disabled');

			$('#newstudentbutton').button({ icons: { primary: "ui-icon-circle-plus" } }).click(function(){ newstudentform(); });
			
			$('#studentlist tbody tr:odd').children('td, th').css({ backgroundColor: <?= (strpos($_SERVER['SCRIPT_NAME'], 'tmp') !== false?"'#ffa0a0'":"'#e0e0ff'")?> });
			<?= (strpos($_SERVER['SCRIPT_NAME'], 'tmp') !== false?"alert('THIS IS A TEST AREA');":"")?>
			
			<?php
				//if( !$PKBASC::$dbUpToDate )
				//	echo 'alert("WARNING: Database is not the latest version!");'; 
			?>
		</script>
		
		
