<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif; background:#f5f6fa; }

.sidebar {
    position:fixed; left:0; top:0;
    width:250px; height:100vh;
    background:linear-gradient(135deg,#667eea,#764ba2);
    padding:20px;
}
.sidebar-header { text-align:center; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.2); }
.sidebar-header h2 { color:white; }
.sidebar-header p { color:rgba(255,255,255,0.8); font-size:14px; }

.menu { list-style:none; }
.menu li { margin-bottom:10px; }
.menu a {
    display:flex; align-items:center;
    padding:15px 20px;
    color:white; text-decoration:none;
    border-radius:10px;
}
.menu a:hover, .menu a.active { background:rgba(255,255,255,0.25); }
.menu-icon { margin-right:15px; }

.main-content { margin-left:250px; padding:30px; }

.top-bar {
    background:white; padding:20px 30px;
    border-radius:15px;
    margin-bottom:30px;
    display:flex; justify-content:space-between; align-items:center;
}
.top-bar h1 { font-size:28px; }

.content-section {
    background:white;
    padding:30px;
    border-radius:15px;
    display:none;
}
.content-section.active { display:block; }

.form-group { margin-bottom:20px; }
.form-group label { display:block; margin-bottom:8px; font-weight:500; }

.form-group input[type="text"],
.form-group input[type="file"],
.form-group textarea {
    width:100%;
    padding:14px;
    border:2px solid #e0e0e0;
    border-radius:8px;
    font-size:15px;
}

.form-group textarea {
    min-height:350px;
    resize:vertical;
}

.btn {
    padding:12px 30px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:16px;
}

@media (max-width:768px) {
    .sidebar { width:70px; }
    .sidebar-header h2, .sidebar-header p, .menu-text { display:none; }
    .main-content { margin-left:70px; }
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
        <p>Management System</p>
    </div>
    <ul class="menu">
        <li><a href="#" class="active" onclick="showSection(event,'dashboard')"><span class="menu-icon">📊</span><span class="menu-text">Dashboard</span></a></li>
        <li><a href="#" onclick="showSection(event,'upload')"><span class="menu-icon">📤</span><span class="menu-text">Upload</span></a></li>
        <li><a href="#" onclick="showSection(event,'about')"><span class="menu-icon">ℹ️</span><span class="menu-text">About</span></a></li>
    </ul>
</div>

<div class="main-content">

<div class="top-bar">
    <h1>Dashboard</h1>
    <div>Admin</div>
</div>

<div id="dashboard" class="content-section active">
    <h2>Selamat Datang</h2>
    <p>Gunakan menu untuk mengelola sistem.</p>
</div>

<div id="upload" class="content-section">
    <h2>Upload File</h2>
    <form>
        <div class="form-group">
            <label>Pilih File</label>
            <input type="file">
        </div>
        <button class="btn">Upload</button>
    </form>

    <h2 style="margin-top:40px;">Input Teks</h2>
    <form>
        <div class="form-group">
            <label>Masukkan Teks</label>
            <textarea placeholder="Tulis teks di sini"></textarea>
        </div>
        <button class="btn">Kirim Teks</button>
    </form>
</div>

<div id="about" class="content-section">
    <h2>About</h2>
    <p>Sistem dashboard admin.</p>
</div>

</div>

<script>
function showSection(e,id){
    e.preventDefault();
    document.querySelectorAll('.content-section').forEach(s=>s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.menu a').forEach(a=>a.classList.remove('active'));
    e.currentTarget.classList.add('active');
    const titles={dashboard:'Dashboard',upload:'Upload File',about:'About System'};
    document.querySelector('.top-bar h1').textContent=titles[id];
}
</script>

</body>
</html>
```
