<?php 

    $num_images_to_generate = 3;
    $H = 512;
    $W = 512;
    $show_banner = false; 
    $n_iter = $num_images_to_generate;

	$gen_folder = "generated";
	$demo_folders = glob($gen_folder . "/demo*");
	$demo_count = count($demo_folders);
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

  <?php if($show_banner) { ?>
      <div class="imgbox">
          <img class="center-fit" src='images/banner.jpg'>
      </div>
  <?php } ?>


  <div id="leftcol" class="column">


    <div id="title">
      <h1 class="notopimage">Generating images with AI</h1>
    </div> <!-- ends title -->
   
    <div>
      <div class="content">
        <p class="intro-text"><em>Diffusion</em>-based generative models are deep neural networks that bring out an image from random noise.</p>
           
        <form id="gen_form">
          <h3>Try it!</h3><br>
          <label id="prompt_lbl" style="margin-top:20px" for="prompt">Describe what image you want to generate:</label>
          <input style="width: 93%" type="text" id="prompt" name="prompt" size="512">
          <input class="button" id="gen" type="submit" value="Generate">
        </form>

      </div>

      <div id="results"></div>
      <p></p>

	<?php
	  $k=0;
	  for($i = count($demo_folders) - 1; $i >= 0; $i--)
	  {
		  if($k >= 2) {
			  break;
		  }
		  $demo_path = $demo_folders[$i];
		  $prompt_file = $demo_path . '/prompt.txt';
		  
		  if(!file_exists($prompt_file)) {
			  continue;
		  }
		  
		  $prompt = file_get_contents($prompt_file);

		  $j = strpos($demo_path, "demo");

		  if($j !== false) {
			  $demo_id = substr($demo_path, $j);
			  echo '<div id="' . $demo_id . '" class="diff_res">' . "\n";
			  echo '  <div id="' . $demo_id . '_prompt" class="diff_prompt">Generated images based on prompt: "' . $prompt . '".</div>' . "\n";
			  for($m = 1; $m<= $n_iter; $m++) {
				  $img_file = $demo_path . "/diffusion_" . str_pad($m, 2, '0', STR_PAD_LEFT) . ".png";
				  if(file_exists($img_file)) {
					echo '  <img id="' . $demo_id . '_' . $m . '" src="' . $img_file . '" width="31%" height="31%">' . "\n"; 
				  }
			  }
			  echo "</div>\n";
			  $k += 1;
		  }
	  }
	?>

    </div>
  </div>

  <div id="rightcol" class="column">
    <div>

    <div class="content" style="padding-left: 2em; margin-top: 2em;">
   
      <div class="panel">

        <div class="panel_num">1</div>

        <p>A set of captioned images is infused with increasing amounts of noise.</p>

        <div class="im_panel">
          <img src="images/diff1.png" width="40%">
        </div>

      </div>

      <div class="panel">

        <div class="panel_num">2</div>

        <p>A neural network that takes as input a text caption and an image is trained to produce a slightly less noisy version of that image.  This is done on lots of images with various levels of noise.</p>

        <div class="im_panel">
          <img src="images/diff2.png" width="55%">
        </div>

      </div>

      <div class="panel">

        <div class="panel_num">3</div>

        <p>Once trained, the network can generate a new image through repeated de-noising, starting from completely random noise.  The text caption guides the generation process.  Different starting random noise produces a different image.</p>

        <div class="im_panel">
          <img src="images/diff3.png" width="55%">
        </div>

      </div>
    </div>
  </div>

</body>

<script>

function makeid(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
    }
    return result;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function check_ready() {

    var ready = false;

    while(!ready) {
        $.ajax({type: "POST",
                url: "cmd.php",
                data: { cmd: "ready?"},
                success: function( msg ) {
                    msg = JSON.parse(msg);
                    if(msg.cmd == "ready?" && msg.ready) {
                        $( "#gen_form").css('visibility', 'visible');
                        ready = true;
                    }
                }
        });    
        if(ready) {
            break;
        }
        await sleep(3000);
    }
}

async function check_update(msg, prompt) {

    var done = false;
    var step = 0;
    var n_iter = 1;
	var first = true;



    //alert(msg.n_iter);
    var next = true;
    while(!done) {
		await sleep(200);
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
						if(first) {
							var numItems = $('.diff_res').length
							while (numItems >= 2) {
								$('.diff_res').last().remove();
								numItems = $('.diff_res');
							}


							var content = '<div id="' + msg.demo + '" class="diff_res">';
							content += '<div id="' + msg.demo + '_prompt" class="diff_prompt">Generating images based on prompt: "' + prompt + '"...</div>';  
							for (let i = 1; i <= parseInt(msg.n_iter); i++) {
								  content += '<img id="' + msg.demo + '_' + i + '" src="" width="31%" height="31%">';
							} 
							content += '</div>';

							$(content).insertAfter('#results');
							first = false;
						}
						
						
                        let img_id = '#' + umsg.demo + '_' + umsg.n_iter;
                        $(img_id).load(umsg.im_file, function(responseTxt, statusTxt, xhr) {
							if(statusTxt == "success") {
								$(this).attr('src',umsg.im_file);
								next = true;
							}
						});
						//$(img_id).attr('src',umsg.im_file);
                        if(umsg.final) {
                            if(umsg.n_iter >= parseInt(msg.n_iter)) {
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
		await sleep(200);
    }
    $('#' + msg.demo + '_prompt').text('Generated images based on prompt: "' + prompt + '".');  

    check_ready();
}

function run_gen(localList) {
    let filter = new Filter({ list: localList });
 
    var seed = 0;
    var id;
    var state = "ready?";

    $( "#gen_form").css('visibility', 'hidden');;


    $( "#gen" ).on( "click", function(e) {
           
        e.preventDefault();
        
        var prompt = $("#prompt").val().trim();
        prompt = filter.clean(prompt);

        if(prompt.includes("*")) {
          $("#prompt").val(prompt);
          $("#prompt_lbl").text("Please refolmulate your text prompt:");
          return;
        }
        $("#prompt_lbl").text('Describe what image you want to generate:');

        id = makeid(6);


        $.ajax({type: "POST",
            url: "cmd.php",
            data: { seed: seed, cmd: "go", prompt: prompt, <?php echo "H: $H, W: $W, n_iter: $n_iter";?>},
            success: function( msg ) {
                msg = JSON.parse(msg);
                if(msg.going) {
                    $( "#gen_form").css('visibility', 'hidden');;
                    check_update(msg, prompt);
                }
            }
          });

        seed += 1;
        seed %= 5000;
    });

    check_ready();

}

$( document ).ready(function() {
 
    $.getJSON('scripts/bad-words-list.json', function(data) {
       run_gen(data.words);
    });
    

    

});
</script>

</html>