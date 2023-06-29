<?php 
	$win = False;
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$win = True;
	} 

    $gen_folder = "generated";
    $sel_folder = "selected";
    $cmd_folder = "cmd";

    $gen_count = count(glob($gen_folder . "/demo*"));

    $ready_file = $cmd_folder . "/ready";
    $go_file = $cmd_folder . "/go";

    $args = $_POST;

	function deleteDir($dirPath) {
		if (! is_dir($dirPath)) {
			return;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

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
            //$demo_count -= 1;

			while(True) {
				$demo = "demo" . str_pad($demo_count, 5, '0', STR_PAD_LEFT);
				if(file_exists($gen_folder . "/" . $demo)) {
					$demo_count +=1;
				} else {
					break;
				}
			}
			
			
            $cmd = "--prompt \"$prompt\" --n_iter $n_iter --n_samples 1 --H $H --W $W --demo $demo\n";

            unlink($ready_file);

            $img_folder = $gen_folder . "/$demo";
            $fp = fopen($go_file, 'w');
            fwrite($fp, $cmd);
            fclose($fp);

            echo json_encode(array("going"=>True, "img_folder"=>$img_folder, "n_iter"=>$n_iter, "demo"=>$demo, "H"=>$H, "W"=>$W));
        } elseif($cmd == 'update') {
            $demo = $args['demo'];
            $n_iter = intval($args['n_iter']);
            $step = $args['step'];
			$folder = $args['folder'];
			if($folder == 'generated') {
				$demo_folder = $gen_folder . "/$demo";
			} else {
				$demo_folder = $sel_folder . "/$demo";
			}

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
        } elseif($cmd == 'random') {
			$rcount = intval($args['rcount']);
			$lshown = intval($args['lshown']);
			$demo_folders = glob($sel_folder . "/demo*");
			usort( $demo_folders, function( $a, $b ) { return filemtime($a) - filemtime($b); } );
			$demos = array();
			foreach($demo_folders as $folder) {
				$all_results = True;
				for($i=1;$i<=3;$i++) {
					$final_image = $folder . "/diffusion_" . str_pad($i, 2, '0', STR_PAD_LEFT) . ".png";
                    if(!file_exists($final_image)) {
						$all_results = False;
						break;
					}
				}
				if($all_results) {
					array_push($demos,$folder);
				}				
			}

			$demo_count = count($demos);
			if($demo_count >= 1) {
				if($demo_count == 1) {
					$index = 0;
					$rcount = 1;
				} else if($rcount < $demo_count) {
					$index = $rcount;	
					$rcount = $rcount + 1;
				} else {
					while(True) {
						$index = rand(0,$demo_count-1);
						if($index != $lshown) {
							break;
						}
					}
					$rcount = $demo_count;
				}
				$demo_path = $demos[$index];
				$j = strpos($demo_path, "demo");
				if($j !== false) {
					$demo = substr($demo_path, $j);
					$lshown = $index;
					echo json_encode(array("ok"=>True, "demo"=>$demo, "n_iter"=> "3", "rcount" => $rcount, "lshown" => $lshown));			
				} else {
					echo json_encode(array("ok"=>False));
				}

				
			} else {
				echo json_encode(array("ok"=>False));
			}
		} elseif($cmd == 'delete') {
			$demo = $args['demo'];
			$demo_folder = $gen_folder . "/" . $demo;
			$demo_lnk_folder = $sel_folder . "/" . $demo;

			if(file_exists($demo_lnk_folder)) {
				if($win) {
					rmdir($demo_lnk_folder);
				} else {
					unlink($demo_lnk_folder);
				}
			}

			if(file_exists($demo_folder)) {
				deleteDir($demo_folder);
				echo json_encode(array("ok"=>True, "demo"=>$demo));
			} else {
				echo json_encode(array("ok"=>False));				
			}
		} elseif($cmd == "select") {
			$demo = $args['demo'];
			$cmd_arg = $args['arg'];
			$demo_tgt_folder = $gen_folder . "/" . $demo;
			$demo_lnk_folder = $sel_folder . "/" . $demo;
			$absolute_path = dirname(__FILE__);
			$demo_tgt_folder = $absolute_path . "/" . $demo_tgt_folder;
			$demo_lnk_folder = $absolute_path . "/" . $demo_lnk_folder;
			if($cmd_arg == "rm") {
				if($win) {
					rmdir($demo_lnk_folder);
				} else {
					unlink($demo_lnk_folder);
				}
			} elseif($cmd_arg == "mk") {
				symlink($demo_tgt_folder, $demo_lnk_folder);
			}
			echo json_encode(array("ok"=>True, "demo"=>$demo, "arg"=>$cmd_arg));
		} else {
			echo json_encode(array("ok"=>False));
		}
    }

?>
