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
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="{{ asset('functions/logout.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Display Content</title>
    

    <!-- <style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    </style> -->
</head>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener for when the tag filter form is submitted
        document.getElementById('tagFilterForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            let selectedTag = document.getElementById('tagDropdown').value; // Get selected tag
            let resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = ''; // Clear previous results

            let uploads = [];
            let tags = [];
            let pictures = []; 
            let links = [];

            // Fetch all uploads, tags, pictures, links table data
            try {
                let [uploadsRes, tagsRes, picturesRes, linksRes, pdfsRes] = await Promise.all([
                    fetch('/api/uploads/Retrieve'),
                    fetch('/api/tags/Retrieve'),
                    fetch('/api/pictures/Retrieve'),
                    fetch('/api/links/Retrieve'),
                    fetch('/api/pdfs/Retrieve')
                ]);


                // Extract the 'data' property from responses
                let uploadsResponse = await uploadsRes.json();
                let tagsResponse = await tagsRes.json();
                let picturesResponse = await picturesRes.json();
                let linksResponse = await linksRes.json();
                let pdfsResponse = await pdfsRes.json();

                uploads = uploadsResponse.data;
                tags = tagsResponse.data;
                pictures = picturesResponse.data;
                links = linksResponse.data;
                pdfs = pdfsResponse.data;

            } catch (error) {
                console.error('Error fetching data:', error);
                resultsDiv.innerHTML = 'Failed to load content.';
                return;
            }


            // Get all matching upload IDs based on the selected tag
            let matchingUploadIds = tags
                .filter(tag => tag[selectedTag] === true) // Check if the selected tag is true
                .map(tag => tag.uploads_id); // Get the corresponding uploads_id for those tags

            // Filter the uploads based on matching upload IDs
            let filteredUploads = uploads.filter(upload => matchingUploadIds.includes(upload.id));

            // If no uploads match, show a message
            if (filteredUploads.length === 0) {
                resultsDiv.innerHTML = '<p>No matching uploads found.</p>';
                return;
            }



            // DISPLAY
            // Get references to the table and tbody
            let resultsTable = document.getElementById('resultsTable');
            let resultsTableBody = document.getElementById('resultsTableBody');

            // Clear previous results
            resultsTableBody.innerHTML = '';
            resultsTable.style.display = 'none';

            // If no uploads match, show a message
            if (filteredUploads.length === 0) {
                resultsDiv.innerHTML = '<p>No matching uploads found.</p>';
                return;
            }

            // Polulate the rows Upload table
            filteredUploads.forEach(upload => {
                let row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                // Get related pictures for this upload
                let relatedPictures = pictures.filter(p => p.uploads_id === upload.id);
                let picturesHTML = relatedPictures.map(pic => `
                    <div>
                        <strong>${pic.picture_title}</strong><br>
                        <img src="/storage/${pic.picture_path}" alt="Image" style="max-width: 100px; margin-right: 10px;">
                    </div>
                `).join('');

                // Get related PDFs for this upload
                let relatedPdfs = pdfs.filter(p => p.uploads_id === upload.id);
                let pdfsHTML = relatedPdfs.map(pdf => `
                    <div>
                        <strong>${pdf.pdf_title}</strong><br>
                        <a href="/storage/${pdf.pdf_path}" target="_blank" class="text-blue-500 underline">View PDF</a>
                    </div>
                `).join('');

                // Get related links for this upload
                let relatedLinks = links.filter(l => l.uploads_id === upload.id);
                let linksHTML = relatedLinks.map(link => `
                    <div>
                        <strong>${link.link_title}</strong><br>
                        <a href="${link.link_url}" target="_blank">${link.link_url}</a>
                    </div>
                `).join('<br>');

                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 max-w-2xl break-words">${upload.title}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${upload.upload_date}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${upload.event_date || 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${upload.location || 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="view-btn text-blue-600 hover:text-blue-900 mr-4"
                            data-upload='${btoa(unescape(encodeURIComponent(JSON.stringify(upload))))}'
                            data-pictures="${btoa(unescape(encodeURIComponent(JSON.stringify(relatedPictures))))}"
                            data-pdfs="${btoa(unescape(encodeURIComponent(JSON.stringify(relatedPdfs))))}"
                            data-links="${btoa(unescape(encodeURIComponent(JSON.stringify(relatedLinks))))}">View</button>
                        <button class="delete-btn text-red-600 hover:text-red-900" data-upload-id="${upload.id}">Delete</button>
                        
                    </td>
                `;
                resultsTableBody.appendChild(row);
            });

            // Show the table
            resultsTable.style.display = 'table';

            // Add event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const uploadId = this.getAttribute('data-upload-id');

                    try {
                        // Send DELETE request to the back-end to delete the upload
                        let response = await fetch(`/api/uploads/Delete/${uploadId}`, {
                            method: 'DELETE'
                        });

                        let result = await response.json();
                        if (response.ok) {
                            // If successful, remove the row from the table
                            this.closest('tr').remove();
                            alert(result.message); // Optional: Show success message
                        } else {
                            alert('Failed to delete upload');
                        }
                    } catch (error) {
                        console.error('Error deleting upload:', error);
                        alert('An error occurred while deleting the upload.');
                    }
                });
            });


            // View button logic
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const upload = JSON.parse(decodeURIComponent(escape(atob(this.getAttribute('data-upload')))));
                    const pictures = JSON.parse(decodeURIComponent(escape(atob(this.getAttribute('data-pictures')))));
                    const pdfs = JSON.parse(decodeURIComponent(escape(atob(this.getAttribute('data-pdfs')))));
                    const links = JSON.parse(decodeURIComponent(escape(atob(this.getAttribute('data-links')))));

                    let content = `
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-bold mb-2"><span class="font-normal text-gray-500">Title:</span> ${upload.title}</h2>                               
                                <div class="prose max-w-none">
                                    ${upload.description}
                                </div>
                            </div>
                    `;

                    if (pictures.length > 0) {
                        content += `
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Pictures</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    ${pictures.map(pic => `
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="font-medium mb-2">${pic.picture_title}</p>
                                            <img src="/storage/${pic.picture_path}" class="w-full h-auto rounded-lg shadow-sm">
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    if (pdfs.length > 0) {
                        content += `
                            <div>
                                <h3 class="text-lg font-semibold mb-3">PDFs</h3>
                                <div class="space-y-3">
                                    ${pdfs.map(pdf => `
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="font-medium mb-2">${pdf.pdf_title}</p>
                                            <a href="/storage/${pdf.pdf_path}" class="text-blue-600 hover:text-blue-800 underline" target="_blank">View PDF</a>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    if (links.length > 0) {
                        content += `
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Links</h3>
                                <div class="space-y-3">
                                    ${links.map(link => `
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
                });
            });

            
        });
    });

    function closePopup() {
                document.getElementById('viewPopup').classList.add('hidden');
            }

            
    // Logout
    // Attach event listener to logout button
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('logoutButton').addEventListener('click', logout);
    });


</script>


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

<!-- for the sidebar to appear at the left, shift all content to right by 25 -->
<div class="ml-25 mt-25 p-8">





    <h1 class="text-2xl font-bold mb-8 text-center">Filter Content by Tag</h1>

    <div class="flex flex items-center justify-center">
        <form id="tagFilterForm" class="flex flex-row gap-4">
            <!-- <label for="tagDropdown">Select Tag:</label> -->
            <select id="tagDropdown" name="tagDropdown" class="bg-white border border-gray-300 rounded-full px-4 py-2 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:shadow-lg transition-all duration-200">
                <option value="achievement">Achievement</option>
                <option value="activity_calendar">Activity Calendar</option>
                <option value="advertisement">Advertisement</option>
                <option value="awareness_meeting">Awareness Meeting</option>
                <option value="awareness_program">Awareness Program</option>
                <option value="juvenile_justice">Juvenile Justice</option>
                <option value="legal_aid">Legal Aid</option>
                <option value="legal_assistance">Legal Assistance</option>
                <option value="lok_adalat">Lok Adalat</option>
                <option value="legal_literacy_classes">Legal Literacy Classes</option>
                <option value="mediation">Mediation</option>
                <option value="monitoring_legal_clinic">Monitoring Legal Clinic</option>
                <option value="monitoring_jail">Monitoring Jail</option>
                <option value="meeting">Meeting</option>
                <option value="notice">Notice</option>
                <option value="observance">Observance</option>
                <option value="results">Results</option>
                <option value="schemes">Schemes</option>
                <option value="victim_compensation">Victim Compensation</option>
                <option value="workshop">Workshop</option>
                <option value="recruitment">Recruitment</option>
            </select>
            <button type="submit" class="cursor-pointer font-bold bg-[#20FA04] px-4 py-2 text-white rounded-md hover:bg-green-500 cursor-pointer">Submit</button>
        </form>
    </div>





            <div id="results"></div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden mt-5">
                <table id="resultsTable" class="min-w-full divide-y divide-gray-200" style="display: none">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- JavaScript will populate rows here -->
                    </tbody>
                </table>
            </div>

            <!-- Modal -->
            <div id="viewPopup" class="fixed inset-0 backdrop-blur-xs hidden overflow-y-auto h-full w-full pb-20">
                <div class="relative top-20 mx-auto my-10 p-5 border w-4/5 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <button onclick="closePopup()" class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Close</button>
                        <div id="popupContent" class="text-sm space-y-4 mt-8"></div>
                    </div>
                </div>
            </div>

    















</div>
</body>
</html>