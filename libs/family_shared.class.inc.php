<?php

require_once('option_base.class.inc.php');
require_once('generate_helper.class.inc.php');

abstract class family_shared extends option_base {


    //////////////////Private Variables//////////

    protected $families = array();

    ///////////////Public Functions///////////

    public function __construct($db,$id = 0) {
        parent::__construct($db, $id);
    }

    public function __destruct() {
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTIONS SPECIFIC TO FAMILIES
    
    public function get_families() { return $this->families; }
    public function get_families_comma() { return implode(",", $this->get_families()); }

    //returns an array of the pfam families or empty array otherwise
    public function get_pfam_families() {
        $pfam_families = array();
        foreach ($this->families as $family) {
            if (substr($family,0,2) == "PF") {
                array_push($pfam_families,$family);
            }

        }
        return $pfam_families;

    }

    //returns an array of the interpro families or empty array otherwise
    public function get_interpro_families() {
        $interpro_families = array();
        foreach ($this->families as $family) {
            if (substr($family,0,3) == "IPR") {
                array_push($interpro_families,$family);
            }

        }
        return $interpro_families;

    }

    // END FUNCTIONS SPECIFIC TO FAMILIES
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // OVERLOADS
    
    protected function get_insert_array($data) {
        $insert_array = parent::get_insert_array($data);
        $formatted_families = $this->format_families($data->families);
        $insert_array['generate_families'] = $formatted_families;
        return $insert_array;
    }

    protected function validate($data) {
        $result = parent::validate($data);

        if (($data->families != "") && (!$this->verify_families($data->families))) {
            $result->errors = true;
            $result->message .= "<br><b>Please enter valid Interpro and PFam numbers</b></br>";
        }

        return $result;
    }

    protected function get_run_script() {
        return "generatedata.pl";
    }

    protected function get_run_script_args($outDir) {

        $pfam_families = implode(",",$this->get_pfam_families());
        $interpro_families = implode(",",$this->get_interpro_families());

        $parms = array();
        $parms = generate_helper::get_run_script_args($outDir, $parms);
        #$parms["-blast"] = strtolower($this->get_program());
        if (strlen($interpro_families)) {
            $parms["-ipro"] = $interpro_families;
        }
        if (strlen($pfam_families)) {
            $parms["-pfam"] = $pfam_families;
        }
        $parms["-fraction"] = $this->get_fraction();

        return $parms;
    }

    protected function load_generate($id) {
        $result = parent::load_generate($id);
        if (! $result) {
            return;
        }

        if ($result[0]['generate_families'] != "") {
            $families = explode(",", $result[0]['generate_families']);
            $this->families = $families;
        }

        return $result;
    }

    // END OVERLOADS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function verify_families($families) {
        $family_array = explode(",",$families);
        $valid = 0;
        foreach ($family_array as $family) {
            $family = trim(rtrim($family));
            $family = strtolower($family);
            //Test if Interpro Number
            if ((substr($family,0,3) == "ipr") && (is_numeric(substr($family,3))) && (strlen(substr($family,3)) == 6)) {
                $valid = 1;

            }
            //Test if PFam Number
            elseif ((substr($family,0,2) == "pf") && (is_numeric(substr($family,2))) && (strlen(substr($family,2)) == 5)) {
                $valid = 1;
            }
            else {
                $valid = 0;
                break;
            }
        }
        return $valid;

    }

    private function format_families($families) {
        $search = array(" ");
        $replace = "";
        $formatted_families = str_ireplace($search,$replace,$families);
        $formatted_families = strtoupper($formatted_families);
        return $formatted_families;

    }

}

?>
