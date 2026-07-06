<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
</head>
<body>
    <h1>Admin Register</h1>
    <form id="registerForm">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="/login">Login here</a></p>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    alert('Registration successful! Please log in.');
                    localStorage.removeItem('token'); // deletes token
                    localStorage.removeItem('admin'); // deletes admin details
                    window.location.href = '/login'; // Redirect to login page
                } else {
                    alert('Registration failed: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>
