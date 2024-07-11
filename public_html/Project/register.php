<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<!-- UCID: vs53, Date: Jul 10th 2024 -->

<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label> 
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    
    function validate(form) {
        let email = form.email.value;
        let username = form.username.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;
        let flash = document.getElementById("flash");

        // Clear previous flash messages
        flash.innerHTML = "";

        // Check if email is empty
        if (email.trim() === "") {
            addFlashMessage("[Client] Email must not be empty", "danger");
            isValid = false;
        }

        // Validate email
        if (!validateEmail(email)) {
            addFlashMessage("[Client] Invalid email address", "danger");
            isValid = false;
        }

        // Check if username is empty
        if (username.trim() === "") {
            addFlashMessage("[Client] Username must not be empty", "danger");
            isValid = false;
        }

        // Validate username
        if (!validateUsername(username)) {
            addFlashMessage("[Client] Invalid username. Must be 3-16 characters long and contain only letters, numbers, underscores, or dashes.", "danger");
            isValid = false;
        }

        // Check if password is empty
        if (password.trim() === "") {
            addFlashMessage("[Client] Password must not be empty", "danger");
            isValid = false;
        }

        // Check if confirm password is empty
        if (confirm.trim() === "") {
            addFlashMessage("[Client] Confirm password must not be empty", "danger");
            isValid = false;
        }

        // Validate password length
        if (password.length < 8) {
            addFlashMessage("[Client] Password must be at least 8 characters long", "danger");
            isValid = false;
        }

        // Check if passwords match
        if (password !== confirm) {
            addFlashMessage("[Client] Passwords must match", "danger");
            isValid = false;
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
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("[Server] Email must not be empty", "danger");
        $hasError = true;
    }
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
    if (empty($password)) {
        flash("[Server] Password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("[Server] Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("[Server] Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("[Server] Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (Exception $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
