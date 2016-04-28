<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id = ( isset($_GET['id'])?(int)$_GET['id']:null );
	if( !isset($_GET['showall']) )
			$_GET['showall'] = 0;
	$user = $ATC->get_personnel($id, 'ASC', null, (int)$_GET['showall']);

	$ATC->gui_output_page_header('Personnel');
	
	if( is_object($user) )
	{	
?>
		<div id="personnelform">
			<h2 href="personal.php?id=<?=$user->personnel_id?>"> Personal details &mdash; <?=$user->rank.' '.$user->display_name?> </h2>
			<div id="personal">
			</div>
	
			<h2 href="nok.php?id=<?=$user->personnel_id?>"> Next of Kin </h2>
			<div id="nok">
			</div>
	
			<h2 href="attendance.php?id=<?=$user->personnel_id?>"> Attendance </h2>
			<div id="attendance">
			</div>
			
			<h2 href="personal.php?id=<?=$user->personnel_id?>&amp;action=promotion"> Promotions </h2>
			<div id="promotion">
			</div>
			
			<h2 href="personal.php?id=<?=$user->personnel_id?>&amp;action=finance"> Finance </h2>
			<div id="finance">
			</div>
	<!--
			<h2> Activites </h2>
			<div id="activities">
			</div>
	
	
			<h2> Stores </h2>
			<div id="stores">
			</div>

			<h2> Training </h2>
			<div id="training">
			</div>
			
			
			<h2> Current browser sessions </h2>
			<div id="browsersessions">
			</div>
			-->
		</div>
		
		<script>
			$(function(){					
				$('#personnelform div:first').load( $('#personnelform h2:first').attr('href'), function(){
					$('#personnelform').accordion({ 
						header: 'h2', 
						changestart:function( event, ui )
						{
							// There's a delay after load to set height, to allow the DOM to update properly
							ui.newContent.load(ui.newHeader.attr('href'), function(){ setTimeout( function(){ $('#personnelform').accordion('resize'); },500); });
						} 
					});
				});
			});
			
		</script>

<?php
	} elseif( is_array( $user ) ) {
?>
	<table class="tablesorter" style="float:left; margin-right:1em;">
		<thead>
			<tr>
				<th> Flight </th>
				<th> Rank </th>
				<th> Name </th>
				<th> Contact N&ordm; </th>
				<th> Access rights </th>
				<td> <?= ( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT )?'<a href="?id=0" class="button new"> New </a>':'')?> </td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="5"> <form><label for="showall">Show all personnel?</label><input type="checkbox" name="showall" id="showall" value="1" <?=($_GET['showall']?' checked="checked"':'')?> onchange="$(this).parent().submit();" /></form></th>
				
			</tr>
		</tfoot>
		<tbody>
			<?php
				$contactdetails = array();
				$contactdetails["cadets"] = array();
				$contactdetails["jncos"] = array();
				$contactdetails["sncos"] = array();
				$contactdetails["officers"] = array();
				$contactdetails["cadetsnok"] = array();
				$contactdetails["jncosnok"] = array();
				$contactdetails["sncosnok"] = array();
				$contactdetails["officersnok"] = array();
				$contactdetails["usc"] = array();
				$contactdetails["others"] = array();
				
				$smscontactdetails = array();
				$smscontactdetails["cadets"] = array();
				$smscontactdetails["jncos"] = array();
				$smscontactdetails["sncos"] = array();
				$smscontactdetails["officers"] = array();
				$smscontactdetails["cadetsnok"] = array();
				$smscontactdetails["jncosnok"] = array();
				$smscontactdetails["sncosnok"] = array();
				$smscontactdetails["officersnok"] = array();
				$smscontactdetails["usc"] = array();
				$smscontactdetails["others"] = array();
				
				foreach( $user as $obj )
				{
					if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $obj->personnel_id ) )
					{
						echo '<tr'.($obj->enabled?'':' class="ui-state-disabled"').'>';
						//echo '	<th>'.$obj->personnel_id.'</th>';
						echo '	<td>'.$obj->flight.'</td>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td><a href="?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						echo '	<td>'.$obj->mobile_phone.'</td>';
						if( isset( $translations['userlevel'][$obj->access_rights] ) )
							echo '	<td>'.$translations['userlevel'][$obj->access_rights].'</td>';
						else
							echo '	<td class="ui-state-error"><strong>Unknown</strong></td>';
						if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $obj->personnel_id ) )
							echo '	<td> <a href="?id='.$obj->personnel_id.'" class="button edit">Edit</a> </td>';
						echo '</tr>';
						
						// store some contact details
						$obj->nok = $ATC->get_nok($obj->personnel_id);
						if( in_array($obj->access_rights, explode(",",ATC_USER_GROUP_CADETS) ) )
						{
							$contactdetails["cadets"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["cadets"][] = $obj->mobile_phone;
							foreach($obj->nok as $nok)
							{
								$contactdetails["cadetsnok"][] = '"'.$nok->firstname.' '.$nok->lastname.'" <'.$nok->email.'>';
								if(strlen(trim($nok->mobile_number)) > 3) 
									$smscontactdetails["cadetsnok"][] = $nok->mobile_number;
							}
						}
						if( in_array($obj->access_rights, explode(",",ATC_USER_GROUP_OFFICERS) ) )
						{
							$contactdetails["officers"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["officers"][] = $obj->mobile_phone;
							foreach($obj->nok as $nok)
							{
								$contactdetails["officersnok"][] = '"'.$nok->firstname.' '.$nok->lastname.'" <'.$nok->email.'>';
							}
						}
						if( $obj->access_rights == ATC_USER_LEVEL_JNCO )
						{
							$contactdetails["jncos"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["jncos"][] = $obj->mobile_phone;
							foreach($obj->nok as $nok)
							{
								$contactdetails["jncosnok"][] = '"'.$nok->firstname.' '.$nok->lastname.'" <'.$nok->email.'>';
							}
						}
						if( $obj->access_rights == ATC_USER_LEVEL_SNCO )
						{
							$contactdetails["sncos"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["sncos"][] = $obj->mobile_phone;
							foreach($obj->nok as $nok)
							{
								$contactdetails["sncosnok"][] = '"'.$nok->firstname.' '.$nok->lastname.'" <'.$nok->email.'>';
							}
						}
						if( in_array($obj->access_rights, array(ATC_USER_LEVEL_TREASURER, ATC_USER_LEVEL_USC) ) )
						{
							$contactdetails["usc"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["usc"][] = $obj->mobile_phone;
						}
						if( in_array($obj->access_rights, array(ATC_USER_LEVEL_EMRG_CONTACT, ATC_USER_LEVEL_ADMIN) ) )
						{
							$contactdetails["others"][] = '"'.$obj->rank.' '.$obj->display_name.'" <'.$obj->email.'>';
							if(strlen(trim($obj->mobile_phone)) > 3) 
								$smscontactdetails["others"][] = $obj->mobile_phone;
						}
					}
				}
			?>
		</tbody>
	</table>
	<?php
		if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW ) )
		{
	?>
	<fieldset id="emaillist">
		<legend> Send bulk email </legend>
		<label for="cadets">Cadets</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["cadets"])) )?>" id="cadets" checked="checked" /><br />
		<label for="jncos">JNCOs</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["jncos"])) )?>" id="jncos" checked="checked" /><br />
		<label for="sncos">SNCOs</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["sncos"])) )?>" id="sncos" checked="checked" /><br />
		<label for="officers">Officers</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["officers"])) )?>" id="officers" checked="checked"  /><br />
		<hr />		
		<label for="cadetsnok">Cadets Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["cadetsnok"])) )?>" id="cadetsnok" checked="checked" /><br />
		<label for="jncosnok">JNCOs Next of  Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["jncosnok"])) )?>" id="jncosnok" checked="checked" /><br />
		<label for="sncosnok">SNCOs Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["sncosnok"])) )?>" id="sncosnok" checked="checked" /><br />
		<label for="officersnok">Officers Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["officersnok"])) )?>" id="officersnok" checked="checked"  /><br />
		<hr />
		<label for="usc">USC</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["usc"])) )?>" id="usc" /><br />
		<label for="others">Others</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($contactdetails["others"])) )?>" id="others" /><br />
		
		<a class="button email">Create email</a>
	</fieldset>
	
	<fieldset id="smslist" style="margin-top:1em;">
		<legend> Send bulk SMS </legend>
		<label for="cadets">Cadets</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["cadets"])) )?>" id="cadets" checked="checked" /><br />
		<label for="jncos">JNCOs</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["jncos"])) )?>" id="jncos" checked="checked" /><br />
		<label for="sncos">SNCOs</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["sncos"])) )?>" id="sncos" checked="checked" /><br />
		<label for="officers">Officers</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["officers"])) )?>" id="officers" checked="checked"  /><br />
		<hr />		
		<label for="cadetsnok">Cadets Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["cadetsnok"])) )?>" id="cadetsnok" checked="checked" /><br />
		<label for="jncosnok">JNCOs Next of  Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["jncosnok"])) )?>" id="jncosnok" checked="checked" /><br />
		<label for="sncosnok">SNCOs Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["sncosnok"])) )?>" id="sncosnok" checked="checked" /><br />
		<label for="officersnok">Officers Next of Kin</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["officersnok"])) )?>" id="officersnok" checked="checked"  /><br />
		<hr />
		<label for="usc">USC</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["usc"])) )?>" id="usc" /><br />
		<label for="others">Others</label><input type="checkbox" value="<?= htmlentities( implode("; ", array_unique($smscontactdetails["others"])) )?>" id="others" /><br />
		
		<a class="button sms">Create SMS</a>
	</fieldset>
	<?php
		}
	?>
	<script>
		//$("thead th").button({ icons: { primary: "ui-icon-arrowthick-2-n-s" } }).removeClass("ui-corner-all").css({ display: "table-cell" });
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
		$('td.ui-state-highlight').removeClass('ui-state-highlight').parent().addClass('ui-state-highlight');
		
		$('a.button.email').button({ icons: { primary: 'ui-icon-mail-closed' } }).click(function(){
			// Collate our list of addresses based off which tick boxes are selected
			var addresses = [];
			$.each($('#emaillist input:checked'), function( index, value ){ 
				var foo = $(this).val().split(';');
				for(var i=0;i<foo.length;i++) addresses.push(foo[i]);
			});
			// Dedupe our list in case someone appears more than once (i.e. NOK for cadet, NOK for SNCO & USC member)
			$(this).attr('href', "mailto:?bcc="+encodeURI( array_unique( addresses ).join(';') ));
		});
		$('a.button.sms').button({ icons: { primary: 'ui-icon-battery-2' } }).click(function(){
			// Collate our list of addresses based off which tick boxes are selected
			var addresses = [];
			$.each($('#smslist input:checked'), function( index, value ){ 
				var foo = $(this).val().split(';');
				for(var i=0;i<foo.length;i++) addresses.push(foo[i]);
			});
			// Dedupe our list in case someone appears more than once (i.e. NOK for cadet, NOK for SNCO & USC member)
			$(this).attr('href', "sms://"+encodeURI( array_unique( addresses ).join(';') ));
		});
		
		function array_unique( array )
		{
			var result = [];
			$.each( array, function(i, e){ if( $.inArray(e, result) == -1 ) result.push(e); });
			return result;
		}
	</script>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>