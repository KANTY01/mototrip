<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoTrip Register</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <div class="register-box">
            <h2>Register Mototrip</h2>
            <form action="./php/register.php" method="post">
                <div class="input-box">
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" required>
                    <label>Confirm Password</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
                </div>
                <button type="submit" class="btn">Register</button>
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
