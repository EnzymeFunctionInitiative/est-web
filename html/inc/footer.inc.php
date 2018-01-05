<?php

$feedbackMessage = "Need help or have suggestions or comments?   Please click here.";

if ((isset($Is404Page) && $Is404Page) || (isset($IsExpiredPage) && $IsExpiredPage)) {
    echo "</div>";
    if (isset($Is404Page) && $Is404Page)
        $feedbackMessage = "Please click here to report this.";
}

?>

            <p class="suggestions">
                <a href="http://enzymefunction.org/content/sequence-similarity-networks-tool-feedback" target="_blank"><?php echo $feedbackMessage; ?></a>
            </p>
        </div> <!-- content_holder -->

        <div class="footer_container">
            <div class="footer">
                <div class="address inline">
                    Enzyme Function Initiative |
                    1206 W. Gregory Drive Urbana, IL 61801
                    &nbsp;|&nbsp; <a href="mailto:efi@enzymefunction.org">efi@enzymefunction.org</a>
                </div>
                <div class="footer_logo inline">
                    <a href="http://www.nigms.nih.gov/" target="_blank"><img alt="NIGMS" src="images/nighnew.png" style="width: 201px; height: 30px;"></a>
                </div>
            </div>
        </div>
    </div> <!-- container -->
<?php /*
<script>
$(document).ready(function() {
    var updateMsg = $("#update-message");
    if (updateMsg.children().count > 0 || updateMsg.text().trim().length > 0)
        updateMsg.removeClass("initial-hidden");

<?php if (!isset($IsLoggedIn) || !$IsLoggedIn) { ?>
    addLoginActions("user_login.php", "index.php");
<?php } ?>

<?php if (isset($_GET["show-login"]) && $_GET["show-login"] == 1) { ?>
    showLoginForm();
<?php } ?>

});
</script>

<?php if (!isset($IsLoggedIn) || !$IsLoggedIn) { ?>
<div id="login-form" class="login-form hidden">
    <div style="margin-bottom:15px">Sign in or <a href="user_account.php?action=create">create an account</a> to view previous job history.</div>
    <table border="0">
        <tbody>
            <tr><td>Email Address:</td><td><input type="text" name="login-email" id="login-email"></td></tr>
            <tr><td>Password:</td><td><input type="password" name="login-password" id="login-password"></td></tr>
        </tbody>
    </table>
    <div id="login-error"></div>
</div>

<?php } ?>
 */ ?>

<?php include("../../main/html/inc/global_login.inc.php"); ?>

</body>
</html>

