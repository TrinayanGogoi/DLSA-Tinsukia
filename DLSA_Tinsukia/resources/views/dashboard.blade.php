<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Prevents going back to dashboard after logout and back button -->
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
                // Page was restored from bfcache
                if (!localStorage.getItem('token')) {
                    window.location.replace('/login');
                }
            }
        });
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <script src="{{ asset('functions/logout.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Admin Dashboard</title>

    <script>
        // Check authentication on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('token')) {
                window.location.replace('/login');
            }

            // Get admin details from localStorage
            const token = localStorage.getItem('token');
            const admin = JSON.parse(localStorage.getItem('admin'));

            // Display admin details
            document.getElementById('adminName').textContent = admin.name;
            document.getElementById('adminEmail').textContent = admin.email;

            // Attach logout event listener
            document.getElementById('logoutButton').addEventListener('click', logout);
        });
    </script>
</head>
<body>
    <!-- Full-screen background image below nav and sidebar -->
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:-1; background: url('/storage/pictures/Screenshot%202025-06-27%20204921.png') center center/cover no-repeat;"></div>

    <nav class="grid grid-cols-2 gap-4 bg-[#1F8AFF] h-[93px] fixed left-0 top-0 w-full">  
        <!-- HYPERLINK TO OTHER PAGES -->
        <div class="flex items-center ml-4">
            <p class="text-2xl font-bold text-[#FFFFFF] w-40">DLSA Tinsukia</p>
        </div>
        <div class="flex flex-row gap-9 justify-end items-center mr-4 font-bold text-[#FFFFFF]">
            <a class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130 " href="{{ url('/dashboard') }}">Dashboard</a>
            <a class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130" href="{{ url('/ContentUpload') }}">Upload</a>
            <a class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130" href="{{ url('/DisplayContent') }}">View</a>
            <a class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130" href="{{ url('/SliderPicture') }}">Slider</a>
            <a class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130" href="{{ url('/Latest') }}">Latest</a>
            <p class="flex h-full items-center justify-center duration-300 ease-in-out hover:-translate-y-3 hover:-translate-x-0 hover:scale-130"><button id="logoutButton">Logout</button></p>
        </div>
    </nav> 

    <!-- Fixed Sidebar -->
    <div class="fixed left-0 top-20 h-full w-20 bg-[#1F8AFF] z-50"></div>

<!-- for the sidebar to appear at the left, shift all content to right by 25 -->
<div class="ml-25 mt-25 flex flex-col items-center justify-center min-h-[70vh]">
    <div class="backdrop-blur-md bg-white/60 rounded-3xl shadow-2xl p-12 w-full max-w-xl mt-40 border border-white/40">
        <h1 class="text-4xl md:text-5xl font-extrabold text-center text-[#1F8AFF] drop-shadow mb-6">Welcome to the Admin Dashboard</h1>
        <div class="flex flex-col items-center gap-2 mt-8">
            <h3 class="text-lg md:text-xl font-semibold text-gray-700 mb-2 tracking-wide">Admin Details</h3>
            <div class="w-full flex flex-col gap-2 bg-white/70 rounded-xl p-4 shadow">
                <p class="text-base md:text-lg text-gray-800"><span class="font-bold text-[#1F8AFF]">Name:</span> <span id="adminName"></span></p>
                <p class="text-base md:text-lg text-gray-800"><span class="font-bold text-[#1F8AFF]">Email:</span> <span id="adminEmail"></span></p>
            </div>
        </div>
    </div>
</div>
</body> 
</html>