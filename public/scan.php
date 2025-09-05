<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Attendance QR</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .message { padding: 20px; margin: 20px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Smart Attendance</h1>
    <div id="message"></div>

    <script>
        
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        if (!navigator.geolocation) {
            showMessage('Geolocation is not supported by your browser.', 'error');
        }
        else if (!token) {
            showMessage('Invalid QR code. No token found.', 'error');
        } 
        else {
   
            navigator.geolocation.getCurrentPosition(
                (position) => {

                    submitAttendance(token, position.coords.latitude, position.coords.longitude);
                },
                (error) => {
                
                    showMessage('Error getting location: ' + error.message, 'error');
                }
            );
        }

        function submitAttendance(token, lat, lon) {

            const formData = new FormData();
            formData.append('token', token);
            formData.append('latitude', lat);
            formData.append('longitude', lon);

            fetch('../api/student_scan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    showMessage('Attendance marked successfully!', 'success');
                } else {
                    showMessage('Error: ' + (data.error || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                showMessage('Network error: ' + error, 'error');
            });
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
        }
    </script>
</body>
</html>