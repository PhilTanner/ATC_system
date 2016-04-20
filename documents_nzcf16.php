<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$personnel = $ATC->get_personnel( null, 'ASC', null, true );
	
	if( !isset($_GET['personnel_id']) )
	{
		$ATC->gui_output_page_header('Documents');
?>
	
	<form name="datepicker" id="datepicker">
		<fieldset>
			<legend>Choose personnel to generate documentation for</legend>
			<label for="personnel">Personnel:</label>
			<input type="hidden" name="document" value="nzcf16" />
			<select name="personnel_id[]" multiple="multiple" required="required">
				<?php
					foreach( $personnel as $obj )
					{
						echo '<option value="'.$obj->personnel_id.'"'.($obj->enabled?'':' class="ui-state-disabled"').'>'.$obj->rank.' '.$obj->display_name.'</option>';
					}
				?>
			</select>
			<button type="submit" class="update">Update</button>			
		</fieldset>
	</form>
<?php
	} else {

		require_once "atc_finance.class.php";
		$ATC_Finance = new ATC_Finance();

		// Create a PDF document for us to use
		require('./fpdf17/fpdf.php');
	
		$footerstring = '';
		
		class PDF extends FPDF
		{
			// Page header
			function Header()
			{
				global $activity; 
				
				$this->SetTextColor(0);
				$this->Image('49squadron.png',175,5,25);
				// Line break
				$this->Ln(22);
			}
			function Footer()
			{
				global $footerstring;
				
				$this->SetY(-15);
				$this->SetFont('Arial', '', 8);
				$this->Cell(20,6,$footerstring.' - Page '.$this->PageNo(),0);
				$this->Cell(0,6,'Printed: '.date(ATC_SETTING_DATETIME_OUTPUT),0,1,'R');
			}
		}

		// Instanciation of inherited class
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->SetLeftMargin(10);
	
		$pdf->SetFont('Arial','B',12);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(255);
		
		foreach( $_GET['personnel_id'] as $id )
		{
			if($ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $id) )
				$obj = $ATC->get_personnel($id);
			else continue;
			$obj->nok = $ATC->get_nok($id);
			$obj->promotions = $ATC->get_promotion_history($id);
			if($ATC->user_has_permission( ATC_PERMISSION_FINANCE_VIEW, $id) )
				$obj->payments = $ATC_Finance->get_account_history($id, '1970-01-01', date('c'));
			else 
				$obj->payments = null;
			
			//var_dump($obj);
			
			$pdf->AddPage();
			
			$footerstring = $obj->rank.' '.$obj->display_name;
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Personal Details',0,1,'C',true);
			$pdf->SetFont('Arial','B',8);
			
			$pdf->SetFillColor(200);
			$pdf->Cell(40,8,'Section',1,0,'R',true);
			$pdf->Cell(60,8,'On record',1,0,'C',true);
			$pdf->Cell(90,8,'Update to',1,1,'C',true);
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Name',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,$obj->firstname.' '.$obj->lastname,1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Personal Mobile',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,$obj->mobile_phone,1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Email address',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(60,8,$obj->email,1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Date of birth',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,date(ATC_SETTING_DATE_OUTPUT." Y", strtotime($obj->dob)),1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Medical information',0,1,'C',true);
			$pdf->SetFont('Arial','',8);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,16,'Allergies',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(60,16,(trim($obj->allergies)?$obj->allergies:'N/A'),1,0,'C',true);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(90,16,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,16,'Reactions to medicines',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(60,16,(trim($obj->medicinal_reactions)?$obj->medicinal_reactions:'N/A'),1,0,'C',true);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(90,16,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,16,'Dietary Requirements',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(60,16,(trim($obj->dietary_requirements)?$obj->dietary_requirements:'N/A'),1,0,'C',true);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(90,16,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,16,'Other notes',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(60,16,(trim($obj->other_notes)?$obj->other_notes:'N/A'),1,0,'C',true);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(90,16,'',1,1,'C',true);
			
			
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Next of kin',0,1,'C',true);
			$pdf->SetFont('Arial','',8);
			
			foreach($obj->nok as $nok)
			{
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Relationship',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				switch($nok->relationship)
				{
					case ATC_NOK_TYPE_MOTHER:
						$pdf->Cell(60,8,'Mother',1,0,'C',true);
						break;
					case ATC_NOK_TYPE_STEPMOTHER:
						$pdf->Cell(60,8,'Step-Mother',1,0,'C',true);
						break;
					case ATC_NOK_TYPE_GRANDMOTHER:
						$pdf->Cell(60,8,'Grandmother',1,0,'C',true);
						break;
					case ATC_NOK_TYPE_FATHER:
						$pdf->Cell(60,8,'Father',1,0,'C',true);
						break;
					case ATC_NOK_TYPE_STEPFATHER:
						$pdf->Cell(60,8,'Step-Father',1,0,'C',true);
						break;
					case  ATC_NOK_TYPE_GRANDFATHER:
						$pdf->Cell(60,8,'Grandfather',1,0,'C',true);
						break;
					case  ATC_NOK_TYPE_SPOUSE:
						$pdf->Cell(60,8,'Spouse',1,0,'C',true);
						break;
					case  ATC_NOK_TYPE_DOMPTNR:
						$pdf->Cell(60,8,'Domestic Partner',1,0,'C',true);
						break;
					case  ATC_NOK_TYPE_SIBLING:
						$pdf->Cell(60,8,'Sibling',1,0,'C',true);
						break;
					default:
						$pdf->Cell(60,8,'Other/Unknown',1,0,'C',true);
				}
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'First name',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->firstname,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Last name',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->lastname,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Email address',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',6);
				$pdf->Cell(60,8,$nok->email,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Mobile number',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->mobile_number,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Home phone',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->home_number,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Address 1',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->address1,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Address 2',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->address2,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'City',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->city,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
				
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,'Postal code',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,$nok->postcode,1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
			}
			
			
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Squadron information',0,1,'C',true);
			$pdf->SetFont('Arial','',8);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Joining date',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,date(ATC_SETTING_DATE_OUTPUT." Y", strtotime($obj->joined_date)),1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Flight',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,$obj->flight,1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Signed social media',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,($obj->social_media_approved?'Y':'N'),1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Promotion history',0,1,'C',true);
			$pdf->SetFont('Arial','',8);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,8,'Current rank',1,0,'R',true);
			$pdf->SetFillColor(255);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(60,8,$obj->rank,1,0,'C',true);
			$pdf->Cell(90,8,'',1,1,'C',true);
			
			foreach($obj->promotions as $rank)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->SetFillColor(200);
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(40,8,($rank->acting?'Acting ':'').$rank->rank.' ('.$rank->rank_shortname.')',1,0,'R',true);
				$pdf->SetFillColor(255);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(60,8,date(ATC_SETTING_DATE_OUTPUT." Y", strtotime($rank->date_achieved)),1,0,'C',true);
				$pdf->Cell(90,8,'',1,1,'C',true);
			}
			
			
			
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(190,10,'Financial history',0,1,'C',true);
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(30,8,'Date',1,0,'C',true);
			$pdf->Cell(40,8,'Amount',1,0,'C',true);
			$pdf->Cell(40,8,'Payment type',1,0,'C',true);
			$pdf->Cell(40,8,'Reference',1,0,'C',true);
			$pdf->Cell(40,8,'Recorded by',1,1,'C',true);
			
			$totalamount = 0;
			
			$pdf->SetFillColor(255);
			foreach($obj->payments as $obj)
			{
				$totalamount += $obj->amount;
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(30,8,date(ATC_SETTING_DATE_OUTPUT." Y", strtotime($obj->created)),1,0,'C',true);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(40,8,$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $obj->amount),1,0,'R',true);
				$pdf->Cell(40,8,$translations['paymenttype'][$obj->payment_type],1,0,'C',true);
				
				$pdf->SetFont('Arial','',6);
				$pdf->Cell(40,8,$obj->reference,1,0,'C',true);
				$pdf->Cell(40,8,$obj->rank.' '.$obj->display_name,1,0,'C',true);
				$pdf->Cell(0,8,'',1,1,'C',true);
			}
			
			$pdf->SetFillColor(200);
			$pdf->SetFont('Arial','UB',8);
			$pdf->Cell(30,8,'Total',1,0,'C',true);
			$pdf->Cell(40,8,$ATC_Finance->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $totalamount),1,0,'C',true);
			$pdf->Cell(40,8,'',1,0,'C',true);
			$pdf->Cell(40,8,'',1,0,'C',true);
			$pdf->Cell(40,8,'',1,1,'C',true);
		}
		
		$pdf = $pdf->Output($ATC->generate_session_key().'.pdf','D');

