<?php
require(__DIR__ . "/../../partials/nav.php");
?>

<!-- UCID: vs53, Date: Jul 10th 2024 -->
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">

<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email/Username</label>
        <input type="text" name="email" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <input type="submit" value="Login" />
</form>
<script>
        // UCID: vs53, Date: Jul 10th 2024

    function validate(form) {
        let emailOrUsername = form.email.value;
        let password = form.password.value;
        let isValid = true;
        let flash = document.getElementById("flash");

        flash.innerHTML = "";

        if (emailOrUsername.trim() === "") {
            addFlashMessage("[Client] Email/Username must not be empty", "danger");
            isValid = false;
        }

        if (emailOrUsername.includes("@")) {
            if (!validateEmail(emailOrUsername)) {
                addFlashMessage("[Client] Invalid email address", "danger");
                isValid = false;
            }
        } else {
            if (!validateUsername(emailOrUsername)) {
                addFlashMessage("[Client] Invalid username. Must be 3-16 characters long and contain only letters, numbers, underscores, or dashes.", "danger");
                isValid = false;
            }
        }

        if (password.trim() === "") {
            addFlashMessage("[Client] Password must not be empty", "danger");
            isValid = false;
        }

        if (password.length < 8) {
            addFlashMessage("[Client] Password must be at least 8 characters long", "danger");
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
        let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validateUsername(username) {
        let re = /^[a-zA-Z0-9_-]{3,16}$/;
        return re.test(username);
    }
</script>
<?php
//TODO 2: add PHP Code
    // UCID: vs53, Date: Jul 10th 2024

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("[Server] Email must not be empty");
        $hasError = true;
    }
    if (str_contains($email, "@")) {
        //sanitize
        $email = sanitize_email($email);
        //validate
        if (!is_valid_email($email)) {
            flash("[Server] Invalid email address");
            $hasError = true;
        }
    } else {
        if (!is_valid_username($email)) {
            flash("[Server] Invalid username");
            $hasError = true;
        }
    }
    if (empty($password)) {
        flash("[Server] Password must not be empty");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("[Server] Password too short");
        $hasError = true;
    }
    if (!$hasError) {
        //flash("Welcome, $email");
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users 
        where email = :email or username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        //flash("Weclome $email");
                        $_SESSION["user"] = $user; //sets our session data from db
                        //lookup potential roles
                        $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        //save roles or empty array
                        if ($roles) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        flash("Welcome, " . get_username());
                        die(header("Location: home.php"));
                    } else {
                        flash("[Server] Invalid password");
                    }
                } else {
                    flash("[Server] Email not found");
                }
            }
        } catch (Exception $e) {
            flash("[Server] <pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
