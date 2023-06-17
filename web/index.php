<?php 

    $num_images_to_generate = 3;
    $H = 256;
    $W = 256;
    $show_banner = false; 
    $n_iter = $num_images_to_generate;

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

    
	<title>Department of Computer Science, Department of Computer Science, University of Otago, New Zealand</title>

	<style>[v-if], [v-show] { display: none !important; }</style>

    <link rel="stylesheet" href="css/global.css" media="all">
	<link rel="stylesheet" href="css/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous">
	
	<link id="injectcss" rel="stylesheet" href="css/computer-science.css" media="all">
	
	
	<script src="scripts/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="scripts/jquery-migrate.min.js" integrity="sha256-F0O1TmEa4I8N24nY0bya59eP6svWcshqX1uzwaWC4F4=" crossorigin="anonymous"></script>
    <script src="scripts/bad-words.js"></script>
    <meta name="Description" content="University of Otago Department of Computer Science">

    <style>
        * {
            margin: 0;
            padding: 0;
        }
        .imgbox {
            display: grid;
            height: 100%;
        }
        .center-fit {
            max-width: 100%;
            max-height: 100vh;
            margin: auto;
        }

        #title {
            margin-top: 1em;
        }

        h3 {
          color: grey;
          font-family: 'Amsi Pro Condbold', 'Open Sans', Helvetica, Arial, Geneva, sans-serif;
          font-size: 23px;
          margin-top: 5px;
          display: inline;
        }

        .panel {
          margin-top: 20px;
          width: 100%;
        }

        .panel_num {
          float: left;
          font-size: 40px;
          padding-bottom: 40px;
          margin-right: 10px;
        }

        .column {
          width: 820px;
          float: left;
          margin-left: 40px;
        }

        .im_panel {
          text-align: center;
          width: 100%;
        }

        .diff_prompt {
          font-size: 20px;
          margin-bottom: 5px;
        }

        .diff_res {
          margin-left: 35px;
        }
    </style>


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
      <div id="content" style="padding-left: 2em;">
        <p class="intro-text"><em>Diffusion</em>-based generative models are deep neural networks that bring out an image from random noise.</p>
           
        <form id="gen_form">
          <h3>Try it!</h3><br>
          <label style="margin-top:20px" for="prompt">Describe what image you want to generate:</label><br>
          <input style="width: 50em" type="text" id="prompt" name="prompt" size="512">
          <input id="gen" type="submit" value="Generate">
        </form>

      </div>

      <div id="results"></div>
      <p></p>

    </div>
  </div>

  <div id="rightcol" class="column">
    <div>

    <div id="content" style="padding-left: 2em;">
   
      <div class="panel">

        <div class="panel_num">1</div>

        <p>A set of captioned images is infused with increasing amounts of noise.</p>

        <div class="im_panel">
          <img src="images/diff1.png" width="350px">
        </div>

      </div>

      <div class="panel">

        <div class="panel_num">2</div>

        <p>A neural network that takes as input a text caption and an image is trained to produce slightly less noisy version of that image.  This is done on lots of images with various levels of noise.</p>

        <div class="im_panel">
          <img src="images/diff2.png" width="450px">
        </div>

      </div>

      <div class="panel">

        <div class="panel_num">3</div>

        <p>Once trained, the network can generate a new image through repeated de-noising, starting from completely ranom noise.  The text caption guides the generation process.  Different starting random noise produces different image.</p>

        <div class="im_panel">
          <img src="images/diff3.png" width="450px">
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

    var content = '<div id="' + msg.demo + '" class="diff_res">';
    content += '<div id="' + msg.demo + '_prompt" class="diff_prompt">Generating images based on prompt: "' + prompt + '"...</div>';  
    for (let i = 1; i <= parseInt(msg.n_iter); i++) {
          content += '<img id="' + msg.demo + '_' + i + '" src="" width="' + parseInt(msg.W) + '" height="' + parseInt(msg.H) + '">';
    } 
    content += '</div>';

    $(content).insertAfter('#results');

    //alert(msg.n_iter);
    var next = true;
    while(!done) {
        while(!next) {
            await sleep(200);
        }
        next = false;
        $.ajax({type: "POST",
                url: "cmd.php",
                data: { cmd: "update", demo: msg.demo, n_iter: n_iter, step: step},
                success: function( umsg ) {
                    umsg = JSON.parse(umsg);

                    if(umsg.update && umsg.results) {
                        let img_id = '#' + umsg.demo + '_' + umsg.n_iter;
                        $(img_id).attr('src',umsg.im_file);
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
                    }
                    next=true;
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    next=true;
                }    
        });    
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