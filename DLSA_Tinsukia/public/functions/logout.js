// public/functions/logout.js
function logout(){
    fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        localStorage.removeItem('token'); // deletes token
        localStorage.removeItem('admin'); // deletes admin details
        sessionStorage.clear(); // Clear session storage
        
        // Prevent browser from storing cached pages
        // window.location.href = '/login';
        window.location.replace('/login'); // Ensures redirect without history
    });
}