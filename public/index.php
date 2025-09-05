<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<title>Login</title>
<style>
:root {
    --primary-color: #3498db;
    --secondary-color: #2ecc71;
    --dark-blue: #2980b9;
    --dark-green: #27ae60;
    --light-bg: #f5f9fc;
    --text-color: #333;
    --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.login-container {
    display: flex;
    width: 900px;
    height: 500px;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
}

.brand-section {
    flex: 1;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.brand-logo {
    width: 120px;
    height: 120px;
    background: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
    font-size: 40px;
    color: var(--primary-color);
}

.brand-section h1 {
    font-size: 28px;
    margin-bottom: 15px;
    font-weight: 600;
}

.brand-section p {
    font-size: 16px;
    opacity: 0.9;
    line-height: 1.6;
}

.login-section {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h2 {
    color: var(--primary-color);
    font-size: 28px;
    margin-bottom: 10px;
}

.login-header p {
    color: #777;
    font-size: 15px;
}

.login-form {
    width: 100%;
}

.input-group {
    margin-bottom: 20px;
    position: relative;
}

.input-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.input-group input {
    width: 100%;
    padding: 15px 15px 15px 45px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.input-group input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

.login-button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.login-button:hover {
    background: linear-gradient(to right, var(--dark-blue), var(--dark-green));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.register-link {
    text-align: center;
    margin-top: 20px;
    color: #777;
    font-size: 15px;
}

.register-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.register-link a:hover {
    text-decoration: underline;
}

#msg {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
    padding: 10px;
    border-radius: 5px;
}

@media (max-width: 768px) {
    .login-container {
        flex-direction: column;
        height: auto;
        width: 100%;
        max-width: 400px;
    }
    
    .brand-section {
        padding: 30px 20px;
    }
    
    .login-section {
        padding: 30px 20px;
    }
}
</style>
</head>
<body>
<div class="login-container">
    <div class="brand-section">
        <div class="brand-logo">
            <!-- <i class="fas fa-graduation-cap"></i> -->
             <i class="fa-solid fa-qrcode"></i>
        </div>
        <h1>Smart Attendance</h1>
        <p></p>
    </div>
    
    <div class="login-section">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to continue to your account</p>
        </div>
        
        <form class="login-form" method="post" action="../api/auth_login.php" onsubmit="return login(event)">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input name="registration_number" type="text" placeholder="Registration Number" required/>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input name="password" type="password" placeholder="Password" required/>
            </div>
            
            <button class="login-button">Login</button>
            
            <p id="msg"></p>
            
            <div class="register-link">
                <p>Don't have an account? <a href="register.html">Register here</a></p>
            </div>
        </form>
    </div>
</div>

<script>
async function login(e){
  e.preventDefault();
  const fd = new FormData(e.target);
  
  try {
    const r = await fetch('../api/auth_login.php', {
      method: 'POST',
      body: fd
    });
    
    const d = await r.json(); 
    
    if (r.ok) {
      document.getElementById('msg').textContent = 'Logged in successfully';
      document.getElementById('msg').style.color = 'green';
      
      // Redirect based on role
      const role = d.role;
      setTimeout(() => {
        location.href = role === 'teacher' ? 'teacher.php' : 
                        role === 'admin' ? 'admin.php' : 
                        'student-scan.php';
      }, 1000);
    } else {
      document.getElementById('msg').textContent = d.error || 'Login failed';
      document.getElementById('msg').style.color = 'red';
    }
  } catch (error) {
    document.getElementById('msg').textContent = 'Network error. Please try again.';
    document.getElementById('msg').style.color = 'red';
  }
}
</script>
</body>
</html>