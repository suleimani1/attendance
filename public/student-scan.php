<?php 
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['student','admin']); 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <title>Student Scan</title>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2ecc71;
        --dark-blue: #2980b9;
        --dark-green: #27ae60;
        --light-bg: #f5f9fc;
        --text-color: #333;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --header-height: 70px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, var(--light-bg) 0%, #e0f0ff 100%);
        min-height: 100vh;
        color: var(--text-color);
    }

    /* Header Styles */
    header {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 0 20px;
        height: var(--header-height);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    header h2 {
        font-size: 22px;
        font-weight: 600;
    }

    .logout-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    /* Main Content */
    main {
        padding: 30px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
    }

    .card h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Scanner Section */
    .scanner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .scanner-status {
        margin-bottom: 20px;
        font-size: 16px;
        padding: 10px 15px;
        border-radius: 8px;
        background: #f8f9fa;
        width: 100%;
        text-align: center;
    }

    #qr-reader {
        width: 100%;
        max-width: 500px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
    }

    .scanner-controls {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, var(--dark-blue), var(--dark-green));
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background: #f1f1f1;
        color: #555;
    }

    .btn-secondary:hover {
        background: #e5e5e5;
        transform: translateY(-2px);
    }

    /* Summary Section */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }

    .summary-item h4 {
        color: var(--primary-color);
        margin-bottom: 10px;
        font-size: 16px;
    }

    .summary-item p {
        font-size: 18px;
        font-weight: 600;
        color: #444;
    }

    .attendance-percentage {
        font-size: 24px;
        color: var(--secondary-color);
        font-weight: 700;
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    th {
        background: #f8f9fa;
        color: var(--primary-color);
        font-weight: 600;
        position: sticky;
        top: 0;
    }

    tr:hover {
        background: #f8f9fa;
    }

    .status-present {
        color: var(--secondary-color);
        font-weight: 600;
    }

    .status-absent {
        color: #e74c3c;
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        header {
            padding: 0 15px;
        }
        
        header h2 {
            font-size: 18px;
        }
        
        main {
            padding: 20px 15px;
        }
        
        .card {
            padding: 20px;
        }
        
        .scanner-controls {
            flex-direction: column;
            width: 100%;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
        
        .summary-grid {
            grid-template-columns: 1fr;
        }
        
        th, td {
            padding: 10px;
            font-size: 14px;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .loading {
        animation: pulse 1.5s infinite;
        text-align: center;
        padding: 20px;
    }
  </style>
</head>

<body>
  <header>
    <h2><i class="fas fa-qrcode"></i> Scan and Mark Attendance</h2>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </header>

  <main>
    <section class="card">
      <h3><i class="fas fa-camera"></i> QR Code Scanner</h3>
      <div class="scanner-container">
        <div class="scanner-status" id="status">Loading scanner...</div>
        <div id="qr-reader"></div>
        <div class="scanner-controls">
          <button id="start-btn" class="btn btn-primary" onclick="startScanner()">
            <i class="fas fa-play"></i> Start Camera
          </button>
          <button id="stop-btn" class="btn btn-secondary" onclick="stopScanner()" style="display: none;">
            <i class="fas fa-stop"></i> Stop Camera
          </button>
        </div>
      </div>
    </section>

    <section class="card">
      <h3><i class="fas fa-chart-pie"></i> Attendance Summary</h3>
      <div id="summary" class="loading">
        <p>Loading summary...</p>
      </div>
    </section>

    <section class="card">
      <h3><i class="fas fa-history"></i> Your Attendance History</h3>
      <div class="table-container">
        <table id="att">
          <thead>
            <tr>
              <th>ID</th>
              <th>Reg. Number</th>
              <th>Name</th>
              <th>Program</th>
              <th>Course</th>
              <th>Class</th>
              <th>Session</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </main>

<script>
const statusEl = document.getElementById('status');
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
let html5QrcodeScanner = null;

function isScannerLibraryAvailable() {
  return typeof Html5QrcodeScanner !== 'undefined';
}

function startScanner() {
  if (!isScannerLibraryAvailable()) {
    statusEl.textContent = 'Scanner library failed to load. Please refresh the page.';
    statusEl.style.color = 'red';
    return;
  }

  statusEl.textContent = 'Starting camera...';
  statusEl.style.color = 'black';
  statusEl.style.background = '#f8f9fa';

  startBtn.style.display = 'none';
  stopBtn.style.display = 'flex';

  html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader",
    { fps: 10, qrbox: { width: 250, height: 250 } },
    false
  );

  html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function stopScanner() {
  if (html5QrcodeScanner) {
    html5QrcodeScanner.clear().then(() => {
      statusEl.textContent = 'Scanner stopped. Click "Start Camera" to scan again.';
      statusEl.style.background = '#f8f9fa';
      startBtn.style.display = 'flex';
      stopBtn.style.display = 'none';
    }).catch(error => {
      console.error("Failed to stop scanner:", error);
    });
  }
}

function onScanSuccess(decodedText) {
  statusEl.textContent = 'QR detected. Getting location...';
  statusEl.style.background = '#fff3cd';
  stopScanner();

  let token;
  try {
    const url = new URL(decodedText);
    token = url.searchParams.get('token') || decodedText;
  } catch {
    token = decodedText;
  }

  navigator.geolocation.getCurrentPosition(
    async (pos) => {
      await submitAttendance(token, pos.coords.latitude, pos.coords.longitude);
      await loadAttendance();
    },
    (error) => {
      statusEl.textContent = 'Location access denied. Cannot mark attendance.';
      statusEl.style.color = 'white';
      statusEl.style.background = '#e74c3c';
      console.error('Geolocation error:', error);
      startBtn.style.display = 'flex';
    },
    { timeout: 10000, enableHighAccuracy: true }
  );
}

function onScanFailure(error) {
  console.log('Scan error (usually harmless):', error);
}

// submitAttendance - robust parsing and credentials included
async function submitAttendance(token, latitude, longitude) {
  const fd = new FormData();
  fd.append('token', token);
  fd.append('latitude', latitude);
  fd.append('longitude', longitude);

  try {
    const response = await fetch('../api/student_scan.php', {
      method: 'POST',
      credentials: 'include',
      body: fd
    });

    const raw = await response.text();
    // log raw response for debugging (remove once stable)
    console.debug('submit raw response:', raw);

    let data;
    try {
      data = JSON.parse(raw);
    } catch (e) {
      console.error('Invalid JSON response:', raw);
      throw new Error('Invalid response from server');
    }

    if (response.status === 401 || data.error === 'Unauthorized') {
      statusEl.textContent = 'Unauthorized — please refresh and log in again.';
      statusEl.style.color = 'white';
      statusEl.style.background = '#e74c3c';
      console.warn('Submission unauthorized');
      startBtn.style.display = 'flex';
      return;
    }

    if (response.ok && data.ok) {
      statusEl.textContent = 'Attendance marked successfully!';
      statusEl.style.color = 'white';
      statusEl.style.background = '#2ecc71';
    } else {
      statusEl.textContent = 'Error: ' + (data.error || 'Server error');
      statusEl.style.color = 'white';
      statusEl.style.background = '#e74c3c';
      startBtn.style.display = 'flex';
    }
  } catch (error) {
    statusEl.textContent = 'Network/server error. Please try again.';
    statusEl.style.color = 'white';
    statusEl.style.background = '#e74c3c';
    console.error('Submission error:', error);
    startBtn.style.display = 'flex';
  }
}

// loadAttendance - robust, checks for 401 and bad JSON
async function loadAttendance() {
  try {
    const summaryEl = document.getElementById("summary");
    summaryEl.innerHTML = '<div class="loading">Loading attendance data...</div>';
    
    const resp = await fetch('../api/student_attendance.php', {
      credentials: 'include'
    });

    if (resp.status === 401) {
      summaryEl.innerHTML = '<p style="color:red; padding: 15px; background: #fee; border-radius: 8px;"><i class="fas fa-exclamation-triangle"></i> Unauthorized. Please log in again or refresh the page.</p>';
      console.warn('Attendance load returned 401');
      return;
    }

    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);

    const raw = await resp.text();
    console.debug('loadAttendance raw response:', raw);
    let data;
    try {
      data = JSON.parse(raw);
    } catch (e) {
      console.error('Invalid JSON in attendance response:', raw);
      throw new Error('Invalid JSON format');
    }

    const s = data.summary || {};
    summaryEl.innerHTML = `
      <div class="summary-grid">
        <div class="summary-item">
          <h4><i class="fas fa-graduation-cap"></i> Program</h4>
          <p>${s.program || 'N/A'}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-sun"></i> Morning Course</h4>
          <p>${s.morning_course || 'N/A'}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-moon"></i> Evening Course</h4>
          <p>${s.evening_course || 'N/A'}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-chart-bar"></i> Total Classes</h4>
          <p>${s.total || 0}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-check-circle"></i> Present</h4>
          <p>${s.present || 0}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-times-circle"></i> Absent</h4>
          <p>${s.absent || 0}</p>
        </div>
        <div class="summary-item">
          <h4><i class="fas fa-percent"></i> Attendance Percentage</h4>
          <p class="attendance-percentage">${s.percent || 0}%</p>
        </div>
      </div>
    `;

    const tb = document.querySelector('#att tbody');
    tb.innerHTML = '';
    (data.records || []).forEach(x => {
      const tr = document.createElement('tr');
      const statusClass = x.status === 'Present' ? 'status-present' : 'status-absent';
      tr.innerHTML = `
        <td>${x.attendance_id || ''}</td>
        <td>${x.registration_number || ''}</td>
        <td>${x.student_name || ''}</td>
        <td>${x.program || ''}</td>
        <td>${x.course_name || ''}</td>
        <td>${x.class_name || ''}</td>
        <td>${x.session_id || ''}</td>
        <td>${x.timestamp || ''}</td>
        <td class="${statusClass}">${x.status || ''}</td>
      `;
      tb.appendChild(tr);
    });

  } catch (error) {
    console.error("Error loading attendance:", error);
    document.getElementById("summary").innerHTML = '<p style="color:red; padding: 15px; background: #fee; border-radius: 8px;"><i class="fas fa-exclamation-triangle"></i> Failed to load attendance history.</p>';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  if (isScannerLibraryAvailable()) {
    statusEl.textContent = 'Click "Start Camera" to begin scanning';
    statusEl.style.background = '#f8f9fa';
  } else {
    statusEl.textContent = 'Scanner not supported. Please use a modern browser.';
    statusEl.style.color = 'white';
    statusEl.style.background = '#e74c3c';
    startBtn.style.display = 'none';
  }
  loadAttendance();
});
</script>
</body>
</html>