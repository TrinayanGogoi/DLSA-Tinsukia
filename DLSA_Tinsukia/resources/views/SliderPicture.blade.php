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
    <!-- Add TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/kw04k5kw7ok0lfr3cpjhe1gn01gzxh6rtox56wrel4o1iovt/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <title>Slider Picture</title>
</head>
<body class="bg-gray-100">

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

    <!-- Main Content -->
    <div class="ml-25 mt-25 p-8">
        <h1 class="text-2xl font-bold mb-8 text-center">Slider Picture Management</h1>

        <!-- Slider Picture Rows -->
        <div class="space-y-6">
            @php
                $sliderIds = [4, 5, 6, 7, 8];
            @endphp
            @foreach ($sliderIds as $index => $id)
            <div class="bg-white p-6 rounded-lg shadow-md" id="slider-row-{{ $id }}">
                <div class="flex items-center space-x-6">
                    <!-- Preview Section -->
                    <div class="w-48 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                        <img id="preview-{{ $id }}" src="" alt="Preview" class="hidden max-w-full max-h-full">
                        <p id="no-image-{{ $id }}" class="text-gray-500">No picture uploaded</p>
                    </div>

                    <!-- Upload Form -->
                    <div class="flex-1">
                        <form id="upload-form-{{ $id }}" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title (Optional)</label>
                                <input type="text" id="title-{{ $id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Image</label>
                                <input type="file" id="image-{{ $id }}" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div class="flex space-x-4">
                                <button type="button" onclick="updateImage({{ $id }})" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Update</button>
                                <button type="button" onclick="clearImage({{ $id }})" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        // Function to handle image preview
        function handleImagePreview(input, previewId, noImageId) {
            const preview = document.getElementById(previewId);
            const noImage = document.getElementById(noImageId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    noImage.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize image preview handlers
        const sliderIds = [4, 5, 6, 7, 8];
        sliderIds.forEach(id => {
            document.getElementById(`image-${id}`).addEventListener('change', function() {
                handleImagePreview(this, `preview-${id}`, `no-image-${id}`);
            });
        });

        // Function to update image
        async function updateImage(id) {
            const formData = new FormData();
            const imageFile = document.getElementById(`image-${id}`).files[0];
            const title = document.getElementById(`title-${id}`).value;

            if (!imageFile) {
                alert('Please select an image to update');
                return;
            }

            formData.append('slider_picture_path', imageFile);
            formData.append('slider_picture_title', title);

            try {
                const response = await fetch(`/api/slider_picture/Update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: formData
                });

                if (response.ok) {
                    alert('Image updated successfully');
                    loadSliderPictures();
                } else {
                    alert('Failed to update image');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating');
            }
        }

        // Function to clear image
        async function clearImage(id) {
            if (!confirm('Are you sure you want to clear this image?')) {
                return;
            }

            try {
                const response = await fetch(`/api/slider_picture/Delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (response.ok) {
                    alert('Image cleared successfully');
                    loadSliderPictures();
                } else {
                    alert('Failed to clear image');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while clearing the image');
            }
        }

        // Function to load all slider pictures
        async function loadSliderPictures() {
            try {
                const response = await fetch('/api/slider_picture/Retrieve');
                const data = await response.json();

                if (data.success) {
                    // Reset all previews
                    sliderIds.forEach(id => {
                        document.getElementById(`preview-${id}`).classList.add('hidden');
                        document.getElementById(`no-image-${id}`).classList.remove('hidden');
                        document.getElementById(`title-${id}`).value = '';
                        document.getElementById(`image-${id}`).value = '';
                    });

                    // Display existing pictures
                    data.data.forEach(picture => {
                        if (sliderIds.includes(picture.id)) {
                            const preview = document.getElementById(`preview-${picture.id}`);
                            const noImage = document.getElementById(`no-image-${picture.id}`);
                            const titleInput = document.getElementById(`title-${picture.id}`);

                            preview.src = `/storage/${picture.slider_picture_path}`;
                            preview.classList.remove('hidden');
                            noImage.classList.add('hidden');
                            titleInput.value = picture.slider_picture_title || '';
                        }
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load slider pictures');
            }
        }

        // Load slider pictures when page loads
        document.addEventListener('DOMContentLoaded', loadSliderPictures);

        // Logout
        // Attach event listener to logout button
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('logoutButton').addEventListener('click', logout);
        });
    </script>
</body>
</html>