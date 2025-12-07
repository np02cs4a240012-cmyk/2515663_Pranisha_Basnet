<?php
$name = $email = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // 1. VALIDATION

    // Check empty fields
if (empty($name)) {
    $errors['name'] = "Name is required.";
}

if (empty($email)) {
    $errors['email'] = "Email is required.";
}  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
}

if (empty($password)) {
    $errors['password'] = "Password is required.";
} elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
}
 if ($confirm_password !== $password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Only continue if no errors
      if (empty($errors)) {

        // 2. Read Json file
        $file = "users.json";

        if (!file_exists($file)) {
            $errors['file'] = "Error: users.json not found.";
        } else {
            $json_data = file_get_contents($file);

            if ($json_data === false) {
                $errors['file'] = "Error reading JSON file.";
            }
        }

        // If file was read successfully
        if (empty($errors)) {
            $users = [];
        }

        // 3. HASH PASSWORD
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. ADD NEW USER
            
            $new_user = [
                "name" => $name,
                "email" => $email,
                "password" => $hashed_password
            ];

            $users[] = $new_user;

           
            // 5. WRITE BACK TO JSON
          
            $save = file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

            if ($save === false) {
                $errors['file'] = "Error writing to JSON file.";
            } else {
                $success = "Registration successful!";
                $name = $email = "";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>

<h2>User Registration Form</h2>

<?php if ($success): ?>
    <div style="color: green;"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $name; ?>">
    <span style="color:red"><?php echo $errors['name'] ?? ''; ?></span>
    <br><br>

    <label>Email:</label><br>
    <input type="text" name="email" value="<?php echo $email; ?>">
    <span style="color:red"><?php echo $errors['email'] ?? ''; ?></span>
    <br><br>

    <label>Password:</label><br>
    <input type="password" name="password">
    <span style="color:red"><?php echo $errors['password'] ?? ''; ?></span>
    <br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password">
    <span style="color:red"><?php echo $errors['confirm_password'] ?? ''; ?></span>
    <br><br>

    <button type="submit">Register</button>

    <br><br>
    <span style="color:red"><?php echo $errors['file'] ?? ''; ?></span>

</form>




