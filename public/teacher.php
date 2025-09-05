<?php 
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['teacher']); 
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <title>Teacher Panel</title>
  <style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2ecc71;
        --dark-blue: #2980b9;
        --dark-green: #27ae60;
        --light-bg: #f5f9fc;
        --text-color: #333;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --sidebar-width: 250px;
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
        display: flex;
    }

    /* Sidebar Styles */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
        color: white;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
        transition: all 0.3s;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 25px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h3 {
        font-size: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .sidebar-menu {
        padding: 20px 0;
    }

    .menu-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .menu-item:hover, .menu-item.active {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: white;
    }

    .menu-item i {
        width: 20px;
        text-align: center;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        padding: 20px;
        padding-top: calc(var(--header-height) + 20px);
    }

    /* Header Styles */
    header {
        background: white;
        color: var(--text-color);
        padding: 0 30px;
        height: var(--header-height);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        right: 0;
        left: var(--sidebar-width);
        z-index: 100;
    }

    header h2 {
        font-size: 22px;
        font-weight: 600;
    }

    .logout-btn {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .logout-btn:hover {
        background: linear-gradient(to right, var(--dark-blue), var(--dark-green));
        transform: translateY(-2px);
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        display: none;
    }

    .card.active {
        display: block;
    }

    .card h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Form Styles */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
    }

    .input-group input, .input-group select {
        width: 100%;
        padding: 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .input-group input:focus, .input-group select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }

    .btn {
        padding: 14px 25px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
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

    /* Location Button Styles */
    .location-btn {
        background: #95a5a6;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
    }

    .location-btn:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .location-btn i {
        font-size: 12px;
    }

    /* Message Styles */
    .message {
        padding: 12px 15px;
        border-radius: 8px;
        margin-top: 15px;
        font-weight: 500;
    }

    .message-success {
        background: #d4edda;
        color: #155724;
    }

    .message-error {
        background: #f8d7da;
        color: #721c24;
    }

    /* QR Code Styles */
    .qr-container {
        text-align: center;
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .qr-container img {
        max-width: 100%;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 15px;
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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

    /* Popup Message */
    .popup {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    }

    .popup.show {
        transform: translateX(0);
    }

    .popup.success {
        background: var(--secondary-color);
    }

    .popup.error {
        background: #e74c3c;
    }

    .popup.info {
        background: var(--primary-color);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .sidebar {
            width: 70px;
            overflow: visible;
        }
        
        .sidebar-header h3 span, .menu-item span {
            display: none;
        }
        
        .main-content, header {
            margin-left: 70px;
        }
        
        .sidebar:hover {
            width: var(--sidebar-width);
        }
        
        .sidebar:hover .sidebar-header h3 span, 
        .sidebar:hover .menu-item span {
            display: inline;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            overflow: hidden;
        }
        
        .main-content, header {
            margin-left: 0;
        }
        
        .menu-toggle {
            display: block;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .sidebar.open {
            width: var(--sidebar-width);
        }
        
        .sidebar.open .sidebar-header h3 span, 
        .sidebar.open .menu-item span {
            display: inline;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
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
  <button class="menu-toggle" onclick="toggleSidebar()" style="display: none;">
    <i class="fas fa-bars"></i>
  </button>

  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3><i class="fas fa-chalkboard-teacher"></i> <span>Teacher Panel</span></h3>
    </div>
    <div class="sidebar-menu">
      <div class="menu-item active" data-target="create-class">
        <i class="fas fa-plus-circle"></i> <span>Create Class</span>
      </div>
      <div class="menu-item" data-target="create-session">
        <i class="fas fa-qrcode"></i> <span>Create Session</span>
      </div>
      <div class="menu-item" data-target="view-attendance">
        <i class="fas fa-list-alt"></i> <span>View Attendance</span>
      </div>
      <div class="menu-item" data-target="manual-mark">
        <i class="fas fa-user-check"></i> <span>Manual Mark</span>
      </div>
      <div class="menu-item" data-target="list-classes">
        <i class="fas fa-book"></i> <span>My Classes</span>
      </div>
    </div>
  </div>

  <header>
    <h2 id="page-title">Create Class</h2>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </header>

  <div class="main-content">
    <!-- Create Class -->
    <section class="card active" id="create-class">
      <h3><i class="fas fa-plus-circle"></i> Create Class</h3>
      <form id="fClass" onsubmit="createClass(event)">
        <div class="form-grid">
          <div class="input-group">
            <label for="class_name">Class Name</label>
            <input name="class_name" id="class_name" placeholder="Enter class name" required>
          </div>
          <div class="input-group">
            <label for="course_name">Course Name</label>
            <input name="course_name" id="course_name" placeholder="Enter course name" required>
          </div>
          <div class="input-group">
            <label for="latitude">Latitude</label>
            <input name="latitude" id="latitude" type="number" step="0.000001" placeholder="Enter latitude" required>
          </div>
          <div class="input-group">
            <label for="longitude">Longitude</label>
            <input name="longitude" id="longitude" type="number" step="0.000001" placeholder="Enter longitude" required>
          </div>
          <div class="input-group">
            <label for="radius">Allowed Radius (meters)</label>
            <input name="radius" id="radius" type="number" min="1" max="1000" value="100" 
                   placeholder="Enter allowed radius in meters" required>
            <small style="font-size: 12px; color: #666;">Distance in meters from class location where students can mark attendance</small>
          </div>
          <div class="input-group">
            <label for="schedule">Schedule</label>
            <input name="schedule" id="schedule" type="datetime-local" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create Class</button>
      </form>
      <div id="classMsg" class="message"></div>
    </section>

    <!-- Create Session and QR -->
    <section class="card" id="create-session">
      <h3><i class="fas fa-qrcode"></i> Create Session and QR</h3>
      <form id="fSession" onsubmit="createSession(event)">
        <div class="form-grid">
          <div class="input-group">
            <label for="session_course">Course Name</label>
            <input name="course_name" id="session_course" type="text" placeholder="Enter course name" required>
          </div>
          <div class="input-group">
            <label for="session_date">Session Date</label>
            <input name="session_date" id="session_date" type="date" required>
          </div>
          <div class="input-group">
            <label for="session_time">Session Time</label>
            <input name="session_time" id="session_time" type="time" required>
          </div>
          <div class="input-group">
            <label for="ttl_minutes">TTL (Minutes)</label>
            <input name="ttl_minutes" id="ttl_minutes" type="number" value="10" min="1">
          </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-qrcode"></i> Create Session</button>
      </form>
      <div id="qrWrap" class="qr-container"></div>
    </section>

    <!-- View Attendance -->
    <section class="card" id="view-attendance">
      <h3><i class="fas fa-list-alt"></i> View Attendance</h3>
      <form id="fView" onsubmit="viewAttendance(event)">
        <div class="input-group">
          <label for="view_course">Course Name</label>
          <input name="course_name" id="view_course" type="text" placeholder="Enter course name" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load Attendance</button>
      </form>
      <div class="table-container">
        <table id="attTable">
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

    <!-- Manual Mark -->
    <section class="card" id="manual-mark">
      <h3><i class="fas fa-user-check"></i> Manual Mark Attendance</h3>
      <form id="fManual" onsubmit="manualMark(event)">
        <div class="form-grid">
          <div class="input-group">
            <label for="reg_number">Student Registration Number</label>
            <input name="registration_number" id="reg_number" type="text" placeholder="Enter registration number" required>
          </div>
          <div class="input-group">
            <label for="manual_course">Course Name</label>
            <input name="course_name" id="manual_course" type="text" placeholder="Enter course name" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle"></i> Mark Student Present</button>
      </form>
      <div id="manualMsg" class="message"></div>
    </section>

    <!-- List of Classes -->
    <section class="card" id="list-classes">
      <h3><i class="fas fa-book"></i> Your Classes</h3>
      <button class="btn btn-secondary" onclick="loadClasses()"><i class="fas fa-sync-alt"></i> Load Classes</button>
      <div class="table-container">
        <table id="classesTable">
          <thead>
            <tr>
              <th>Class ID</th>
              <th>Class Name</th>
              <th>Course Name</th>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Radius (m)</th>
              <th>Schedule</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </div>

  <!-- Popup Message Container -->
  <div id="popup" class="popup">
    <i class="fas fa-info-circle"></i>
    <span id="popup-message"></span>
  </div>

<script>
const API = '../api/teacher_api.php';

// Show popup message
function showPopup(message, type = 'info') {
  const popup = document.getElementById('popup');
  const messageEl = document.getElementById('popup-message');
  
  messageEl.textContent = message;
  popup.className = `popup ${type}`;
  popup.classList.add('show');
  
  // Hide after 3 seconds
  setTimeout(() => {
    popup.classList.remove('show');
  }, 3000);
}

// Get current location using browser geolocation
function getCurrentLocation() {
  if (navigator.geolocation) {
    showPopup('Getting your location...', 'info');
    
    navigator.geolocation.getCurrentPosition(
      (position) => {
        document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
        document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
        showPopup('Location detected successfully!', 'success');
      },
      (error) => {
        let errorMessage = 'Unable to get your location. Please enter manually.';
        switch(error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = 'Location access denied. Please allow location access or enter coordinates manually.';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = 'Location information unavailable. Please enter coordinates manually.';
            break;
          case error.TIMEOUT:
            errorMessage = 'Location request timed out. Please try again or enter coordinates manually.';
            break;
        }
        showPopup(errorMessage, 'error');
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000
      }
    );
  } else {
    showPopup('Geolocation is not supported by your browser. Please enter coordinates manually.', 'error');
  }
}

// Navigation functionality
document.querySelectorAll('.menu-item').forEach(item => {
  item.addEventListener('click', function() {
    // Update active menu item
    document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
    this.classList.add('active');
    
    // Show corresponding card
    const target = this.getAttribute('data-target');
    document.querySelectorAll('.card').forEach(card => card.classList.remove('active'));
    document.getElementById(target).classList.add('active');
    
    // Update page title
    document.getElementById('page-title').textContent = this.querySelector('span').textContent;
  });
});

// Toggle sidebar on mobile
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Check if mobile view and show toggle button
function checkMobileView() {
  if (window.innerWidth <= 768) {
    document.querySelector('.menu-toggle').style.display = 'flex';
    document.getElementById('sidebar').classList.remove('open');
  } else {
    document.querySelector('.menu-toggle').style.display = 'none';
    document.getElementById('sidebar').classList.remove('open');
  }
}

window.addEventListener('resize', checkMobileView);
checkMobileView();

// Add location buttons to latitude and longitude inputs
document.addEventListener('DOMContentLoaded', function() {
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  
  if (latInput && lngInput) {
    // Create location button for latitude
    const latLocationButton = document.createElement('button');
    latLocationButton.type = 'button';
    latLocationButton.className = 'location-btn';
    latLocationButton.innerHTML = '<i class="fas fa-location-crosshairs"></i> Get My Location';
    latLocationButton.onclick = getCurrentLocation;
    latLocationButton.style.marginTop = '8px';
    
    // Add button after latitude input
    latInput.parentNode.appendChild(latLocationButton);
  }
});

// ---- CREATE CLASS (using the same API) ----
async function createClass(e) {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action', 'create_class'); // Add action parameter
  
  const msgEl = document.getElementById('classMsg');
  msgEl.textContent = 'Creating class...';
  msgEl.className = 'message';
  
  try {
    const r = await fetch(API, {
      method: 'POST',
      body: fd
    });
    
    const data = await r.json();
    if (data.success) {
      msgEl.textContent = 'Class created successfully';
      msgEl.className = 'message message-success';
      showPopup('Class created successfully', 'success');
      e.target.reset(); // Clear the form
      loadClasses(); // Refresh the classes list
    } else {
      msgEl.textContent = 'Error: ' + (data.error || 'Unknown error');
      msgEl.className = 'message message-error';
      showPopup(data.error || 'Failed to create class', 'error');
    }
  } catch (error) {
    msgEl.textContent = 'Network error. Please try again.';
    msgEl.className = 'message message-error';
    showPopup('Network error. Please try again.', 'error');
  }
}

// ---- CREATE SESSION BY COURSE ----
async function createSession(e) {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action', 'create_session_by_course');

  try {
    const r = await fetch(API, { method: 'POST', body: fd });
    const data = await r.json();
    
    if (data.success) {
      const token = data.data.qr_token;
      const scanUrl = `${location.origin}/smart1/api/student_scan.php?token=${encodeURIComponent(token)}`;
      const qrUrl   = `qr.php?token=${encodeURIComponent(token)}`;
      
      document.getElementById('qrWrap').innerHTML = `
        <p><b>QR URL:</b> 
          <input type="text" value="${scanUrl}" readonly 
                 style="width: 100%; padding: 8px; border: 1px solid #ddd; 
                        border-radius: 4px; margin: 10px 0;">
        </p>
        
        <img src="${qrUrl}" alt="QR Code" 
             style="max-width: 260px; border: 1px solid #ccc; border-radius: 12px;"/>
        
        <p style="margin-top: 10px; font-size: 14px;">
          Scan this QR code with the student app to mark attendance
        </p>

        <!--  Download button -->
        <a href="${qrUrl}" download="attendance_qr.png"
           class="btn btn-primary" style="margin-top:15px; display:inline-block;">
          <i class="fas fa-download"></i> Download QR
        </a>  
      `;
      
      showPopup('Session created successfully', 'success');
      e.target.reset(); // Clear the form
    } else {
      showPopup(data.error || 'Failed to create session', 'error');
    }
  } catch (error) {
    showPopup('Network error. Please try again.', 'error');
  }
}

// ---- VIEW ATTENDANCE BY COURSE ----
async function viewAttendance(e) {
  e.preventDefault();
  const course = e.target.course_name.value.trim();
  if (!course) {
    showPopup('Please enter a course name', 'error');
    return;
  }
  
  const tbody = document.querySelector('#attTable tbody');
  tbody.innerHTML = '<tr><td colspan="9" class="loading">Loading attendance data...</td></tr>';
  
  try {
    const r = await fetch(`${API}?action=list_attendance_by_course&course_name=${encodeURIComponent(course)}`);
    const data = await r.json();
    tbody.innerHTML = '';
    
    if (data.success) {
      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:20px">No attendance records found</td></tr>';
        showPopup('No attendance records found for this course', 'info');
      } else {
        data.data.forEach(x => {
          const tr = document.createElement('tr');
          const statusClass = x.status === 'Present' ? 'status-present' : 'status-absent';
          tr.innerHTML = `
            <td>${x.attendance_id}</td>
            <td>${x.registration_number}</td>
            <td>${x.student_name}</td>
            <td>${x.program}</td>
            <td>${x.course_name}</td>
            <td>${x.class_name}</td>
            <td>${x.session_id}</td>
            <td>${x.timestamp}</td>
            <td class="${statusClass}">${x.status}</td>
          `;
          tbody.appendChild(tr);
        });
        showPopup(`Loaded ${data.data.length} attendance records`, 'success');
      }
    } else {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:20px;color:red;">Error: ${data.error}</td></tr>`;
      showPopup(data.error || 'Failed to load attendance', 'error');
    }
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:20px;color:red;">Network error. Please try again.</td></tr>';
    showPopup('Network error. Please try again.', 'error');
  }
}

// ---- MANUAL MARK PRESENT ----
async function manualMark(e) {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action', 'mark_present_by_course');
  
  const msgEl = document.getElementById('manualMsg');
  msgEl.textContent = 'Marking attendance...';
  msgEl.className = 'message';
  
  try {
    const r = await fetch(API, { method: 'POST', body: fd });
    const data = await r.json();
    
    if (data.success) {
      msgEl.textContent = "Student marked present successfully";
      msgEl.className = 'message message-success';
      showPopup('Student marked present successfully', 'success');
      e.target.reset(); // Clear the form
    } else {
      msgEl.textContent = `Error: ${data.error}`;
      msgEl.className = 'message message-error';
      showPopup(data.error || 'Failed to mark attendance', 'error');
    }
  } catch (error) {
    msgEl.textContent = 'Network error. Please try again.';
    msgEl.className = 'message message-error';
    showPopup('Network error. Please try again.', 'error');
  }
}

// ---- LIST CLASSES ----
async function loadClasses() {
  const tbody = document.querySelector('#classesTable tbody');
  tbody.innerHTML = '<tr><td colspan="7" class="loading">Loading classes...</td></tr>';
  
  try {
    const r = await fetch(`${API}?action=list_classes`);
    const data = await r.json();
    tbody.innerHTML = '';
    
    if (data.success) {
      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px">No classes found</td></tr>';
        showPopup('No classes found', 'info');
      } else {
        data.data.forEach(c => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${c.class_id}</td>
            <td>${c.class_name}</td>
            <td>${c.course_name}</td>
            <td>${c.latitude}</td>
            <td>${c.longitude}</td>
            <td>${c.radius}</td>
            <td>${c.schedule}</td>
          `;
          tbody.appendChild(tr);
        });
        showPopup(`Loaded ${data.data.length} classes`, 'success');
      }
    } else {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:20px;color:red;">Error: ${data.error}</td></tr>`;
      showPopup(data.error || 'Failed to load classes', 'error');
    }
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:red;">Network error. Please try again.</td></tr>';
    showPopup('Network error. Please try again.', 'error');
  }
}

// Load classes when page loads
document.addEventListener('DOMContentLoaded', function() {
  loadClasses();
});
</script>
</body>
</html>