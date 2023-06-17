<?php 

    $gen_folder = "generated";
    $cmd_folder = "cmd";

    $gen_count = count(glob($gen_folder . "/*"));

    $ready_file = $cmd_folder . "/ready";
    $go_file = $cmd_folder . "/go";

    $args = $_POST;

    if(isset($args['cmd']) && !empty($args['cmd'])) {
        $cmd = $args['cmd'];

        if($cmd == 'ready?') {
            if(file_exists($ready_file) && !file_exists($go_file)) {
                echo json_encode(array("cmd"=>$cmd, "ready"=>True));                
            } else {
                echo json_encode(array("cmd"=>$cmd, "ready"=>False));
            }
        } elseif($cmd == 'go') {
            $prompt = $args['prompt'];
            $H = $args['H'];
            $W = $args['W'];
            $n_iter = $args['n_iter'];
            $demo_count = $gen_count +1;

            //Testing (remove)
            $demo_count -= 1;

            $demo = "demo" . str_pad($demo_count, 5, '0', STR_PAD_LEFT);
            $cmd = "--prompt \"$prompt\" --n_iter $n_iter --n_samples 1 --H $H --W $W --demo $demo\n";

            //Testing (uncomment)
            //unlink($ready_file);

            $img_folder = $gen_folder . "/$demo";
            $fp = fopen($go_file, 'w');
            fwrite($fp, $cmd);
            fclose($fp);

            echo json_encode(array("going"=>True, "img_folder"=>$img_folder, "n_iter"=>$n_iter, "demo"=>$demo, "H"=>$H, "W"=>$W));
        } elseif($cmd == 'update') {
            $demo = $args['demo'];
            $n_iter = intval($args['n_iter']);
            $step = $args['step'];
            $demo_folder = $gen_folder . "/$demo";

            if(!file_exists($demo_folder)) {
                echo json_encode(array("update"=>True, "results"=>False));
            } else {
                $results = glob($demo_folder . "/*");
                if($step == 'final') {
                    $im_file = $demo_folder . "/diffusion_" . str_pad($n_iter, 2, '0', STR_PAD_LEFT) . ".png";
                    if(file_exists($im_file)) {
                        echo json_encode(array("update"=>True, "results"=>True, "im_file"=>$im_file, "final"=>True,"n_iter"=>$n_iter, "demo"=>$demo));
                    } else {
                        echo json_encode(array("update"=>True, "results"=>False));
                    }
                } else {
                    $step = intval($step);
                    for($i=$step;$i<=50;$i++) {
                        $im_file = $demo_folder . "/img" . str_pad($n_iter, 2, '0', STR_PAD_LEFT) . "_" . str_pad($i, 3, '0', STR_PAD_LEFT) . ".png";
                        if(file_exists($im_file)) {
                            echo json_encode(array("update"=>True, "results"=>True, "im_file"=>$im_file, "final"=>False, "step"=>$i, "n_iter"=>$n_iter, "demo"=>$demo));
                            return;
                        }
                    }
                    echo json_encode(array("update"=>True, "results"=>False));
                }
            }
        }
    }

?>
