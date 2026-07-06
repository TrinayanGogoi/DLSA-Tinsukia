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
    <title>Latest Section</title>
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
        <h1 class="text-2xl font-bold mb-8 text-center">Latest Section Management</h1>

        <!-- Latest Entries Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="latestTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- JavaScript will populate rows here -->
                </tbody>
            </table>
        </div>

        <!-- View Modal -->
        <div id="viewPopup" class="fixed inset-0 backdrop-blur-xs hidden overflow-y-auto h-full w-full pb-20">
            <div class="relative top-20 mx-auto my-10 p-5 border w-4/5 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <button onclick="closePopup()" class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Close</button>
                    <div id="popupContent" class="text-sm space-y-4 mt-8"></div>
                </div>
            </div>
        </div>

        <!-- Extend Date Modal -->
        <div id="extendDatePopup" class="fixed inset-0 backdrop-blur-xs hidden overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Extend Expiry Date</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="newExpiryDate">New Expiry Date</label>
                            <input type="date" id="newExpiryDate" class="w-full rounded-md border border-gray-300 shadow-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button onclick="closeExtendDatePopup()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                            <button onclick="submitNewDate()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentLatestId = null;

        // Function to load all latest entries
        async function loadLatestEntries() {
            try {
                const response = await fetch('/api/latest/Retrieve');
                const data = await response.json();

                if (data.success) {
                    const tableBody = document.getElementById('latestTableBody');
                    tableBody.innerHTML = '';

                    data.data.forEach(entry => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        // Check if the date is expired
                        const today = new Date();
                        const expiryDate = entry.expires_at ? new Date(entry.expires_at) : null;
                        const isExpired = expiryDate && expiryDate < today;
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${entry.upload.title}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">${entry.upload.upload_date}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm ${isExpired ? 'text-red-600' : 'text-green-600'}">${entry.expires_at || 'No expiry'}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="viewEntry(${entry.id})" class="text-blue-600 hover:text-blue-900 mr-4">View</button>
                                <button onclick="extendDate(${entry.id})" class="text-yellow-600 hover:text-yellow-900 mr-4">Extend Date</button>
                                <button onclick="deleteEntry(${entry.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load latest entries');
            }
        }

        // Function to view entry details
        async function viewEntry(latestId) {
            try {
                const response = await fetch('/api/latest/Retrieve');
                const data = await response.json();

                if (data.success) {
                    const entry = data.data.find(e => e.id === latestId);
                    if (entry) {
                        const upload = entry.upload;
                        let content = `
                            <div class="space-y-6">
                                <div>
                                    <h2 class="text-xl font-bold mb-2"><span class="font-normal text-gray-500">Title:</span> ${upload.title}</h2>
                                    <div class="prose max-w-none">
                                        ${upload.description || 'No description available'}
                                    </div>
                                </div>
                        `;

                        // Add pictures if available
                        if (upload.pictures && upload.pictures.length > 0) {
                            content += `
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Pictures</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        ${upload.pictures.map(pic => `
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <p class="font-medium mb-2">${pic.picture_title}</p>
                                                <img src="/storage/${pic.picture_path}" class="w-full h-auto rounded-lg shadow-sm">
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }

                        // Add PDFs if available
                        if (upload.pdfs && upload.pdfs.length > 0) {
                            content += `
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">PDFs</h3>
                                    <div class="space-y-3">
                                        ${upload.pdfs.map(pdf => `
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <p class="font-medium mb-2">${pdf.pdf_title}</p>
                                                <a href="/storage/${pdf.pdf_path}" class="text-blue-600 hover:text-blue-800 underline" target="_blank">View PDF</a>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }

                        // Add links if available
                        if (upload.links && upload.links.length > 0) {
                            content += `
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Links</h3>
                                    <div class="space-y-3">
                                        ${upload.links.map(link => `
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <p class="font-medium mb-2">${link.link_title}</p>
                                                <a href="${link.link_url}" class="text-blue-600 hover:text-blue-800 underline break-all" target="_blank">${link.link_url}</a>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }

                        content += '</div>';
                        document.getElementById('popupContent').innerHTML = content;
                        document.getElementById('viewPopup').classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load entry details');
            }
        }

        // Function to delete entry
        async function deleteEntry(latestId) {
            if (!confirm('Are you sure you want to remove this entry from the latest section?')) {
                return;
            }

            try {
                const response = await fetch(`/api/latest/Delete/${latestId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (response.ok) {
                    alert('Entry removed from latest section successfully');
                    loadLatestEntries(); // Reload the table
                } else {
                    alert('Failed to remove entry from latest section');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while removing the entry');
            }
        }

        // Function to close popup
        function closePopup() {
            document.getElementById('viewPopup').classList.add('hidden');
        }

        // Function to open extend date modal
        function extendDate(latestId) {
            currentLatestId = latestId;
            document.getElementById('extendDatePopup').classList.remove('hidden');
        }

        // Function to close extend date modal
        function closeExtendDatePopup() {
            document.getElementById('extendDatePopup').classList.add('hidden');
            document.getElementById('newExpiryDate').value = '';
            currentLatestId = null;
        }

        // Function to submit new date
        async function submitNewDate() {
            const newDate = document.getElementById('newExpiryDate').value;
            if (!newDate) {
                alert('Please select a date');
                return;
            }

            try {
                const response = await fetch(`/api/latest/Update/${currentLatestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({
                        expires_at: newDate
                    })
                });

                if (response.ok) {
                    alert('Expiry date updated successfully');
                    closeExtendDatePopup();
                    loadLatestEntries(); // Reload the table
                } else {
                    alert('Failed to update expiry date');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating the date');
            }
        }

        // Load latest entries when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadLatestEntries();
            document.getElementById('logoutButton').addEventListener('click', logout);
        });
    </script>
</body>
</html>