<?php
session_start();
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_user_id'])) {
    header('Location: search-create.php');
    exit();
}

$user_id = $_SESSION['verified_user_id'];
$conn = getDBConnection();

// Get user's first CV for profile display
$sql = "SELECT * FROM personal_information WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
$stmt->close();

if (!$user_info) {
    // User has no CVs, redirect to create one
    header('Location: personal-information.php');
    exit();
}

// Get ALL CVs for this user (supporting multiple CVs)
$cvs = [];
$sql = "SELECT 
            pi.id,
            pi.cv_title,
            pi.given_name,
            pi.middle_name,
            pi.surname,
            pi.updated_at,
            pi.created_at,
            co.objective
        FROM personal_information pi
        LEFT JOIN career_objectives co ON pi.id = co.personal_info_id
        WHERE pi.user_id = ?
        ORDER BY pi.updated_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cvs[] = $row;
}
$stmt->close();

closeDBConnection($conn);

// Helper function to format date
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    return floor($diff / 2592000) . ' months ago';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Builder Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0,0,0,0.3);
        }
        
        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #60a5fa;
        }
        
        .sidebar-header p {
            font-size: 13px;
            color: rgba(255,255,255,0.6);
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
        }
        
        .sidebar-nav ul {
            list-style: none;
        }
        
        .sidebar-nav ul li {
            margin: 5px 0;
        }
        
        .nav-link {
            display: block;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: #60a5fa;
            color: white;
            padding-left: 30px;
        }
        
        .nav-link.active {
            background: rgba(96,165,250,0.2);
            border-left-color: #60a5fa;
            color: white;
            font-weight: 600;
        }
        
        .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .logout-link {
            display: block;
            padding: 12px 20px;
            background: rgba(239,68,68,0.2);
            color: #fca5a5;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .logout-link:hover {
            background: rgba(239,68,68,0.3);
            color: #fee2e2;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }
        
        .main-section {
            display: none;
        }
        
        .main-section.active {
            display: block;
        }
        
        .content-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-header h1 {
            font-size: 32px;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .content-header p {
            color: #64748b;
            font-size: 15px;
        }
        
        .menu-toggle {
            display: none;
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            margin-right: 15px;
        }
        
        /* Profile Section */
        .profile-section,
        .recent-resumes,
        .settings-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .profile-section h2,
        .recent-resumes h2,
        .settings-section h2 {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .profile-content {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        
        .profile-avatar {
            flex-shrink: 0;
        }
        
        .profile-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
        }
        
        .profile-details {
            flex: 1;
        }
        
        .profile-field {
            margin-bottom: 15px;
        }
        
        .profile-field label {
            display: block;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .profile-field p,
        .profile-field a {
            font-size: 15px;
            color: #1e293b;
        }
        
        /* Resumes List */
        .resumes-list {
            display: grid;
            gap: 20px;
        }
        
        .resume-item {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .resume-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .resume-info h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .resume-info p {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .resume-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        
        .btn-view {
            background: #10b981;
            color: white;
        }
        
        .btn-view:hover {
            background: #059669;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        
        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-create-cv {
            padding: 15px 40px;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-create-cv .icon {
            font-size: 22px;
            font-weight: bold;
        }
        
        /* Danger Zone */
        .danger-zone {
            border: 2px solid #fee2e2;
            background: #fef2f2;
        }
        
        .danger-zone h2 {
            color: #991b1b;
            border-bottom-color: #fecaca;
        }
        
        .settings-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
        }
        
        .setting-info h3 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .setting-info p {
            font-size: 14px;
            color: #64748b;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .profile-content {
                flex-direction: column;
            }
            
            .resume-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .resume-actions {
                width: 100%;
                flex-wrap: wrap;
            }
            
            .btn-small {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>📋 CV Builder</h2>
                <p>Manage Your Resumes</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="nav-link active" onclick="showSection('profile'); return false;">👤 Profile</a></li>
                    <li><a href="#" class="nav-link" onclick="showSection('my-resumes'); return false;">📄 My Resumes</a></li>
                    <li><a href="#" class="nav-link" onclick="showSection('account-settings'); return false;">⚙️ Account Settings</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-link" onclick="return confirm('Are you sure you want to logout?');">🚪 Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Profile Section -->
            <section id="profile-section" class="main-section active">
                <div class="content-header">
                    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                    <div>
                        <h1>Your Profile</h1>
                        <p>Manage your personal information and resume settings</p>
                    </div>
                </div>

                <!-- Profile Information Section -->
                <section class="profile-section">
                    <h2>Profile Information</h2>
                    <div class="profile-content">
                        <div class="profile-avatar">
                            <?php if (!empty($user_info['photo']) && file_exists($user_info['photo'])): ?>
                                <img src="<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Avatar">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150" alt="Profile Avatar">
                            <?php endif; ?>
                        </div>
                        <div class="profile-details">
                            <div class="profile-field">
                                <label>Full Name</label>
                                <p><?php echo htmlspecialchars($user_info['given_name'] . ' ' . ($user_info['middle_name'] ?? '') . ' ' . $user_info['surname'] . ' ' . ($user_info['extension'] ?? '')); ?></p>
                            </div>
                            <div class="profile-field">
                                <label>Email</label>
                                <p><a href="mailto:<?php echo htmlspecialchars($user_info['email']); ?>"><?php echo htmlspecialchars($user_info['email']); ?></a></p>
                            </div>
                            <div class="profile-field">
                                <label>Phone</label>
                                <p><?php echo htmlspecialchars($user_info['phone']); ?></p>
                            </div>
                            <?php if (!empty($user_info['address'])): ?>
                            <div class="profile-field">
                                <label>Address</label>
                                <p><?php echo htmlspecialchars($user_info['address']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                
                <!-- Quick Stats -->
                <section class="profile-section">
                    <h2>Quick Stats</h2>
                    <div class="profile-content">
                        <div class="profile-field">
                            <label>Total Resumes</label>
                            <p style="font-size: 32px; font-weight: 700; color: #667eea;"><?php echo count($cvs); ?></p>
                        </div>
                    </div>
                </section>
            </section>

            <!-- My Resumes Section -->
            <section id="my-resumes-section" class="main-section">
                <div class="content-header">
                    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                    <div>
                        <h1>My Resumes</h1>
                        <p>View and manage all your resumes (<?php echo count($cvs); ?> total)</p>
                    </div>
                </div>

                <!-- Recent Resumes -->
                <section class="recent-resumes">
                    <h2>Your Resumes</h2>
                    <div class="resumes-list">
                        <?php if (!empty($cvs)): ?>
                            <?php foreach ($cvs as $cv): ?>
                                <div class="resume-item">
                                    <div class="resume-info">
                                        <h3><?php echo htmlspecialchars($cv['cv_title'] ?? 'My Resume'); ?></h3>
                                        <p><strong><?php echo htmlspecialchars($cv['given_name'] . ' ' . $cv['surname']); ?></strong></p>
                                        <p>Last updated: <?php echo timeAgo($cv['updated_at']); ?></p>
                                        <?php if (!empty($cv['objective'])): ?>
                                            <p style="margin-top: 8px; font-style: italic; color: #475569;"><?php echo htmlspecialchars(substr($cv['objective'], 0, 80)) . (strlen($cv['objective']) > 80 ? '...' : ''); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="resume-actions">
                                        <button class="btn-small btn-edit" onclick="window.location.href='edit-cv-inline.php?id=<?php echo $cv['id']; ?>'">✏️ Edit</button>
                                        <button class="btn-small btn-view" onclick="window.location.href='view_resume.php?id=<?php echo $cv['id']; ?>'">👁️ View</button>
                                        <button class="btn-small btn-delete" onclick="if(confirm('Are you sure you want to delete this CV? This action cannot be undone.')) window.location.href='delete-cv.php?id=<?php echo $cv['id']; ?>'">🗑️ Delete</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="resume-item">
                                <div class="resume-info">
                                    <h3>No resumes yet</h3>
                                    <p>Create your first resume to get started!</p>
                                </div>
                                <div class="resume-actions">
                                    <button class="btn-small btn-primary" onclick="window.location.href='personal-information.php'">Create Now</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                
                <div style="margin-top: 20px; text-align: center;">
                    <button class="btn-primary btn-create-cv" onclick="createNewCV()">
                        <span class="icon">+</span> Create New Resume
                    </button>
                </div>
            </section>

            <!-- Account Settings Section -->
            <section id="account-settings-section" class="main-section">
                <div class="content-header">
                    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                    <div>
                        <h1>Account Settings</h1>
                        <p>Manage your profile and account preferences</p>
                    </div>
                </div>

                <!-- Profile Settings -->
                <section class="profile-section">
                    <h2>Profile Settings</h2>
                    <div class="profile-content">
                        <div class="profile-avatar">
                            <?php if (!empty($user_info['photo']) && file_exists($user_info['photo'])): ?>
                                <img src="<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Avatar">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150" alt="Profile Avatar">
                            <?php endif; ?>
                        </div>
                        <div class="profile-details">
                            <div class="profile-field">
                                <label>Full Name</label>
                                <p><?php echo htmlspecialchars($user_info['given_name'] . ' ' . ($user_info['middle_name'] ?? '') . ' ' . $user_info['surname']); ?></p>
                            </div>
                            <div class="profile-field">
                                <label>Email</label>
                                <p><?php echo htmlspecialchars($user_info['email']); ?></p>
                            </div>
                            <div class="profile-field">
                                <label>Phone</label>
                                <p><?php echo htmlspecialchars($user_info['phone']); ?></p>
                            </div>
                            <?php if (!empty($user_info['address'])): ?>
                            <div class="profile-field">
                                <label>Address</label>
                                <p><?php echo htmlspecialchars($user_info['address']); ?></p>
                            </div>
                            <?php endif; ?>
                            <button class="btn-small btn-edit" style="margin-top: 15px;" onclick="window.location.href='edit-cv-inline.php?id=<?php echo $user_info['id']; ?>'">Edit Profile</button>
                        </div>
                    </div>
                </section>

                <!-- Danger Zone -->
                <section class="settings-section danger-zone">
                    <h2>⚠️ Danger Zone</h2>
                    <div class="settings-content">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Delete Account</h3>
                                <p>Permanently delete your account and all <?php echo count($cvs); ?> resume(s)</p>
                            </div>
                            <button class="btn-small btn-delete" onclick="if(confirm('⚠️ WARNING: This will permanently delete your account and ALL <?php echo count($cvs); ?> resume(s). This action CANNOT be undone! Are you absolutely sure?')) window.location.href='delete-account.php'">Delete Account</button>
                        </div>
                    </div>
                </section>
            </section>
        </main>
    </div>
    
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.main-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId + '-section').classList.add('active');
            
            // Add active class to clicked link
            event.target.classList.add('active');
            
            // Close mobile sidebar
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        function createNewCV() {
            // Clear any existing resume data session
            window.location.href = 'create-additional-cv.php';
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>