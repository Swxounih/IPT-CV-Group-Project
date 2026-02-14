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
        
        <div class="step-indicator">Final Step</div>
        <ul>
            <li>
                <a href="preview.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'preview.php') ? 'active' : ''; ?>">
                Preview & Submit
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
}
</script>