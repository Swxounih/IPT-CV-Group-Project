<!-- Sidebar Navigation -->
<button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>CV Builder</h2>
        <p>Step by step Resume Creation</p>
    </div>
    
    <nav>
        <div class="step-indicator">Personal Details</div>
        <ul>
            <li>
                <a href="personal-information.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'personal-information.php') ? 'active' : ''; ?>">
                Personal Information
                </a>
            </li>
            <li>
                <a href="career-objectives.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'career-objectives.php') ? 'active' : ''; ?>">
                 Career Objectives
                </a>
            </li>
        </ul>
        
        <div class="step-indicator">Professional Background</div>
        <ul>
            <li>
                <a href="education.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'education.php') ? 'active' : ''; ?>">
                  Education
                </a>
            </li>
            <li>
                <a href="work-experience.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'work-experience.php') ? 'active' : ''; ?>">
                 Work Experience
                </a>
            </li>
            <li>
                <a href="skills.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'skills.php') ? 'active' : ''; ?>">
                 Skills
                </a>
            </li>
        </ul>
        
        <div class="step-indicator">Additional Info</div>
        <ul>
            <li>
                <a href="interests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'interests.php') ? 'active' : ''; ?>">
                 Interests & Hobbies
                </a>
            </li>
            <li>
                <a href="references.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'references.php') ? 'active' : ''; ?>">
                References
                </a>
            </li>
        </ul>
        
    </nav>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Prevent body scroll when sidebar is open on mobile
    if (sidebar.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Close sidebar when clicking on a navigation link on mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar nav ul li a');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Handle sidebar first load animation only once per session
    if (!sessionStorage.getItem('sidebarAnimated')) {
        sidebar.classList.add('first-load');
        sessionStorage.setItem('sidebarAnimated', 'true');
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }, 250);
    });
});
</script>