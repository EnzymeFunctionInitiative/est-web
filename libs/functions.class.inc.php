<?php

class functions {
	public static function get_blasts($db,$status = 'NEW') {

		$sql = "SELECT * ";
		$sql .= "FROM generate ";
		$sql .= "WHERE generate_status='" . $status . "' ";
		$sql .= "AND generate_type='BLAST' ";
		$sql .= "ORDER BY generate_time_created ASC ";
		$result = $db->query($sql);
		return $result;





	}

	public static function get_families($db,$status = 'NEW') {

		$sql = "SELECT * ";
		$sql .= "FROM generate ";
		$sql .= "WHERE generate_status='" . $status . "' ";
		$sql .= "AND generate_type='FAMILIES' ";
		$sql .= "ORDER BY generate_time_created ASC ";
		$result = $db->query($sql);
		return $result;


	}

	public static function get_analysis($db,$status = 'NEW') {

                $sql = "SELECT * ";
                $sql .= "FROM analysis ";
                $sql .= "WHERE analysis_status='" . $status . "' ";
                $sql .= "ORDER BY analysis_time_created ASC ";
                $result = $db->query($sql);
                return $result;


	}

	# recursively remove a directory
	public static function rrmdir($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				self::rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}

	public static function bytes_to_megabytes($bytes) {
		return number_format($bytes/1048576,0);

	}

       	public static function get_server_name() {
		return $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/";

        }

	public static function get_retention_days() {
		return __RETENTION_DAYS__;
	}
	
	public static function get_retention_secs() {
		return self::get_retention_days() * 24 * 60 * 60;
	}
	public static function get_results_dir() {
		return __RESULTS_DIR__;
	}
	public static function get_example_dir() {
		return "examples";
	}
	public static function get_evalue() {
		return __EVALUE__;
	}

	public static function get_max_seq() {
		return __MAX_SEQ__;
	}
	public static function get_blast_seq() {
		return __MAX_BLAST_SEQ__;
	}

	public static function get_email_footer() {
		return __EMAIL_FOOTER__;
	}
	
	public static function get_web_root() {
		return __WEB_ROOT__;
	}
	public static function get_admin_email() {
		return __ADMIN_EMAIL__;
	}

	public static function website_enabled() {
		return __ENABLE_WEBSITE__; 
	}


	public static function get_efi_module() {
		return __EFI_MODULE__;

	}

	public static function log_message($message) {
		$current_time = date('Y-m-d H:i:s');
		$full_msg = $current_time . ": " . $message . "\n";
		if (self::log_enabled()) {
			file_put_contents(self::get_log_file(),$full_msg,FILE_APPEND | LOCK_EX);
		}
		echo $full_msg;

	}

	public static function get_log_file() {
		$log_file = __LOG_FILE__;
		if (!$log_file) {
			touch($log_file);
		}
		return $log_file;

	}

	public static function log_enabled() {
		return __ENABLE_LOG__;
	}

	public static function get_cluster_user() {
                return __CLUSTER_USER__;
        }

        public static function get_cluster_user_queuable() {
                return __CLUSTER_USER_QUEUABLE__;
        }
        public static function get_cluster_queuable() {
                return __CLUSTER_QUEUABLE__;
        }

        public static function get_cluster_procs() {
                return __CLUSTER_PROCS__;
        }

	public static function get_interpro_version() {
		return __INTERPRO_VERSION__;

	}
	
	public static function get_uniprot_version() {
		return __UNIPROT_VERSION__;
	}	
	public static function get_generate_queue() {
		return __GENERATE_QUEUE__;
	}
	public static function get_analyse_queue() {
		return __ANALYSE_QUEUE__;
	}

	public static function get_interpro_website() {
		return __INTERPRO_WEBSITE__;
	}
}

?>
