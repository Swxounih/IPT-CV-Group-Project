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
            pi.photo,
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

// Helper function to generate avatar with initials
function getAvatarImage($user_info) {
    if (!empty($user_info['photo'])) {
        return 'uploads/photos/' . htmlspecialchars($user_info['photo']);
    }
    
    // Generate initials
    $initials = strtoupper(substr($user_info['given_name'], 0, 1) . substr($user_info['surname'], 0, 1));
    
    // Create SVG with gradient background and initials
    $svg = <<<SVG
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'>
    <defs>
        <linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'>
            <stop offset='0%' style='stop-color:#1ebbeb;stop-opacity:1' />
            <stop offset='100%' style='stop-color:#3450ce;stop-opacity:1' />
        </linearGradient>
    </defs>
    <rect width='200' height='200' fill='url(#grad)'/>
    <text x='100' y='125' font-family='Arial, sans-serif' font-size='80' font-weight='bold' fill='white' text-anchor='middle'>{$initials}</text>
</svg>
SVG;
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Builder Dashboard</title>
    <link rel="stylesheet" href="css/dashboard-styles.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>CV Builder</h2>
                <p>Manage Your Resumes</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="nav-link active" onclick="showSection('profile'); return false;">Profile</a></li>
                    <li><a href="#" class="nav-link" onclick="showSection('my-resumes'); return false;">My Resumes</a></li>
                    <li><a href="#" class="nav-link" onclick="showSection('account-settings'); return false;">Account Settings</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-link" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
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
                            <img src="<?php echo getAvatarImage($user_info); ?>" alt="Profile Avatar">
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
                                        <button class="btn-small btn-edit" onclick="window.location.href='edit-cv-inline.php?id=<?php echo $cv['id']; ?>'">Edit</button>
                                        <button class="btn-small btn-view" onclick="window.location.href='view_resume.php?id=<?php echo $cv['id']; ?>'">View</button>
                                        <button class="btn-small btn-delete" onclick="if(confirm('Are you sure you want to delete this CV? This action cannot be undone.')) window.location.href='delete-cv.php?id=<?php echo $cv['id']; ?>'">Delete</button>
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
                            <img src="<?php echo getAvatarImage($user_info); ?>" alt="Profile Avatar">
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
                    <h2>⚠ Danger Zone</h2>
                    <div class="settings-content">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h3>Delete Account</h3>
                                <p>Permanently delete your account and all <?php echo count($cvs); ?> resume(s)</p>
                            </div>
                            <button class="btn-small btn-delete" onclick="if(confirm('⚠ WARNING: This will permanently delete your account and ALL <?php echo count($cvs); ?> resume(s). This action CANNOT be undone! Are you absolutely sure?')) window.location.href='delete-account.php'">Delete Account</button>
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
            const toggleButtons = document.querySelectorAll('.menu-toggle');
            let isToggle = false;
            
            // Check if click is on any toggle button
            toggleButtons.forEach(button => {
                if (button.contains(event.target)) {
                    isToggle = true;
                }
            });
            
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