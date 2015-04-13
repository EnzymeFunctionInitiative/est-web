<?php

include_once 'includes/main.inc.php';
if (isset($_POST['download_plot'])) {
        $type = $_POST['type'];
        $stepa = new stepa($db,$_POST['id']);
        if ($stepa->get_key() != $_POST['key']) {
                echo "No EFI-EST Selected. Please go back";
                exit;
        }
        
        $stepa->download_graph($type);

}
else {
        echo "No EFI-EST Selected. Please go back";
        exit;

}



?>
