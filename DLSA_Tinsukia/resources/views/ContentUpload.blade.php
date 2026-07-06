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
    <title>Content Upload</title>

    <script>
        // Initialize TinyMCE
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#description',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                height: 300,
                menubar: true,
                branding: false,
                promotion: false,
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save(); // Save content to textarea
                    });
                }
            });
        });

        // Function to compress image before upload
        async function compressImage(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function(event) {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        // Calculate new dimensions while maintaining aspect ratio
                        const MAX_WIDTH = 800;
                        const MAX_HEIGHT = 800;
                        
                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height = Math.round((height * MAX_WIDTH) / width);
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width = Math.round((width * MAX_HEIGHT) / height);
                                height = MAX_HEIGHT;
                            }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Convert to blob with maximum compression
                        canvas.toBlob((blob) => {
                            resolve(new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            }));
                        }, 'image/jpeg', 0.5); // 0.5 is the quality (0.1 to 1.0)
                    };
                };
            });
        }

        // Functions to add multiple Pictures/Links as needed with a + sign to add more ----------------------------------------------------------------
        function addPictureField() { // WORKING
            let container = document.getElementById('picturesContainer');
            let count = container.querySelectorAll('input[name="pictures"]').length + 1; // current count + 1

            let timestamp = Date.now();
            let fileInputId = `fileInput_${timestamp}`;
            let previewId = `imagePreview_${timestamp}`;

            let div = document.createElement('div');
            div.classList.add("flex", "items-center", "space-x-4");
            div.innerHTML = `
                <span class="text-gray-500">${count}.</span>
                                <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="picture_title" placeholder="Picture Title">
                                <input type="file" name="pictures" id="${fileInputId}" onchange="validateFileSize(this, '${previewId}')" class="hidden">
                                <label for="${fileInputId}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">Add File</label>
                                <div class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                    <img id="${previewId}" src="" alt="Preview" class="hidden max-w-full max-h-full">
                                </div>
                                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.remove()">Remove</button>
            `;
            container.appendChild(div);
        }

        function addLinkField() {
            let container = document.getElementById('linksContainer');
            let count = container.querySelectorAll('input[name="links"]').length + 1;
            
            let div = document.createElement('div');
            div.classList.add("space-y-2");
            div.innerHTML = `
                <div class="flex items-center space-x-4">
                    <span class="text-gray-500">${count}.</span>
                    <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="link_titles" placeholder="Link Title">
                </div>
                <div class="flex items-center space-x-4 ml-8">
                    <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="url" name="links" placeholder="Upload the link">
                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.parentNode.remove()">Remove</button>
                </div>
            `;
            container.appendChild(div);
        }

        // Function To validate Image size/ and preview ----------------------------------------------------------------
        function validateFileSize(input, previewId) {
            if (input.files.length > 0) {
                let file = input.files[0];
                let maxSize = 1 * 1024 * 1024; // 1MB in bytes
                const preview = document.getElementById(previewId);

                if (file.size > maxSize) {
                    alert("File size exceeds 1MB. Please select a smaller file.");
                    input.value = ""; // Clear the input
                    preview.classList.add('hidden'); // Hide the image preview
                } else {
                    // If the file is valid, display the image preview
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden'); // Show the image preview
                    };
                    
                    reader.readAsDataURL(file); // Convert the file to a data URL
                   
                }
            }
        }

        // Main function to Upload content and call the Picture and Link and Tags upload functions ----------------------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() { // WORKING

            // Event Listener for uploading content, pictures, tags and links -------------------------------------
            document.getElementById('uploadForm').addEventListener('submit', async function(event) {
                event.preventDefault(); // Prevent default form submission

                const uploadButton = document.getElementById('uploadButton');
                uploadButton.disabled = true;
                uploadButton.textContent = 'Uploading...'; // Optional visual feedback

                let formData = new FormData();
                formData.append('title', document.getElementById('title').value);
                formData.append('upload_date', document.getElementById('upload_date').value);
                formData.append('event_date', document.getElementById('event_date').value);
                formData.append('location', document.getElementById('location').value);
                formData.append('description', document.getElementById('description').value);
                
                let token = localStorage.getItem('token'); // Retrieve token

                try {
                    let response = await fetch('/api/uploads/Upload', { // Initial Uploading content to Uploads Table
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    let result = await response.json();
                    console.log("Uploads Table Response:", result);

                    if (response.ok) {
                        let uploadId = result.data.id; // Getting the Uploads ID after the first Upload is done on Uploads Table // Uploads Table sending result in data body
                        await uploadPictures(uploadId); // PICTURE Function call
                        await uploadPdfs(uploadId); // PDF Function call
                        await uploadLinks(uploadId); // LINK Function call
                        await uploadTags(uploadId); // Tags Function call
                        
                        // Add Latest entry if Yes was selected
                        const expirySection = document.getElementById('latestExpirySection');
                        if (!expirySection.classList.contains('hidden')) {
                            await uploadLatest(uploadId);
                        }
                        
                        alert('Upload successful!');
                        uploadButton.disabled = false;
                        uploadButton.textContent = 'Upload';
                        this.reset(); // Reset form after successful upload
                    } else {
                        alert('Error: ' + (result.message || 'Upload failed'));
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('An error occurred. Please try again.');
                    uploadButton.disabled = false;
                    uploadButton.textContent = 'Upload';
                }
            });

            // Event listeners for validating picture size ------------------------------------------------
            document.querySelector("input[name='pictures']").addEventListener("change", function () {
                validateFileSize(this);
            });

        });

        // Picture, Tags Upload and Link Upload functions where Pictures Table and Links Table APIs are used ----------------------------------------------------------------
        // Picture Upload Function ----------------------------------------------------------
        async function uploadPictures(uploadId) { 
            let pictureInputs = document.querySelectorAll("input[name='pictures']");
            let pictureTitles = document.querySelectorAll("input[name='picture_title']");
            
            for (let i = 0; i < pictureInputs.length; i++) {
                if (pictureInputs[i].files.length === 0) {
                    continue; // Skip if no file is selected
                }

                let formData = new FormData();
                formData.append('uploads_id', uploadId);
                
                // Compress the image before uploading
                const compressedFile = await compressImage(pictureInputs[i].files[0]);
                formData.append('picture_path', compressedFile);
                formData.append('picture_title', pictureTitles[i].value);

                try {
                    let response = await fetch('/api/pictures/Upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    let result = await response.json();
                    console.log("Picture Upload Response:", result);
                } catch (error) {
                    console.error("Picture Upload Error:", error);
                }
            }
        }

        // Link Upload Function -----------------------------------------------------
        // This code works but remove []
        async function uploadLinks(uploadId) {
            let links = document.querySelectorAll("input[name='links']");
            let linkTitles = document.querySelectorAll("input[name='link_titles']");

            for (let i = 0; i < links.length; i++) {

                if (!links[i].value.trim()) {
                    continue; // Skip if the link input is empty
                }

                let formData = new FormData();
                formData.append('uploads_id', uploadId);
                formData.append('link_title', linkTitles[i].value); // Send one link title at a time
                formData.append('link_url', links[i].value); // Send the corresponding link one at a time
                
                try {
                    let response = await fetch('/api/links/Upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                        }
                    });
                    let result = await response.json();
                    console.log("Link Upload Response:", result); // Catches and shows in console Api Responce
                } catch (error) {
                    console.error("Link Upload Error:", error); // Catches and Shows in console the error
                }
            }
        }

        // Tags Upload Function -------------------------------------------------
        async function uploadTags(uploadId) {
            let tagInputs = document.querySelectorAll("input[name='tags']:checked"); // Get checked tags

            if (tagInputs.length === 0) return; // Skip if no tags are selected

            let formData = new FormData();
            formData.append('uploads_id', uploadId);

            tagInputs.forEach(tag => {
                formData.append(tag.value, '1'); // Laravel only accepts "1" as true and "0" as false. Not true, t or anything else // Send each tag as a separate boolean field
            });

            // Debugging: Log all form data before sending
            // for (let pair of formData.entries()) {
            //     console.log(pair[0] + ': ' + pair[1]);
            // }

            try {
                let response = await fetch('/api/tags/Upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    }
                });

                let result = await response.json();
                console.log("Tags Upload Response:", result);
            } catch (error) {
                console.error("Tags Upload Error:", error);
            }
        }

        // Logout
        // Attach event listener to logout button
        // Attach event listener to logout button
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('logoutButton').addEventListener('click', logout);
        });



        // Function to add more PDF fields
        function addPdfField() {
            let container = document.getElementById('pdfsContainer');
            let count = container.querySelectorAll('input[name="pdfs"]').length + 1;

            let timestamp = Date.now();
            let fileInputId = `pdfFileInput_${timestamp}`;
            let previewId = `pdfPreview_${timestamp}`;

            let div = document.createElement('div');
            div.classList.add("flex", "items-center", "space-x-4");
            div.innerHTML = `
                <span class="text-gray-500">${count}.</span>
                    <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="pdf_title" placeholder="PDF Title">
                    <input type="file" name="pdfs" id="${fileInputId}" onchange="validatePdfFileSize(this, '${previewId}')" class="hidden">
                    <label for="${fileInputId}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">Add File</label>
                    <div class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                        <img id="${previewId}" src="" alt="Preview" class="hidden max-w-full max-h-full">
                    </div>
                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.remove()">Remove</button>
            `;
            container.appendChild(div);
        }

        // Function to validate PDF file size and preview
        function validatePdfFileSize(input, previewId) {
            if (input.files.length > 0) {
                let file = input.files[0];
                let maxSize = 10 * 1024 * 1024; // 10MB in bytes
                const preview = document.getElementById(previewId);

                if (file.size > maxSize) {
                    alert("File size exceeds 10MB. Please select a smaller file.");
                    input.value = ""; // Clear the input
                    preview.classList.add('hidden'); // Hide the PDF preview
                } else {
                    // If the file is valid, display the PDF preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden'); // Show the PDF preview
                    };
                    reader.readAsDataURL(file); // Convert the file to a data URL
                }
            }
        }
        
        // PDF Upload Function ----------------------------------------------------------
        async function uploadPdfs(uploadId) {
            let pdfInputs = document.querySelectorAll("input[name='pdfs']");
            let pdfTitles = document.querySelectorAll("input[name='pdf_title']");

            for (let i = 0; i < pdfInputs.length; i++) {
                if (pdfInputs[i].files.length === 0) {
                    continue; // Skip if no file is selected
                }

                let formData = new FormData();
                formData.append('uploads_id', uploadId);
                formData.append('pdf_path', pdfInputs[i].files[0]);
                formData.append('pdf_title', pdfTitles[i].value);

                try {
                    let response = await fetch('/api/pdfs/Upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    let result = await response.json();
                    console.log("PDF Upload Response:", result);
                } catch (error) {
                    console.error("PDF Upload Error:", error);
                }
            }
        }

        // Function to toggle Latest section
        function toggleLatestSection(show) {
            const expirySection = document.getElementById('latestExpirySection');
            const yesButton = document.getElementById('latestYes');
            const noButton = document.getElementById('latestNo');
            
            if (show) {
                expirySection.classList.remove('hidden');
                yesButton.classList.add('bg-green-600');
                noButton.classList.remove('bg-red-600');
            } else {
                expirySection.classList.add('hidden');
                noButton.classList.add('bg-red-600');
                yesButton.classList.remove('bg-green-600');
                document.getElementById('expires_at').value = ''; // Clear the date if hidden
            }
        }

        // Function to upload Latest entry
        async function uploadLatest(uploadId) {
            const expiresAt = document.getElementById('expires_at').value;
            
            let formData = new FormData();
            formData.append('uploads_id', uploadId);
            if (expiresAt) {
                formData.append('expires_at', expiresAt);
            }

            try {
                let response = await fetch('/api/latest/Upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });

                let result = await response.json();
                console.log("Latest Upload Response:", result);
            } catch (error) {
                console.error("Latest Upload Error:", error);
            }
        }

    </script>
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
        <h1 class="text-2xl font-bold mb-8 text-center">Content Upload</h1>

        <form id="uploadForm" enctype="multipart/form-data" class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Title</label>
                            <textarea class="w-full h-24 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" id="title" name="title" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="location">Location</label>
                            <input class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" id="location" name="location" placeholder="Location of the Event">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="upload_date">Upload Date</label>
                            <input class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="date" id="upload_date" name="upload_date" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="event_date">Event Date</label>
                            <input class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="date" id="event_date" name="event_date">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Upload Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Media Upload</h2>
                <div class="grid grid-cols-2 gap-6">
                    <!-- Pictures Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-700">Pictures</h3>
                        <div class="space-y-4" id="picturesContainer">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-500">1.</span>
                                <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="picture_title" placeholder="Picture Title">
                                <input type="file" name="pictures" id="fileInput" onchange="validateFileSize(this, 'imagePreview')" class="hidden">
                                <label for="fileInput" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">Add File</label>
                                <div class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                    <img id="imagePreview" src="" alt="Preview" class="hidden max-w-full max-h-full">
                                </div>
                                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.remove()">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="w-full mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" onclick="addPictureField()">+ Add More Pictures</button>
                    </div>

                    <!-- PDFs Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-700">PDFs</h3>
                        <div class="space-y-4" id="pdfsContainer">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-500">1.</span>
                                <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="pdf_title" placeholder="PDF Title">
                                <input type="file" name="pdfs" id="pdfFileInput" onchange="validatePdfFileSize(this, 'pdfPreview')" class="hidden">
                                <label for="pdfFileInput" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">Add File</label>
                                <div class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                    <img id="pdfPreview" src="" alt="Preview" class="hidden max-w-full max-h-full">
                                </div>
                                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.remove()">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="w-full mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" onclick="addPdfField()">+ Add More PDFs</button>
                    </div>
                </div>
            </div>

            <!-- Links Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Links</h2>
                <div class="space-y-4" id="linksContainer">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-500">1.</span>
                            <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="text" name="link_titles" placeholder="Link Title">
                        </div>
                        <div class="flex items-center space-x-4 ml-8">
                            <input class="flex-1 rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="url" name="links" placeholder="Upload the link">
                            <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="this.parentNode.parentNode.remove()">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="w-full mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" onclick="addLinkField()">+ Add More Links</button>
            </div>

            <!-- Tags Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Tags</h2>
                <div id="tagsContainer" class="flex flex-wrap gap-2">
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="achievement" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Achievement</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="activity_calendar" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Activity Calendar</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="advertisement" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Advertisement</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="awareness_meeting" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Awareness Meeting</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="awareness_program" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Awareness Program</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="juvenile_justice" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Juvenile Justice</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="legal_aid" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Legal Aid</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="legal_assistance" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Legal Assistance</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="lok_adalat" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Lok Adalat</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="legal_literacy_classes" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Legal Literacy Classes</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="mediation" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Mediation</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="monitoring_legal_clinic" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Monitoring Legal Clinic</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="monitoring_jail" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Monitoring Jail</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="meeting" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Meeting</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="notice" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Notice</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="observance" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Observance</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="results" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Results</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="schemes" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Schemes</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="victim_compensation" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Victim Compensation</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="workshop" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Workshop</label>
                    <label class="border border-blue-500 text-blue-700 px-4 py-1 rounded-full cursor-pointer transition-all duration-200"><input type="checkbox" name="tags" value="recruitment" class="hidden" onchange="this.parentElement.classList.toggle('bg-blue-500', this.checked); this.parentElement.classList.toggle('text-white', this.checked); this.parentElement.classList.toggle('text-blue-700', !this.checked);"> Recruitment</label>
                </div>
            </div>

            <!-- Latest Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Latest Section</h2>
                <div class="space-y-4">
                    <p class="text-gray-700">Do you want to add this content to the latest section?</p>
                    <div class="flex space-x-4">
                        <button type="button" id="latestYes" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600" onclick="toggleLatestSection(true)">Yes</button>
                        <button type="button" id="latestNo" class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="toggleLatestSection(false)">No</button>
                    </div>
                    <div id="latestExpirySection" class="hidden space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="expires_at">Expiry Date</label>
                        <input class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" type="date" id="expires_at" name="expires_at">
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Description</h2>
                <textarea id="description" name="description" class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button id="uploadButton" class="px-12 py-4 bg-blue-500 text-white text-lg rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out hover:scale-130" type="submit">Upload</button>
            </div>
        </form>
    </div>

    <script>

    </script>
</body>
</html>
