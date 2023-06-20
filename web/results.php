<?php 

    $gen_folder = "generated";
    $cmd_folder = "cmd";

    $results_count = count(glob($gen_folder . "/demo*"));


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


<body id="computer-science" class="n10562 OTAGO746916  frontpage" style="background-color: black">
	
	<div class="container">
		<div class="vertical-center">
			<div style="width: 100%; text-align: center">
				<div id="show0_title" class="rtitle" style="color: white";>Generating images with AI</div>
			</div>
			
			<div id="results" style="text-align:center">

				<div id="show0">
					<img id="img_s0_1" class="img_s0" src="" width="31%" height="31%">
					<img id="img_s0_2" class="img_s0" src="" width="31%" height="31%">
					<img id="img_s0_3" class="img_s0" src="" width="31%" height="31%">
				</div>

				<div id="show1">
					<img id="img_s1" src="" height="vh">
				</div>


			</div>
		</div>
	</div>

</body>

<script>

	var ready = true;
	var results_count = <?php echo $results_count; ?>;
	var last_shown = -1;
	let switch_to_other_show = 5;
	var count_to_switch_to_other_show = switch_to_other_show;
	let num_shows = 1;
	var index_show = 0;

	function sleep(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}

	async function check_update(msg, s=0) {

		var done = false;
		var step = 0;
		var n_iter = 1;

		if(s==0) {
			$("#show1").hide();
			$("#img_s1").css("visibility", "hidden");
			$("#show0_title").show();
			$("#show0").show();			
		} else if(s==1) {
			n_iter = Math.floor(Math.random() * 3) + 1;
			step = 0;
			$("#show0").hide();
			$("#show0_title").hide();
			$(".img_s0").css("visibility", "hidden");
			$("#show1").show();
		}



		var next = true;
		while(!done) {
			await sleep(50);
			if(!next) {
				await sleep(200);
			}
			next = false;
			$.ajax({type: "POST",
					url: "cmd.php",
					data: { cmd: "update", demo: msg.demo, n_iter: n_iter, step: step},
					success: function( umsg ) {
						umsg = JSON.parse(umsg);

						if(umsg.update && umsg.results) {
							var img_id = '#img_s0_' + n_iter;
							if(s==1) {
								img_id = '#img_s1';
							}
							$(img_id).load(umsg.im_file, function(responseTxt, statusTxt, xhr) {
								if(statusTxt == "success") {
									$(this).attr('src',umsg.im_file);
									$(this).css("visibility", "visible");
									next = true;
								}
							});
							if(umsg.final) {
								if(umsg.n_iter >= parseInt(msg.n_iter) || s==1) {
									done = true;
								} else {
									n_iter = umsg.n_iter + 1;
									step = 0;
								}
							} else if (umsg.step != 'final') {
								step = umsg.step + 1;
								if(step >= 50) {
									step = 'final';
								}
							}
						} else {
							next=true;
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) { 
						next=true;
					}    
			});    
			await sleep(50);
		}
		await sleep(10000);
		ready = true;
	}

	function show_random_result() {

		$.ajax({type: "POST",
            url: "cmd.php",
            data: { cmd: "random", rcount: results_count, lshown: last_shown},
            success: async function( msg ) {
                msg = JSON.parse(msg);
                if(msg.ok) {
					if(msg.rcount > results_count) {
						check_update(msg, 0);
					} else {
						if(count_to_switch_to_other_show <= 0) {
							check_update(msg, index_show+1);
							index_show = (index_show + 1) % num_shows;
							count_to_switch_to_other_show = switch_to_other_show;
						} else {
							check_update(msg, 0);
							count_to_switch_to_other_show -= 1;
						}
					}
					results_count = msg.rcount;
					last_shown = msg.lshown;
                } else {
					await sleep(2000);
					ready = true;
				}
            }
          });
		
	}

	async function show_next() {

		while(true) {

			if(ready) {
				ready = false;
				show_random_result();
			} else {
				await sleep(500);
			}
		}
		
	}


	$( document ).ready(function() {
 
		$(window).on('resize', function(){
			$("#img_s1").height($(this). height());
		});
		

		$("#show0").css("visibility", "hidden");
		$(".img_s0").css("visibility", "hidden");
		$("#show1").css("visibility", "hidden");
		$("#img_s1").height($(window). height());
		show_next();



	});
</script>

</html>