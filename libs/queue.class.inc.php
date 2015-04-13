<?php

class queue {

        ////////////////Private Variables//////////

        private $queue_name;
        
	///////////////Public Functions///////////

        public function __construct($queue_name = 'default') {
        	$this->queue_name = $queue_name;

	}

        public function __destruct() {
        

	}

	public function get_queue_name() { 
		return $this->queue_name;

	}
	public function get_max_queuable() { 
                return $this->get_queue_info('max_queuable');


	}
	public function get_max_user_queuable() {
		return $this->get_queue_info('max_user_queuable');

	}

        public function get_num_queued($username = "") {
		$user_cmd = "";
		if ($username != "") {
			$user_cmd = "-u " . $username;
		}
                $output;
                $exec = "qstat -t " . $user_cmd . " " . $this->get_queue_name() . " | tail -n +6 | wc -l";
                exec($exec,$output);
                $num_queued = $output[0];
                return $num_queued;
        }

	////////////////Private Functions//////////////////

	private function get_queue_info($queue_parameter) {
                $output;
                $exec = "qstat -Qf " . $this->get_queue_name() . " | grep $queue_parameter | cut -d ' ' -f 7";
                exec($exec,$output);
                return $output[0];


	}
}
