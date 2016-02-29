<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$ATC->gui_output_page_header('Home');
	
?>
	<h2> Upcoming events </h2>
	
	<h2> Alerts to build </h2>
	<ol>
		<li> Cadets signed up to activities without paying </li>
		<li> Cadets signed up to activities without term fees </li>
		<li> Cadets signed up to activities without NZCF8s </li>
		<li> Cadets who will qualify for uniform </li>
		<li> Cadets who've not attended in 2/3 weeks </li>
	</ol>
	
	<h2> Automated emails </h2>
	<ol>
		<li> Email parents/cadets at sign up - link to NZCF8? Incl costs. Reminder, term fees and activity fees </li>
		<li> Email parents/cadets night before. Incl contact numbers </li>
		<li> Activity organiser the emergency contact sheet </li>
		<li> Email treasurer at sign on </li>
		<li> Email treasurer at sign out </li>
		<li> Email treasurer at uniform in/out </li>
	</ol>
	
	
			
<?php
	$ATC->gui_output_page_footer('Home');
?>