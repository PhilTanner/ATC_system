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

			<h2 href="personal.php?id=<?=$user->personnel_id?>&amp;action=activities"> Activites </h2>
			<div id="activities">
			</div>
<!--	
	
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
				<th> Age </th>
				<th> Length of Service </th>
				<th> Activities </th>
				<th> Contact N&ordm; </th>
				<th> Access rights </th>
				<th> Selected </th>
				<td> <?= ( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT )?'<a href="?id=0" class="button new"> New </a>':'')?> </td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="8"> <form><label for="showall">Show all personnel?</label><input type="checkbox" name="showall" id="showall" value="1" <?=($_GET['showall']?' checked="checked"':'')?> onchange="$(this).parent().submit();" /></form></th>
				
			</tr>
		</tfoot>
		<tbody>
			<form id="personnellist">
				<input type="hidden" name="what" id="what" value="" />
				<input type="hidden" name="how" id="how" value=""/> 
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
					$thispersonsnokemail = array();
					$thispersonsnokmobile = array();
					if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $obj->personnel_id ) )
					{ 
						echo '<tr'.($obj->enabled?'':' class="ui-state-disabled"').' data-id="'.$obj->personnel_id.'">';
						//echo '	<th>'.$obj->personnel_id.'</th>';
						echo '	<td>'.$obj->flight.'</td>';
						echo '	<td>'.$obj->rank.'</td>';
						echo '	<td class="name"><a href="?id='.$obj->personnel_id.'">'.$obj->display_name.'</a></td>';
						$now = new DateTime();
						$dob = new DateTime($obj->dob);
						$age = date_diff( $dob, $now );
						$joined = new DateTime($obj->joined_date);
						$service = date_diff( $joined, $now );
						echo '	<td>'.$age->format('%y/%m').'</td>';
						echo '	<td>'.$service->format('%y/%m').'</td>';
						echo '	<td class="activities"> <a href="#">'.$obj->activities.'</a></td>';
						echo '	<td>'.$obj->mobile_phone.'</td>';
						if( isset( $translations['userlevel'][$obj->access_rights] ) )
							echo '	<td>'.$translations['userlevel'][$obj->access_rights].'</td>';
						else
							echo '	<td class="ui-state-error"><strong>Unknown</strong></td>';
							
						echo '	<td style="text-align:center;"> ';
						echo '		<input type="checkbox" id="contact_'.$obj->personnel_id.'" name="id[]" value="'.$obj->personnel_id.'"';
						echo '			data-personnel_id="'.htmlentities($obj->personnel_id).'"';
						if( in_array($obj->access_rights, explode(",",ATC_USER_GROUP_CADETS) ) )
							echo '			data-cadet="true"';
						if( in_array($obj->access_rights, explode(",",ATC_USER_GROUP_OFFICERS) ) )
							echo '			data-officer="true"';
						if( in_array($obj->access_rights, explode(",",ATC_USER_LEVEL_JNCO) ) )
							echo '			data-jnco="true"';
						if( in_array($obj->access_rights, explode(",",ATC_USER_LEVEL_SNCO) ) )
							echo '			data-snco="true"';
						if( in_array($obj->access_rights, array(ATC_USER_LEVEL_TREASURER, ATC_USER_LEVEL_USC) ) )
							echo '			data-usc="true"';
						if( in_array($obj->access_rights, array(ATC_USER_LEVEL_EMRG_CONTACT, ATC_USER_LEVEL_ADMIN) ) )
							echo '			data-other="true"';
						echo '			data-access_rights="'.$obj->access_rights.'" data-enabled="'.($obj->enabled?'true':'false').'" />';
						echo '	</td>';
						
						
						if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $obj->personnel_id ) )
							echo '	<td> <a href="?id='.$obj->personnel_id.'" class="button edit">Edit</a> </td>';
						echo '</tr>';
						

					}
				}
			?>
			</form>
		</tbody>
	</table>
	<?php
		if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW ) )
		{
	?>
	
	<a href="" style="width:1px; height:1px; border:0px;" id="actiontrigger"></a>
	<fieldset id="findtribute">
		<legend> Select Personnel </legend>
		<a class="button cadets">Cadets</a> 
		<a class="button jncos">JNCOs</a>
		<a class="button sncos">SNCOs</a>
		<a class="button officers">Officers</a>
		<hr />
		<a class="button usc">USC</a>
		<a class="button others">Others</a>
	</fieldset>
	<fieldset id="makecontact">
		<legend> Make contact </legend>
		<h3> With selected personnel: </h3>
		<a class="button email">Send email</a>
		<a class="button sms">Send SMS</a>
		<hr />
		<h3> With selected personnel's Next Of Kin: </h3>
		<a class="button email nok">Send email</a>
		<a class="button sms nok">Send SMS</a>
	</fieldset>
		
	<?php
		}
	?>
	<script>
		//$("thead th").button({ icons: { primary: "ui-icon-arrowthick-2-n-s" } }).removeClass("ui-corner-all").css({ display: "table-cell" });
		$('a.button').button();
		$('a.button.edit').button({ icons: { primary: 'ui-icon-pencil' }, text: false });
		$('a.button.new').button({ icons: { primary: 'ui-icon-plusthick' }, text: false });
		// Buttons to communicate with personnel
		$('a.button.cadets').click( function(){
			// Toggle the checkboxes which are checked on & off
			$('input[type="checkbox"]').filter( function(){ return $(this).data('cadet') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		$('a.button.jncos').click( function(){
			$('input[type="checkbox"]').filter( function(){ return $(this).data('jnco') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		$('a.button.sncos').click( function(){
			$('input[type="checkbox"]').filter( function(){ return $(this).data('snco') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		$('a.button.officers').click( function(){
			$('input[type="checkbox"]').filter( function(){ return $(this).data('officer') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		$('a.button.usc').click( function(){
			$('input[type="checkbox"]').filter( function(){ return $(this).data('usc') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		$('a.button.others').click( function(){
			$('input[type="checkbox"]').filter( function(){ return $(this).data('other') == true && $(this).data('enabled') == true; }).prop('checked', function(_, checked) { return !checked; }); 
			return false;
		});
		
		$('td.ui-state-highlight').removeClass('ui-state-highlight').parent().addClass('ui-state-highlight');
		
		$('a.button.email').button({ icons: { primary: 'ui-icon-mail-closed' } });
		$('a.button.sms').button({ icons: { primary: 'ui-icon-battery-2' } })
		$('a.button.email, a.button.sms').click(function(){
			
			$('#what').val($(this).hasClass('nok')?'nok':'personal');
			$('#how').val($(this).hasClass('email')?'email':'sms');
			
			$.ajax({	
				dataType:	'json',
				url:		'personnel_contactdetails.php',
				data:		$('#personnellist').serialize(),
				success:	function(data)
							{
								if( $('#how').val() == 'email' ) 
									$("#actiontrigger").attr('href', "mailto:?bcc="+encodeURI( data.join(';') ))[0].click();
								else
									$("#actiontrigger").attr('href', "sms://"+encodeURI( data.join(';') ))[0].click();
							},
				error:	function(err,msg){ alert(err); alert(msg); return false; }
			});
		});
	
		$('td.activities a').click( function() { showActivities( $(this).parent() ); });
	
		function showActivities( tablecell ){
			var displayname = tablecell.siblings('.name').html(); 
			$('#dialog').load('personal.php?action=activities&id='+tablecell.parent().data('id')).dialog({
				title: 'Activities attended by '+displayname,
				modal: false,
				position: { my: 'left top', at: 'right top', of: tablecell },
				width: '50em'
			});
		}
	</script>
<?php
	}
	$ATC->gui_output_page_footer('Personnel');
?>
