<?php
	require_once "atc_documentation.class.php";
	$ATC = new ATC_Documentation();
	
	$ATC->gui_output_page_header('System');
?>
	<div id="system">
		<h2> Permmissions structure </h2>
		<div>
			<?php $ATC->dump_userperms(); ?>
		</div>
	
		<h2 href="system_lesson_category.php"> Lesson Categories </h2>
		<div>
		</div>
		
		<h2 href="system_lesson.php"> Lessons </h2>
		<div>
		</div>
	</div>
	
		<script>
			$(function(){					
				
					$('#system').accordion({ 
						header: 'h2', 
						changestart:function( event, ui )
						{
							// There's a delay after load to set height, to allow the DOM to update properly
							ui.newContent.load(ui.newHeader.attr('href'), function(){ setTimeout( function(){ $('#personnelform').accordion('resize'); },500); });
						} 
					});
				
			});
			
		</script>
<?php
	$ATC->gui_output_page_footer('System');
?>