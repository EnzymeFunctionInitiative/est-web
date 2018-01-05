<?php
require_once("../libs/functions.class.inc.php");

if (!isset($TopLevelUrl))
    $TopLevelUrl = "http://efi.igb.illinois.edu/efi-est/";

$title = "EFI - Enzyme Similarity Networks Tool";
if (isset($EstId))
    $title .= ": Job #$EstId";

if (isset($Is404Page) && $Is404Page)
    $title = "Page Not Found";

if (isset($IsExpiredPage) && $IsExpiredPage)
    $title = "Expired Job";

include("../../main/html/inc/global_login_button.inc.php");
//$loginText = '<a href="#" class="about" id="login-menu">SIGN IN</a>';
//if (isset($IsLoggedIn) && $IsLoggedIn)
//    $loginText = '<a href="#" class="about" style="cursor:default">' . $IsLoggedIn . '</a>';


?>

<!doctype html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/css/shared.css">
    <link rel="stylesheet" type="text/css" href="/css/global.css">
    <link rel="stylesheet" type="text/css" href="css/est.css">
    <link rel="stylesheet" type="text/css" href="css/tabs.css">
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<?php if (isset($TUTORIAL) && $TUTORIAL) { ?>
    <link rel="stylesheet" type="text/css" href="css/tutorial.css">
<?php } ?>
    <link rel="shortcut icon" href="images/favicon_efi.ico" type="image/x-icon">
    <title><?php echo $title; ?></title>

    <script src="/js/login.js" type="text/javascript"></script>
    <script src="js/submit.js" type="text/javascript"></script>
    <script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="jquery-ui-1.12.1/jquery-ui.js" type="text/javascript"></script>
</head>

<body>
<?php
include("../../main/html/inc/global_nav.inc.php");
?>
    <div id="container">
        <div class="header">
            <div class="header_logo">
                <a href="<?php echo $TopLevelUrl; ?>"><img src="images/efiest_logo.png" width="250" height="75" alt="Enzyme Function Initiative Logo"></a>
                <a href="http://enzymefunction.org"><img src="images/efi_logo.png" class="efi_logo_small" width="80" height="24" alt="Enzyme Function Initiative Logo"></a>
            </div>
        </div>

        <div class="content_holder">
            <h1 class="ruled">EFI - Enzyme Similarity Tool</h1>
<?php if (functions::is_beta_release()) { ?>
            <div class="beta"><h4>BETA</h4></div>
<?php } ?>

