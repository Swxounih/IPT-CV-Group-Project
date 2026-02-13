<!-- Sidebar Navigation -->
<button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>📄 CV Builder</h2>
        <p>Step-by-step Resume Creation</p>
    </div>
    
    <nav>
        <div class="step-indicator">Personal Details</div>
        <ul>
            <li>
                <a href="personal-information.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'personal-information.php') ? 'active' : ''; ?>">
                    <i>👤</i> Personal Information
                </a>
            </li>
            <li>
                <a href="career-objectives.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'career-objectives.php') ? 'active' : ''; ?>">
                    <i>🎯</i> Career Objectives
                </a>
            </li>
        </ul>
        
        <div class="step-indicator">Professional Background</div>
        <ul>
            <li>
                <a href="education.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'education.php') ? 'active' : ''; ?>">
                    <i>🎓</i> Education
                </a>
            </li>
            <li>
                <a href="work-experience.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'work-experience.php') ? 'active' : ''; ?>">
                    <i>💼</i> Work Experience
                </a>
            </li>
            <li>
                <a href="skills.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'skills.php') ? 'active' : ''; ?>">
                    <i>⚡</i> Skills
                </a>
            </li>
        </ul>
        
        <div class="step-indicator">Additional Info</div>
        <ul>
            <li>
                <a href="interests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'interests.php') ? 'active' : ''; ?>">
                    <i>🎨</i> Interests & Hobbies
                </a>
            </li>
            <li>
                <a href="references.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'references.php') ? 'active' : ''; ?>">
                    <i>📞</i> References
                </a>
            </li>
        </ul>
        
        <div class="step-indicator">Final Step</div>
        <ul>
            <li>
                <a href="preview.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'preview.php') ? 'active' : ''; ?>">
                    <i>👁️</i> Preview & Submit
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