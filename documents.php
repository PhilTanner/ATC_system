<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$ATC->gui_output_page_header('Documents');
	
	var_dump( $ATC->nzcf20_stats( '2016', '02' ) );
		
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
					<td> 29 </td>
					<td> + </td>
					<td> 8 </td>
					<td> = </td>
					<td> 37 </td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td> WO / WOII / W/O </td>
					<td> </td>
					<td> + </td>
					<td> 1 </td>
					<td> = </td>
					<td> 1 </td>
				</tr>
				<tr>
					<td> CPO / SSGT / F/S </td>
					<td> 2 </td>
					<td> + </td>
					<td> </td>
					<td> = </td>
					<td> 2 </td>
				</tr>
				<tr>
					<td> PO / SGT </td>
					<td> 1 </td>
					<td> + </td>
					<td> </td>
					<td> = </td>
					<td> 1 </td>
				</tr>
				<tr>
					<td> LCDT / CPL </td>
					<td> 4 </td>
					<td> + </td>
					<td> </td>
					<td> = </td>
					<td> 4 </td>
				</tr>
				<tr>
					<td> ABCDT / LCPL / LACDT </td>
					<td> 6 </td>
					<td> + </td>
					<td> 1 </td>
					<td> = </td>
					<td> 7 </td>
				</tr>
				<tr>
					<td> CDTs </td>
					<td> 16 </td>
					<td> + </td>
					<td> 6 </td>
					<td> = </td>
					<td> 22 </td>
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
					<td> 21 </td>
					<td> 27 </td>
					<td> 32 </td>
					<td> 31 </td>
					<td> </td>
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