/*
			
		$pdf->Cell(5,3,'SM',1,0,'C',true);
		$pdf->Cell(20,3,'Attendee',1,0,'L',true);
		$pdf->Cell(15,3,'Contact #',1,0,'L',true);
		$pdf->Cell(28,3,'Next of Kin',1,0,'L',true);
		$pdf->Cell(15,3,'NOK Mobile',1,0,'L',true);
		$pdf->Cell(15,3,'NOK Home',1,0,'L',true);
		$pdf->Cell(35,3,'Allergies',1,0,'L',true);
		$pdf->Cell(35,3,'Medical Conditions',1,0,'L',true);
		$pdf->Cell(35,3,'Medicinal Reactions',1,0,'L',true);
		$pdf->Cell(35,3,'Dietary Req.',1,0,'L',true);
		$pdf->Cell(35,3,'Other.',1,0,'L',true);
		
		$pdf->Cell(1,3,'',0,1);
		
		$row = 0;

		foreach( $attendees as $attendee )
		{
			$row++;
			
			$pdf->SetFont('Arial','',6);
			$pdf->SetTextColor(0);
			
			// Colour code entries for easy reading
			$text = array( 0,0,0 );
			$background = array( 255,255,255 );
			
			// Default stripe our rows for easier reading
			if( $row % 2 )	$background = array( 230,230,230 );
			//else		$pdf->SetFillColor( 255 );
			
			if( strlen(trim($attendee->allergies)) ) $text[0] = 51;
			if( strlen(trim($attendee->medical_conditions)) ) $text[1] = 51;
			if( strlen(trim($attendee->medicinal_reactions)) ) $text[2] = 51;
			
			if( strlen(trim($attendee->dietary_requirements)) ) $background[0] = 190;
			if( strlen(trim($attendee->other_notes)) ) $background[1] = 190;
			
			$pdf->SetTextColor($text[0], $text[1], $text[2]);
			$pdf->SetFillColor( $background[0], $background[1], $background[2]);			
			
			$lineheight = 4;
			if( count($attendee->nok) )
				$totallineheight = count($attendee->nok)*$lineheight;
			else
				$totallineheight = $lineheight;
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(5,$totallineheight,($attendee->social_media_approved==-1?'':'X'),1,0,'C',true);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(20,$totallineheight,$attendee->rank.' '.$attendee->display_name,1,0,'L',true);
			$pdf->Cell(15,$totallineheight,$attendee->mobile_phone,1,0,'L',true);
			
			$n = 0;
			foreach($attendee->nok as $nok )
			{
				$pdf->SetFont('Arial','',6);
				switch( $nok->relationship )
				{
					case ATC_NOK_TYPE_MOTHER:
						$relation = 'Mother';
						break;
					case ATC_NOK_TYPE_FATHER:
						$relation = 'Father';
						break;
					case ATC_NOK_TYPE_STEPMOTHER:
						$relation = 'Step-Mother';
						break;
					case ATC_NOK_TYPE_STEPFATHER:
						$relation = 'Step-Father';
						break;
					case ATC_NOK_TYPE_SPOUSE:
						$relation = 'Spouse';
						break;
					case ATC_NOK_TYPE_SIBLING:
						$relation = 'Sibling';
						break;
					case ATC_NOK_TYPE_DOMPTNR:
						$relation = 'Domestic Partner';
						break;
					case ATC_NOK_TYPE_OTHER:
						$relation = 'Other';
						break;
					case ATC_NOK_TYPE_GRANDMOTHER:
						$relation = 'Grandmother';
						break;
					case ATC_NOK_TYPE_GRANDFATHER:
						$relation = 'Grandfather';
						break;
					default:
						$relation = 'Unknown';
				}
				
				$n++;
				if($n > 1 )
				{
					$pdf->Cell(1,$lineheight,'',0,1);
					$pdf->Cell(40,$lineheight,'',0);
				}
				
				$pdf->Cell(28,$lineheight,$nok->firstname.' '.$nok->lastname.' ('.$relation.')',1,0,'L',true);
				$pdf->Cell(15,$lineheight,$nok->mobile_number,1,0,'L',true);
				$pdf->Cell(15,$lineheight,$nok->home_number,1,0,'L',true);
				
				if( $n == 1 )
				{
					$pdf->SetFont('Arial','',4);
					$pdf->Cell(35,$totallineheight,$attendee->allergies,1,0,'L',true);
					$pdf->Cell(35,$totallineheight,$attendee->medical_conditions,1,0,'L',true);
					$pdf->Cell(35,$totallineheight,$attendee->medicinal_reactions,1,0,'L',true);
					$pdf->Cell(35,$totallineheight,$attendee->dietary_requirements,1,0,'L',true);
					$pdf->Cell(35,$totallineheight,$attendee->other_notes,1,0,'L',true);
				}
			}

			if( !count($attendee->nok) )
			{
				$pdf->SetFont('Arial','',4);
				$pdf->Cell(28,$totallineheight,'',1,0,'L',true);
				$pdf->Cell(15,$totallineheight,'',1,0,'L',true);
				$pdf->Cell(15,$totallineheight,'',1,0,'L',true);
				$pdf->Cell(35,$totallineheight,$attendee->allergies,1,0,'L',true);
				$pdf->Cell(35,$totallineheight,$attendee->medical_conditions,1,0,'L',true);
				$pdf->Cell(35,$totallineheight,$attendee->medicinal_reactions,1,0,'L',true);
				$pdf->Cell(35,$totallineheight,$attendee->dietary_requirements,1,0,'L',true);
				$pdf->Cell(35,$totallineheight,$attendee->other_notes,1,0,'L',true);
			}
			
			//if( count($attendee->nok) < 2 )
				$pdf->Cell(1,$lineheight,'',0,1);
		}
			
		$pdf = $pdf->Output($activity->title.'.pdf','D');
		
		*/
	}
		
?>