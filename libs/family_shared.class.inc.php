<?php

require_once('option_base.class.inc.php');
require_once('generate_helper.class.inc.php');

abstract class family_shared extends option_base {


    //////////////////Private Variables//////////

    protected $families = array();
    protected $length_overlap = 1.0;
    protected $seq_id = 1.0;
    protected $uniref_version = "";
    protected $no_demux = 0;

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
    public function get_sequence_identity() { return $this->seq_id; }
    public function get_length_overlap() { return $this->length_overlap; }
    public function get_uniref_version() { return $this->uniref_version; }
    public function get_no_demux() { return $this->no_demux; }
    public function is_cd_hit_job() { return strpos($this->seq_id, ",") !== FALSE; } //HACK: this is a temporary hack for research purposes
            


    //returns an array of the pfam families or empty array otherwise
    public function get_pfam_families() {
        $pfam_families = array();
        foreach ($this->families as $family) {
            if (substr($family,0,2) == "PF" || substr($family,0,2) == "CL") { // Also allow PFAM clans
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

    public function get_cdhit_stats() {
        $results_dir = functions::get_results_dir();
        $file = $results_dir . "/" . $this->get_output_dir();
        $file .= "/" . functions::get_cdhit_stats_filename();
        $file_handle = @fopen($file,"r") or die("Error opening " . $this->stats_file . "\n");
        $i = 0; 
        $stats_array = array();
        $keys = array('SequenceId','SequenceLength','Nodes');
        while (($data = fgetcsv($file_handle,0,"\t")) !== FALSE) {
            $data[0] = number_format(floatval($data[0]) * 100, 0) . "%";
            $data[1] = number_format(floatval($data[1]) * 100, 0) . "%";
            array_push($stats_array,array_combine($keys,$data));
        }
        fclose($file_handle);
        return $stats_array;
    }


    // END FUNCTIONS SPECIFIC TO FAMILIES
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // OVERLOADS
    
    protected function get_insert_array($data) {
        $insert_array = parent::get_insert_array($data);
        $formatted_families = $this->format_families($data->families);
        $insert_array['generate_families'] = $formatted_families;
        $insert_array['generate_seq_id'] = $data->seq_id;
        $insert_array['generate_length_overlap'] = $data->length_overlap;
        if ($data->uniref_version && ($data->uniref_version == "50" || $data->uniref_version == "90"))
            $insert_array['generate_uniref'] = $data->uniref_version;
        $insert_array['generate_no_demux'] = $data->no_demux;
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
        $parms = generate_helper::get_run_script_args($outDir, $parms, $this);
        #$parms["-blast"] = strtolower($this->get_program());
        if (strlen($interpro_families))
            $parms["-ipro"] = $interpro_families;
        if (strlen($pfam_families))
            $parms["-pfam"] = $pfam_families;
        if ($this->seq_id) {
            $parms["-sim"] = $this->seq_id;
            if (strpos($this->seq_id, ",") !== FALSE)
                $parms["-cd-hit"] = functions::get_cdhit_stats_filename();
        }
        if ($this->length_overlap)
            $parms["-lengthdif"] = $this->length_overlap;
        if ($this->uniref_version)
            $parms["-uniref-version"] = $this->uniref_version;
        if (($this->length_overlap || $this->seq_id) && $this->no_demux)
            $parms["-no-demux"] = "";
        $parms["-fraction"] = $this->get_fraction();
        $parms["-seq-count-file"] = $this->get_accession_counts_file_full_path();
        $parms["-conf-ratio-file"] = functions::get_convergence_ratio_filename();

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

        if ($result[0]['generate_seq_id'])
            $this->seq_id = $result[0]['generate_seq_id'];
        if ($result[0]['generate_length_overlap'])
            $this->length_overlap = $result[0]['generate_length_overlap'];
        if ($result[0]['generate_uniref'] != "--")
            $this->uniref_version = $result[0]['generate_uniref'];
        else
            $this->uniref_version = "";
        if ($result[0]['generate_no_demux'])
            $this->no_demux = 1;
        else
            $this->no_demux = 0;

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
            //Test if Clan Number
            elseif ((substr($family,0,2) == "cl") && (is_numeric(substr($family,2))) && (strlen(substr($family,2)) == 4)) {
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
