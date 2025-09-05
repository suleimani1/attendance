<?php 
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['admin']); 
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <title>Admin Panel</title>
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

    .input-group input, .input-group select, .input-group textarea {
        width: 100%;
        padding: 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .input-group input:focus, .input-group select:focus, .input-group textarea:focus {
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

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .btn-success {
        background: var(--secondary-color);
        color: white;
    }

    .btn-success:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
    }

    /* Filter Section */
    .filter-toggle {
        background: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-content {
        display: none;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .filter-content.show {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    /* Export Links */
    .export-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .export-link {
        padding: 12px 20px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .export-link:hover {
        background: var(--dark-blue);
        transform: translateY(-2px);
    }

    .export-link.pdf {
        background: #e74c3c;
    }

    .export-link.pdf:hover {
        background: #c0392b;
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
        
        .form-grid, .filter-content.show {
            grid-template-columns: 1fr;
        }
        
        .export-links {
            flex-direction: column;
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

    /* Summary Styles */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
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
  </style>
</head>
<body>
  <button class="menu-toggle" onclick="toggleSidebar()" style="display: none;">
    <i class="fas fa-bars"></i>
  </button>

  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3><i class="fas fa-user-shield"></i> <span>Admin Panel</span></h3>
    </div>
    <div class="sidebar-menu">
      <div class="menu-item active" data-target="attendance-search">
        <i class="fas fa-search"></i> <span>Attendance Search</span>
      </div>
      <div class="menu-item" data-target="export-data">
        <i class="fas fa-file-export"></i> <span>Export Data</span>
      </div>
      <div class="menu-item" data-target="manage-users">
        <i class="fas fa-users-cog"></i> <span>Manage Users</span>
      </div>
      <div class="menu-item" data-target="add-user">
        <i class="fas fa-user-plus"></i> <span>Add User</span>
      </div>
    </div>
  </div>

  <header>
    <h2 id="page-title">Attendance Search</h2>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </header>

  <div class="main-content">
    <!-- Attendance Search -->
    <section class="card active" id="attendance-search">
      <h3><i class="fas fa-search"></i> Search Attendance Records</h3>
      
      <div class="filter-toggle" onclick="toggleFilters()">
        <span><i class="fas fa-filter"></i> Advanced Filters</span>
        <i class="fas fa-chevron-down" id="filter-arrow"></i>
      </div>
      
      <div class="filter-content" id="filter-content">
        <div class="input-group">
          <label for="date_from">Date From</label>
          <input type="date" id="date_from" name="date_from">
        </div>
        <div class="input-group">
          <label for="date_to">Date To</label>
          <input type="date" id="date_to" name="date_to">
        </div>
        <div class="input-group">
          <label for="status_filter">Status</label>
          <select id="status_filter" name="status">
            <option value="">All Statuses</option>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
          </select>
        </div>
      </div>
      
      <form onsubmit="loadAtt(event)">
        <div class="input-group">
          <label for="course_name">Course Name</label>
          <input name="course_name" id="course_name" type="text" placeholder="Enter course name">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
        <button type="button" class="btn btn-secondary" onclick="clearFilters()"><i class="fas fa-times"></i> Clear Filters</button>
      </form>
      
      <div id="attendance-summary" class="summary-grid"></div>
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

    <!-- Export -->
    <section class="card" id="export-data">
      <h3><i class="fas fa-file-export"></i> Export Data</h3>
      <p>Export attendance data in different formats:</p>
      <div class="export-links">
        <a href="../api/admin_export_csv.php" target="_blank" class="export-link">
          <i class="fas fa-file-csv"></i> Download CSV
        </a>
        <a href="../api/admin_export_pdf.php" target="_blank" class="export-link pdf">
          <i class="fas fa-file-pdf"></i> Download PDF
        </a>
      </div>
    </section>

    <!-- Users Management -->
    <section class="card" id="manage-users">
      <h3><i class="fas fa-users-cog"></i> Manage Users</h3>
      <button class="btn btn-secondary" onclick="loadUsers()"><i class="fas fa-sync-alt"></i> Load Users</button>
      <div class="table-container">
        <table id="users">
          <thead>
            <tr>
              <th>ID</th>
              <th>Reg. Number</th>
              <th>Name</th>
              <th>Program</th>
              <th>Morning Course</th>
              <th>Evening Course</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>

    <!-- Add Teacher/Admin -->
    <section class="card" id="add-user">
      <h3><i class="fas fa-user-plus"></i> Add Teacher/Admin</h3>
      <form id="addUserForm" onsubmit="addUser(event)">
        <div class="form-grid">
          <div class="input-group">
            <label for="registration_number">Registration Number</label>
            <input type="text" name="registration_number" id="registration_number" placeholder="Enter registration number" required>
          </div>
          <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" placeholder="Enter full name" required>
          </div>
          <div class="input-group">
            <label for="program">Program</label>
            <input type="text" name="program" id="program" placeholder="Enter program (optional)">
          </div>
          <div class="input-group">
            <label for="morning_course">Morning Course</label>
            <input type="text" name="morning_course" id="morning_course" placeholder="Enter morning course (optional)">
          </div>
          <div class="input-group">
            <label for="evening_course">Evening Course</label>
            <input type="text" name="evening_course" id="evening_course" placeholder="Enter evening course (optional)">
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required>
          </div>
          <div class="input-group">
            <label for="role">Role</label>
            <select name="role" id="role" required>
              <option value="">-- Select Role --</option>
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add User</button>
      </form>
      <div id="addUserMsg"></div>
    </section>
  </div>

  <!-- Popup Message Container -->
  <div id="popup" class="popup">
    <i class="fas fa-info-circle"></i>
    <span id="popup-message"></span>
  </div>

<script>
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

// Toggle advanced filters
function toggleFilters() {
  const filterContent = document.getElementById('filter-content');
  const filterArrow = document.getElementById('filter-arrow');
  
  filterContent.classList.toggle('show');
  filterArrow.classList.toggle('fa-chevron-down');
  filterArrow.classList.toggle('fa-chevron-up');
}

// Clear all filters
function clearFilters() {
  document.getElementById('course_name').value = '';
  document.getElementById('date_from').value = '';
  document.getElementById('date_to').value = '';
  document.getElementById('status_filter').value = '';
  document.querySelector('#att tbody').innerHTML = '';
  document.getElementById('attendance-summary').innerHTML = '';
  showPopup('Filters cleared', 'info');
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

// Attendance Search + Summary
async function loadAtt(e){
  e.preventDefault();
  const course_name = document.getElementById('course_name').value;
  const date_from = document.getElementById('date_from').value;
  const date_to = document.getElementById('date_to').value;
  const status = document.getElementById('status_filter').value;
  
  // Build query string
  const params = new URLSearchParams();
  if (course_name) params.append('course_name', course_name);
  if (date_from) params.append('date_from', date_from);
  if (date_to) params.append('date_to', date_to);
  if (status) params.append('status', status);
  
  const qs = params.toString();
  const tbody = document.querySelector('#att tbody');
  tbody.innerHTML = '<tr><td colspan="9" class="loading">Loading attendance data...</td></tr>';

  try {
    // Fetch summary
    const summaryRes = await fetch('../api/admin_attendance_summary.php?' + qs);
    const summaryData = await summaryRes.json();
    
    document.getElementById('attendance-summary').innerHTML = `
      <div class="summary-item">
        <h4><i class="fas fa-calendar-alt"></i> Total Sessions</h4>
        <p>${summaryData.total || 0}</p>
      </div>
      <div class="summary-item">
        <h4><i class="fas fa-check-circle"></i> Present</h4>
        <p>${summaryData.present || 0}</p>
      </div>
      <div class="summary-item">
        <h4><i class="fas fa-times-circle"></i> Absent</h4>
        <p>${summaryData.absent || 0}</p>
      </div>
      <div class="summary-item">
        <h4><i class="fas fa-percent"></i> Percentage</h4>
        <p class="attendance-percentage">${summaryData.percent || 0}%</p>
      </div>
    `;

    // Fetch attendance list
    const r = await fetch('../api/admin_list_attendance.php?' + qs);
    const rows = await r.json();
    tbody.innerHTML = '';

    if (rows.length === 0) {
      tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:20px">No attendance records found</td></tr>';
      showPopup('No attendance records found with current filters', 'info');
    } else {
      rows.forEach(x => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${x.attendance_id}</td>
          <td>${x.registration_number}</td>
          <td>${x.student_name}</td>
          <td>${x.program}</td>
          <td>${x.course_name}</td>
          <td>${x.class_name}</td>
          <td>${x.session_id}</td>
          <td>${x.timestamp}</td>
          <td>
            <select onchange="updateAttendance(${x.attendance_id}, this.value)" class="${x.status === 'present' ? 'status-present' : 'status-absent'}">
              <option value="present" ${x.status==='present'?'selected':''}>Present</option>
              <option value="absent" ${x.status==='absent'?'selected':''}>Absent</option>
            </select>
          </td>
        `;
        tbody.appendChild(tr);
      });
      showPopup(`Found ${rows.length} attendance records`, 'success');
    }
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:20px;color:red;">Error loading attendance data</td></tr>';
    showPopup('Error loading attendance data', 'error');
  }
}

async function updateAttendance(id, status){
  const fd = new FormData();
  fd.append('attendance_id', id);
  fd.append('status', status);
  
  try {
    const r = await fetch('../api/admin_update_attendance.php', { method: 'POST', body: fd });
    if (r.ok) {
      showPopup('Attendance updated successfully', 'success');
    } else {
      showPopup('Failed to update attendance', 'error');
    }
  } catch (error) {
    showPopup('Network error. Please try again.', 'error');
  }
}

// Users management
async function loadUsers(){
  const tb = document.querySelector('#users tbody');
  tb.innerHTML = '<tr><td colspan="8" class="loading">Loading users...</td></tr>';
  
  try {
    const r = await fetch('../api/admin_list_users.php');
    const rows = await r.json();
    tb.innerHTML = '';
    
    if (rows.length === 0) {
      tb.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px">No users found</td></tr>';
    } else {
      rows.forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${u.user_id}</td>
          <td>${u.registration_number}</td>
          <td>${u.name}</td>
          <td>${u.program||''}</td>
          <td>${u.morning_course||''}</td>
          <td>${u.evening_course||''}</td>
          <td>${u.role}</td>
          <td>
            <button class="btn btn-success" onclick="resetPassword(${u.user_id})"><i class="fas fa-key"></i> Reset Password</button>
            <button class="btn btn-danger" onclick="deleteUser(${u.user_id})"><i class="fas fa-trash"></i> Delete</button>
          </td>
        `;
        tb.appendChild(tr);
      });
    }
  } catch (error) {
    tb.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:red;">Error loading users</td></tr>';
    showPopup('Error loading users', 'error');
  }
}

async function resetPassword(uid){
  const newPass = prompt("Enter new password for user ID " + uid);
  if(!newPass) return;
  
  const fd = new FormData();
  fd.append('user_id', uid);
  fd.append('new_password', newPass);
  
  try {
    const r = await fetch('../api/admin_reset_password.php', { method: 'POST', body: fd });
    if (r.ok) {
      showPopup('Password reset successfully', 'success');
    } else {
      showPopup('Failed to reset password', 'error');
    }
  } catch (error) {
    showPopup('Network error. Please try again.', 'error');
  }
}

async function deleteUser(uid){
  if(!confirm("Are you sure you want to delete user ID " + uid + "? This action cannot be undone.")) return;
  
  const fd = new FormData();
  fd.append('user_id', uid);
  
  try {
    const r = await fetch('../api/admin_delete_user.php', { method: 'POST', body: fd });
    if (r.ok) {
      showPopup('User deleted successfully', 'success');
      loadUsers();
    } else {
      showPopup('Failed to delete user', 'error');
    }
  } catch (error) {
    showPopup('Network error. Please try again.', 'error');
  }
}

async function addUser(e){
  e.preventDefault();
  const fd = new FormData(e.target);
  const msgEl = document.getElementById('addUserMsg');
  msgEl.textContent = 'Adding user...';
  msgEl.className = 'message';
  
  try {
    const r = await fetch('../api/admin_create_teacher.php', { method: 'POST', body: fd });
    const d = await r.json();
    
    if (r.ok) {
      msgEl.textContent = 'User created successfully!';
      msgEl.className = 'message message-success';
      showPopup('User created successfully', 'success');
      loadUsers();
      e.target.reset();
    } else {
      msgEl.textContent = (d.error || 'Failed to create user');
      msgEl.className = 'message message-error';
      showPopup(d.error || 'Failed to create user', 'error');
    }
  } catch (error) {
    msgEl.textContent = 'Network error. Please try again.';
    msgEl.className = 'message message-error';
    showPopup('Network error. Please try again.', 'error');
  }
}

// Load users when page loads
document.addEventListener('DOMContentLoaded', function() {
  loadUsers();
  
  // Set default date range to current month
  const today = new Date();
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
  const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
  
  document.getElementById('date_from').value = formatDate(firstDay);
  document.getElementById('date_to').value = formatDate(lastDay);
});

// Helper function to format date as YYYY-MM-DD
function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
</script>
</body>
</html>