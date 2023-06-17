<?php 

    $gen_folder = "generated";
    $demos = glob($gen_folder . "/demo*");


?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-NZ"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8 lang="en-NZ""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="en-NZ"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class=" js flexbox canvas canvastext webgl no-touch geolocation postmessage no-websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients no-cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths" lang="en-NZ"><!--<![endif]-->




<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    
	<title>Department of Computer Science, University of Otago, New Zealand</title>

	<style>[v-if], [v-show] { display: none !important; }</style>

    <link rel="stylesheet" href="css/global.css" media="all">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	
	<link id="injectcss" rel="stylesheet" href="css/computer-science.css" media="all">
	<link rel="stylesheet" href="css/csdemo.css">
	
	
	<script src="scripts/jquery.min.js"></script>
    <script src="scripts/jquery-migrate.min.js"></script>
    <script src="scripts/bad-words.js"></script>
    <meta name="Description" content="University of Otago Department of Computer Science">

</head>


<body id="computer-science" class="n10562 OTAGO746916  frontpage">
	
	<div style="margin-bottom: 20px"></div>
	
	<?php foreach($demos as $demo_folder) {
		$j = strpos($demo_folder, "demo");	
		if($j === false) {
			continue;
		}

		$demo = substr($demo_folder, $j);

		echo "<div id=" . $demo . " class=\"diff_res\" style=\"border-style: solid; width: 320px; float: left\">\n";

		echo "<div class=\"admin_prompt\">" . $demo_folder . "</div>\n"; 

		
		$prompt_file = $demo_folder . "/prompt.txt";
		
		if(file_exists($prompt_file)) {
			$prompt = file_get_contents($prompt_file);
			echo "<div class=\"admin_prompt\">\"" . $prompt . "\"</div>\n"; 
		}
		for($i=1;$i<=3;$i++) {
			$img_file = $demo_folder . "/diffusion_" . str_pad($i, 2, '0', STR_PAD_LEFT) . ".png";
			if(!file_exists($img_file)) {
				for($j=50;$j>0;$j--) {
					$img_file = $demo_folder . "/img" . str_pad($i, 2, '0', STR_PAD_LEFT) . "_" . str_pad($j, 3, '0', STR_PAD_LEFT) . ".png";
					if(file_exists($img_file)) {
						break;
					}
				}
				
			}
			if(file_exists($img_file)) {
				echo '  <img src="' . $img_file . '" width="100px" height="100px">' . "\n"; 
			}
		}

		echo "<form>\n";
		echo "<input type=\"hidden\" name=\"demo\" value=\"" . $demo . "\">\n";
        echo  "<input class=\"button del_btn\" type=\"submit\" value=\"Delete\n\">\n";
        echo "</form>\n";


		echo "</div>";
		
		
		
		
	}?>
	

</body>

<script>




	$( document ).ready(function() {
		$( ".del_btn" ).on( "click", function(e) {
           
			e.preventDefault();
			
        
			let form = $(this).parent();
		
			let demo = $(form).find( 'input:hidden' ).val();
		
			if (confirm('Are you sure you want to save delete \'' + demo + '\'?')) {		
				$.ajax({type: "POST",
						 url: "cmd.php",
						 data: { cmd: "delete", demo: demo},
						 success: function( msg ) {
							msg = JSON.parse(msg);
							if(msg.ok) {
								$('#' + msg.demo).remove();
							}
						 }
				});
			} 		
			
		});

	});
</script>

</html>