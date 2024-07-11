<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
?>
<?php
if (isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);
    $hasError = false;
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("[Server] Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("[Server] Username must only contain 3-16 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
        $db = getDB();
        $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
        try {
            $stmt->execute($params);
            flash("Profile saved", "success");
        } catch (Exception $e) {
            users_check_duplicate($e->errorInfo);
        }
        //select fresh data from table
        $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
        try {
            $stmt->execute([":id" => get_user_id()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                //$_SESSION["user"] = $user;
                $_SESSION["user"]["email"] = $user["email"];
                $_SESSION["user"]["username"] = $user["username"];
            } else {
                flash("[Server] User doesn't exist", "danger");
            }
        } catch (Exception $e) {
            flash("[Server] An unexpected error occurred, please try again", "danger");
            //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        $hasError = false;
        if (!is_valid_password($new_password)) {
            flash("[Server] Password too short", "danger");
            $hasError = true;
        }
        if (!$hasError) {
            if ($new_password === $confirm_password) {
                //TODO validate current
                $stmt = $db->prepare("SELECT password from Users where id = :id");
                try {
                    $stmt->execute([":id" => get_user_id()]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (isset($result["password"])) {
                        if (password_verify($current_password, $result["password"])) {
                            $query = "UPDATE Users set password = :password where id = :id";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                ":id" => get_user_id(),
                                ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                            ]);

                            flash("Password reset", "success");
                        } else {
                            flash("[Server] Current password is invalid", "warning");
                        }
                    }
                } catch (Exception $e) {
                    echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                }
            } else {
                flash("[Server] New passwords don't match", "warning");
            }
        }
    }
}
?>

<?php
$email = get_user_email();
$username = get_username();
?>
<form method="POST" onsubmit="return validate(this);">
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php se($email); ?>" />
    </div>
    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php se($username); ?>" />
    </div>
    <!-- DO NOT PRELOAD PASSWORD -->
    <div>Password Reset</div>
    <div class="mb-3">
        <label for="cp">Current Password</label>
        <input type="password" name="currentPassword" id="cp" />
    </div>
    <div class="mb-3">
        <label for="np">New Password</label>
        <input type="password" name="newPassword" id="np" />
    </div>
    <div class="mb-3">
        <label for="conp">Confirm Password</label>
        <input type="password" name="confirmPassword" id="conp" />
    </div>
    <input type="submit" value="Update Profile" name="save" />
</form>

<script>
    function validate(form) {
        let email = form.email.value;
        let username = form.username.value;
        let currentPassword = form.currentPassword.value;
        let newPassword = form.newPassword.value;
        let confirmPassword = form.confirmPassword.value;
        let isValid = true;
        let flash = document.getElementById("flash");

        // Clear previous flash messages
        flash.innerHTML = "";

        // Validate email
        if (email.trim() === "") {
            addFlashMessage("[Client] Email must not be empty", "danger");
            isValid = false;
        } else if (!validateEmail(email)) {
            addFlashMessage("[Client] Invalid email address", "danger");
            isValid = false;
        }

        // Validate username
        if (username.trim() === "") {
            addFlashMessage("[Client] Username must not be empty", "danger");
            isValid = false;
        } else if (!validateUsername(username)) {
            addFlashMessage("[Client] Invalid username. Must be 3-16 characters long and contain only letters, numbers, underscores, or dashes.", "danger");
            isValid = false;
        }

        // Validate new password
        if (newPassword.length > 0) {
            if (newPassword.length < 8) {
                addFlashMessage("[Client] New password must be at least 8 characters long", "danger");
                isValid = false;
            }
            if (newPassword !== confirmPassword) {
                addFlashMessage("[Client] Password and Confirm password must match", "danger");
                isValid = false;
            }
        }

        return isValid;
    }

    function addFlashMessage(message, type) {
        let flash = document.getElementById("flash");
        let outerDiv = document.createElement("div");
        outerDiv.className = "row justify-content-center";
        let innerDiv = document.createElement("div");

        innerDiv.className = `alert alert-${type}`;
        innerDiv.innerText = message;

        outerDiv.appendChild(innerDiv);
        flash.appendChild(outerDiv);
    }

    function validateEmail(email) {
        // Basic email validation regex
        let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validateUsername(username) {
        // Username validation regex (3-16 characters, letters, numbers, underscores, or dashes)
        let re = /^[a-zA-Z0-9_-]{3,16}$/;
        return re.test(username);
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>
