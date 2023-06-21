<?php 

    $gen_folder = "generated";
    $cmd_folder = "cmd";

	$results = glob($gen_folder . "/demo*");
    $results_count = count($results);


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

				<div id="show2">

				</div>				

			</div>
		</div>
	</div>

</body>

<script>

	var ready = true;
	var results_count = <?php echo $results_count; ?>;
	let results = <?php echo json_encode($results); ?>;
	var last_shown = -1;
	let switch_to_other_show = 5;
	var count_to_switch_to_other_show = switch_to_other_show;
	let num_shows = 2;
	var index_show = 0;
	var img_cache = [];
	let collage_rows = 4;
	var collage_cols;
	var collage_size;

	for(var i=0; i<results.length; i++) {
		img_cache.push(results[i]);
	}

	function pad(num, size) {
    	num = num.toString();
    	while (num.length < size) num = "0" + num;
   		return num;
	}


	function sleep(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}

	async function check_update(msg, s=0) {

		var done = false;
		var step = 0;
		var n_iter = 1;
		var n_available;
		var n_idx;
		var delay = 50;

		if(s==0) {
			$("#show1").hide();
			$("#show2").hide();
			$("#img_s1").css("visibility", "hidden");
			$("#show0_title").show();
			$("#show0").show();			
		} else if(s==1) {
			n_iter = Math.floor(Math.random() * 3) + 1;
			step = 0;
			$("#show0").hide();
			$("#show0_title").hide();
			$(".img_s0").css("visibility", "hidden");
			$("#show2").hide();
			$("#show1").show();
		} else if(s==2) {
			set_collage_vars();
			$("#show0").hide();
			$("#show0_title").hide();
			$(".img_s0").css("visibility", "hidden");
			$("#show1").hide();
			$("#show2").show();
			step = 'final';

			let num_ims = collage_cols*collage_rows;
			var index_perm = permute(num_ims);
			var available = [];
			for(let i=0;i<num_ims;i++) {
				for(let j=1;j<=3;j++) {
					available.push([i,j]);
				}
			}
			available = permute_array(available);

			n_available = available.pop();
			n_idx = index_perm.pop();

			msg.demo = "demo" + pad(n_available[0], 5);
			n_iter = n_available[1];
			delay = 200;
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
							} else if(s==2) {
								img_id = n_idx;
								img_id = '#img_s2_' + (img_id);
							}
							$(img_id).load(umsg.im_file, function(responseTxt, statusTxt, xhr) {
								if(statusTxt == "success") {
									$(this).attr('src',umsg.im_file);
									$(this).css("visibility", "visible");
									next = true;
								}
							});
							if(umsg.final) {
								if(s < 2) {
									if(umsg.n_iter >= parseInt(msg.n_iter) || s==1) {
										done = true;
									} else {
										n_iter = umsg.n_iter + 1;
										step = 0;
									}
									if(img_cache.includes(umsg.im_file)) {
										im_cache.push(umsg.im_file);
									}
								} else {
									if(available.length > 0 && index_perm.length > 0) { 

										n_available = available.pop();
										n_idx = index_perm.pop();
										msg.demo = "demo" + pad(n_available[0], 5);
										n_iter = n_available[1];
										next = true;
										if(delay > 20) {
											delay -= 20;
										}
									} else {
										done = true;
									}
								}
							} else if (umsg.step != 'final') {
								step = umsg.step + 1;
								if(step >= 50) {
									step = 'final';
								}
							}
						} else {
							if(s== 2) {
								if(available.length > 0) { 
									n_available = available.pop();
									msg.demo = "demo" + pad(n_available[0], 5);
									n_iter = n_available[1];
								} else {
									done = true;
								}
							}
							next=true;
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) { 
						next=true;
					}    
			});    
			await sleep(delay);
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
					if(!results.includes(msg.demo)) {
						results.push(msg.demo);
					}

					if(msg.rcount > results_count) {
						check_update(msg, 0);
					} else {
						if(count_to_switch_to_other_show <= 0) {
							if(index_show == 1 && (results.length*3) < (collage_cols*collage_rows)) {
								index_show = 0;
							}
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

	function permute(max) {
	  
	  indices = [];
	  for(let i=0;i<max;i++) {
		indices.push(i);
	  }

	  for (let i = 0; i < max; i++) {
    	j = Math.floor(Math.random() * max);
		temp = indices[i];
		indices[i] = indices[j];
		indices[j] = temp;
	  }

	  return indices;	  
	}

	function permute_array(array) {

		indices = permute(array.length);

		var perm_array = [];

		for(let i=0;i<array.length;i++) {
			perm_array.push(array[indices[i]]);
		}
		return perm_array;
	}

	function set_sh2() {
		$(".img_s2").remove();

		var k = 0;
		for(var r=0; r<collage_rows; r++) {
			for(var c=0; c<collage_cols; c++) {
				var img_id = 'img_s2_' + k;
				var img = $('<img id="' + img_id + '" class="img_s2" src="" width="' + collage_size + '" height="' + collage_size + '">');
				k += 1;
				$("#show2").append(img);
			}
		}
		$(".img_s2").css("visibility","hidden");

	}

	function set_collage_vars() {
		collage_size = Math.floor($(window).height() / collage_rows);
		collage_cols = Math.floor($(window).width() / collage_size);
		set_sh2();
	}


	$( document ).ready(function() {
 
		set_collage_vars();
	
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