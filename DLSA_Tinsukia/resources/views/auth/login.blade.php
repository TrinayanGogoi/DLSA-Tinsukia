<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Admin Login</title>
</head>
<style>
        @keyframes bg-rainbow {
        0% { background-color: #ff0000; }
        14% { background-color: #ff9900; }
        28% { background-color: #ffff00; }
        42% { background-color: #33cc33; }
        57% { background-color: #00ccff; }
        71% { background-color: #3366ff; }
        85% { background-color: #cc33ff; }
        100% { background-color: #ff0000; }
        }
        .rainbow-badge {
        animation: bg-rainbow 2s linear infinite;
        }
</style>
<body>

<span class="rainbow-badge text-white text-xs font-bold px-2 py-1 rounded" hidden>NEW</span>

<!-- Full-screen background image below nav and sidebar -->
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:-1; background: url('/storage/pictures/Screenshot%202025-06-27%20204921.png') center center/cover no-repeat;"></div>


    <!-- Step 1: Login Form -->
     <div class="fixed inset-0 flex items-center justify-center backdrop-blur-xs">
        <div class="flex flex-col items-center w-md rounded-3xl shadow-2xl backdrop-blur-md bg-white/60">
            <p class="text-3xl font-bold mt-3">Admin Login</p>
            <form id="loginForm" class="flex flex-col justify-center w-full mt-5 mb-5">
                <div class="flex flex-col py-2 px-2 mb-2">
                    <label for="email" class="text-xs px-2 font-bold text-gray-500 mb-1">Admin email:</label>
                    <input type="email" id="email" name="email" placeholder="Email" required class="rounded-sm px-2 bg-white h-9 text-md inset-shadow-sm shadow-lg  focus:outline-none duration-300 ease-in-out hover:scale-110 ">
                </div>
                <div class="flex flex-col py-2 px-2 mb-2">
                    <label for="password" class="text-xs px-2 font-bold text-gray-500 mb-1">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Password" required class="rounded-sm px-2 bg-white h-9 text-md inset-shadow-sm shadow-lg focus:outline-none duration-300 ease-in-out hover:scale-110 ">
                </div>
                <div class="flex flex-col justify-center items-center mb-2">
                    <button type="submit" class="rounded-xl w-1/2 h-9 m-2 bg-blue-500 text-white font-bold text-lg inset-shadow-sm shadow-lg shadow-blue-500/50 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 hover:bg-blue-700">Login</button>
                </div>
            </form>


            <!-- Step 2: 2FA Verification Form (Initially Hidden) -->
            <form id="verifyForm" style="display: none;" class="flex flex-col justify-center w-full mt-5 mb-5">
                <div class="flex flex-col py-2 px-2 mb-2">
                    <label for="code" class="text-xs px-2 font-bold text-gray-500 mb-1">Enter 2FA Code:</label>
                    <input type="text" id="code" name="code" placeholder="******" required class="rounded-sm px-2 bg-white h-9 text-md inset-shadow-sm shadow-lg  focus:outline-none duration-300 ease-in-out hover:scale-110 ">
                </div>
                <div class="flex flex-col justify-center items-center mb-2">
                    <button type="submit" class="rounded-xl w-1/2 h-9 m-2 bg-blue-500 text-white font-bold text-lg inset-shadow-sm shadow-lg shadow-blue-500/50 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 hover:bg-blue-700">Verify</button>
                </div>
            </form>
        </div>
    </div>
    



    <!-- Step 3: Register Form -->
    <p hidden>New Admin? <a href="/register">Register here</a></p>

    <script>
        let storedEmail = '';

        // LOGIN Logic - generates 2FA code and email
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;

            fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === '2FA code sent. Please verify.') {
                    storedEmail = email; // Save email for verification step
                    document.getElementById('loginForm').style.display = 'none';
                    document.getElementById('verifyForm').style.display = 'block';
                    // alert('A 2FA code has been sent to your email.');
                    alert('Your 2FA code is: ' + data.two_factor_code); // ⚠️ For development purposes only, remove this in production!
                } else {
                    alert('Login failed: ' + data.message);
                }
            });
        });

        // 2FA verification logic
        document.getElementById('verifyForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let code = document.getElementById('code').value;

            fetch('/api/verify-2fa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email: storedEmail, code }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    // after calling api/login, we save the admin and token details to localstorage in browser
                    // the verify-2fa function in authController which in routed through api.php generates and gives token and admin details
                    
                                // localStorage: persists until explicitly cleared (manual logout, developer tools, etc.)
                                // sessionStorage: automatically cleared when the tab or browser is closed
                    // So after final ----> CHANGE ALL LOCALSTORAGE TO SESSIONSTORAGE (on all pages)
                    localStorage.setItem('admin', JSON.stringify(data.admin)); // Store admin details as JSON 
                    localStorage.setItem('token', data.token);
                    window.location.href = '/dashboard'; // after storing, calls dashboard. token and admin is stored in the localstorage of dashboard.
                } else {
                    alert('2FA verification failed:' + data.message);
                }
            });
        });

       // ✅ Prevent back button from navigating to previous pages after logout
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.history.pushState(null, null, window.location.href);
        };

    </script>
</body>
</html>