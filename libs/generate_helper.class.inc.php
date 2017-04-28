<?php

require_once('functions.class.inc.php');

class generate_helper {

    public static function get_run_script_args($out, $parms) {
        $parms["-np"] = functions::get_cluster_procs();
        $parms["-evalue"] = functions::get_evalue();
        $parms["-tmp"] = $out->relative_output_dir;
        $parms["-maxsequence"] = functions::get_max_seq();
        $parms["-queue"] = functions::get_generate_queue();
        $parms["-memqueue"] = functions::get_generate_queue();
        return $parms;
    }
    
}

?>
