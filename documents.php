<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$ATC->gui_output_page_header('Documents');
	
	$nzcf20 = $ATC->nzcf20_stats( '2016', '02' );
	var_dump($nzcf20);
	
		
?>
	<h1> NZCF20 </h1>
	
	<div style="width:40%; float: left;">
		<table>
			<thead>
				<tr>
					<th colspan="6"> 1. Enrolled Cadet Strength at Month's End </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th> Totals </th>
					<td align="center"> <?= $nzcf20[0][6][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][6][0]; ?></td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][6][1]+$nzcf20[0][6][0]; ?></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td> WO / WOII / W/O </td>
					<td align="center"> <?= $nzcf20[0][0][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][0][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][0][1]+$nzcf20[0][0][0]; ?> </td>
				</tr>
				<tr>
					<td> CPO / SSGT / F/S </td>
					<td align="center"> <?= $nzcf20[0][1][1]; ?></td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][1][0]; ?></td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][1][1]+$nzcf20[0][1][0]; ?> </td>
				</tr>
				<tr>
					<td> PO / SGT </td>
					<td align="center"> <?= $nzcf20[0][2][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][2][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][2][1]+$nzcf20[0][2][0]; ?> </td>
				</tr>
				<tr>
					<td> LCDT / CPL </td>
					<td align="center"> <?= $nzcf20[0][3][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][3][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][3][1]+$nzcf20[0][3][0]; ?> </td>
				</tr>
				<tr>
					<td> ABCDT / LCPL / LACDT </td>
					<td align="center"> <?= $nzcf20[0][4][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][4][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][4][1]+$nzcf20[0][4][0]; ?> </td>
				</tr>
				<tr>
					<td> CDTs </td>
					<td align="center"> <?= $nzcf20[0][5][1]; ?> </td>
					<td align="center"> + </td>
					<td align="center"> <?= $nzcf20[0][5][0]; ?> </td>
					<td align="center"> = </td>
					<td align="center"> <?= $nzcf20[0][5][1]+$nzcf20[0][5][0]; ?>  </td>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="5"> 2. Cadet Attendance (Week night parades) </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> Week 1 </td>
					<td> Week 2 </th>
					<td> Week 3 </td>
					<td> Week 4 </td>
					<td> Week 5 </td>
				</tr>
				<tr>
					<td> <?= (isset($nzcf20[1][0]->count)?$nzcf20[1][0]->count:'')?> </td>
					<td> <?= (isset($nzcf20[1][1]->count)?$nzcf20[1][1]->count:'')?> </td>
					<td> <?= (isset($nzcf20[1][2]->count)?$nzcf20[1][2]->count:'')?>  </td>
					<td> <?= (isset($nzcf20[1][3]->count)?$nzcf20[1][3]->count:'')?>  </td>
					<td> <?= (isset($nzcf20[1][4]->count)?$nzcf20[1][4]->count:'')?>  </td>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="3"> 3. NZCF Officer &amp; Under Officer Attendance<br />Activity Days = Activities Authorised &amp; Recognised </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> Rank / First Name / Surname<br />(Inc those on: Sup List, Leave &amp; Attached) </td>
					<td> Activity Days </th>
					<td> Parade Hours </td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> </td>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th colspan="3"> 4. Supplementary Staff </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> Name &amp; Position </td>
					<td> Days </th>
					<td> Hours </td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> </td>
				</tr>
			</tbody>
		</table>		
		
	</div>
	
	<script>
		$('table').css({ width: '100%', marginBottom: '1em' });
	</script>
		
<?php
	$ATC->gui_output_page_footer('Personnel');
?>