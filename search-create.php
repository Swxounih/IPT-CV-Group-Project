<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CV Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
    }
    
    :root {
      --primary: #0755d1;
      --primary-dark: #0755d1;
      --primary-light: #818cf8;
      --secondary: #ec4899;
      --success: #10b981;
      --dark: #0755d1;
      --dark-light: #1e293b;
      --gray: #64748b;
      --gray-light: #cbd5e1;
      --gray-lighter: #f1f5f9;
      --white: #ffffff;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
      --radius: 12px;
      --radius-lg: 16px;
    }
    
    body { 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1ebbeb 0%, #3450ce 100%);
      min-height: 100vh;
      color: var(--dark);
      line-height: 1.6;
    }
    
    /* Navigation Bar */
    .navbar {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      padding: 16px 0;
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 100;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 12px;
      padding: 0 24px;
      align-items: center;
      justify-content: center;
    }
    
    .logo {
      font-size: 24px;
      font-weight: 700;
      background: linear-gradient(50deg, var(--primary) 100%, var(--secondary) 0%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-right: auto;
    }
    
    .nav-btn {
      background: transparent;
      color: var(--gray);
      border: none;
      padding: 10px 24px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.3s ease;
      position: relative;
    }
    
    .nav-btn:hover {
      color: var(--primary);
      background: var(--gray-lighter);
    }
    
    .nav-btn.active {
      color: var(--primary);
      background: rgba(99, 102, 241, 0.1);
      font-weight: 600;
    }
    
    .nav-btn.active::after {
      content: '';
      position: absolute;
      bottom: -16px;
      left: 50%;
      transform: translateX(-50%);
      width: 40px;
      height: 3px;
      background: var(--primary);
      border-radius: 2px;
    }
    
    /* Main Container */
    .container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 24px;
     
    }
    
    /* Section Styling */
    .section {
      display: none;
      animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .section.active {
      display: block;
    }
    
    .section-content {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 48px;
      box-shadow: var(--shadow-xl);
    }
    
    /* Hero Section */
    .hero {
      text-align: center;
      margin-bottom: 48px;
    }
    
    .hero h1 {
      color: var(--dark);
      margin-bottom: 16px;
      font-size: 42px;
      font-weight: 700;
      letter-spacing: -0.02em;
      background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero p {
      color: var(--gray);
      font-size: 18px;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.7;
    }
    
    /* Feature Cards */
    .info-card {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(236, 72, 153, 0.05) 100%);
      border: 1px solid rgba(99, 102, 241, 0.1);
      border-radius: var(--radius);
      padding: 32px;
      margin-top: 32px;
    }
    
    .info-card h3 {
      color: var(--dark);
      margin-bottom: 16px;
      font-size: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    
    .info-card p {
      color: var(--gray);
      line-height: 1.7;
      margin-bottom: 12px;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 16px;
      margin-top: 20px;
    }
    
    .feature-item {
      background: var(--white);
      padding: 16px 20px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: var(--shadow-sm);
      transition: all 0.3s ease;
    }
    
    .feature-item:hover {
      box-shadow: var(--shadow);
      transform: translateY(-2px);
    }
    
    .feature-icon {
      width: 40px;
      height: 40px;
      background: transparent;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }
    
    .feature-text {
      color: var(--dark);
      font-weight: 500;
      font-size: 14px;
    }
    
    /* CTA Button */
    .cta-container {
      margin-top: 48px;
      text-align: center;
    }
    
    .cta-btn {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: var(--white);
      border: none;
      padding: 18px 48px;
      font-size: 17px;
      font-weight: 600;
      cursor: pointer;
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    
    
    .cta-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.4);
    }
    
    .cta-btn:hover::after {
      transform: translateX(4px);
    }
    
    /* Search Section */
    .search-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .search-header h1 {
      color: var(--dark);
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 12px;
      letter-spacing: -0.02em;
    }
    
    .search-header p {
      color: var(--gray);
      font-size: 16px;
    }
    
    .search-box {
      display: flex;
      gap: 12px;
      margin-bottom: 32px;
      background: var(--gray-lighter);
      padding: 8px;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
    }
    
    .search-box input {
      flex: 1;
      padding: 14px 20px;
      border: 2px solid transparent;
      border-radius: 8px;
      font-size: 15px;
      background: var(--white);
      transition: all 0.3s ease;
      font-family: inherit;
    }
    
    .search-box input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .search-box input::placeholder {
      color: var(--gray-light);
    }
    
    .search-btn {
      padding: 14px 32px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 15px;
    }
    
    .search-btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-lg);
    }
    
    /* Results Section */
    .results-section {
      min-height: 300px;
      border-radius: var(--radius);
      padding: 24px;
      background: var(--gray-lighter);
    }
    
    .empty-state {
      text-align: left
      padding:  20px;
      color: var(--gray);
    }
    
    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }
    
    .results-header {
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--gray-light);
    }
    
    .results-header h4 {
      color: var(--dark);
      font-size: 18px;
      font-weight: 600;
    }
    
    .result-item {
      background: var(--white);
      border: 1px solid rgba(0, 0, 0, 0.05);
      border-left: 4px solid var(--primary);
      padding: 24px;
      margin-bottom: 12px;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .result-item:hover {
      box-shadow: var(--shadow-lg);
      transform: translateX(8px);
      border-left-color: var(--secondary);
    }
    
    .result-item h4 {
      color: var(--dark);
      margin-bottom: 12px;
      font-size: 20px;
      font-weight: 600;
    }
    
    .result-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
      color: var(--gray);
      font-size: 14px;
    }
    
    .result-info-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .result-info-item strong {
      color: var(--dark);
      font-weight: 600;
    }
    
    .no-results {
      background: var(--white);
      border-radius: var(--radius);
      padding: 40px;
      text-align: center;
    }
    
    .no-results-icon {
      font-size: 64px;
      margin-bottom: 16px;
      opacity: 0.3;
    }
    
    .no-results h3 {
      color: var(--dark);
      margin-bottom: 8px;
      font-size: 20px;
    }
    
    .no-results p {
      color: var(--gray);
    }
    
    /* Loading Animation */
    .loading {
      text-align: center;
      padding: 40px;
    }
    
    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 4px solid var(--gray-lighter);
      border-top-color: var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 16px;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .info-notice {
      background: #e8f4fd;
      border-left: 4px solid #3b82f6;
      padding: 15px;
      border-radius: 4px;
      margin-top: 20px;
    }
    
    .info-notice p {
      color: #1e40af;
      font-size: 14px;
      line-height: 1.6;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-container {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .logo {
        margin-right: 0;
        width: 100%;
        text-align: center;
        margin-bottom: 12px;
      }
      
      .nav-btn.active::after {
        display: none;
      }
      
      .section-content {
        padding: 32px 24px;
      }
      
      .hero h1 {
        font-size: 32px;
      }
      
      .hero p {
        font-size: 16px;
      }
      
      .features-grid {
        grid-template-columns: 1fr;
      }
      
      .search-box {
        flex-direction: column;
      }
      
      .search-btn {
        width: 100%;
        padding: 16px;
      }
      
      .result-info {
        grid-template-columns: 1fr;
      }
      
      .cta-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo">CV Manager</div>
      <button class="nav-btn active" onclick="showSection('main')">Home</button>
      <button class="nav-btn" onclick="showSection('search')">Search CV</button>
    </div>
  </nav>
  
  <!-- Main Container -->
  <div class="container">
    
    <!-- Main Section -->
    <section id="main" class="section active">
      <div class="section-content">
        <h1>Welcome to CV Management System</h1>
        <p>This is your professional CV management platform. Add and manage your professional information here.</p>
      
        
        <div class="info-card">
          <h3>About the Platform</h3>
          <p>A comprehensive CV management system that empowers you to create, edit, and manage your professional resume with powerful features designed for modern job seekers.</p>
          
          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Personal Information</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Education & Qualifications</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Work Experience</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Skills & Competencies</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">References</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Career Objectives</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Advanced Search</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✅</div>
              <div class="feature-text">Profile Analytics</div>
            </div>
          </div>
        </div>
        
        <div class="cta-container">
          <button type="button" class="cta-btn" onclick="window.location.href='personal-information.php'">
            Create Your CV
          </button>
        </div>
      </div>
    </section>
    
    <!-- Search Section -->
    <section id="search" class="section">
      <div class="section-content">
        <div class="search-header">
          <h1>Search CV Database</h1>
          <p>Find professional profiles by name, email, or skills</p>
        </div>
        
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Enter name, email, or skills to search...">
          <button class="search-btn" onclick="performSearch()">Search</button>
        </div>
        
        <div class="info-notice">
        </div>
        
        <div class="results-section" id="resultsView">
          <div class="empty-state">
            <p>Enter a search term above to find professional profiles</p>
          </div>
        </div>
      </div>
    </section>
    
  </div>
  
  <script>
    function showSection(sectionId) {
      const sections = document.querySelectorAll('.section');
      sections.forEach(section => section.classList.remove('active'));
      
      const navBtns = document.querySelectorAll('.nav-btn');
      navBtns.forEach(btn => btn.classList.remove('active'));
      
      document.getElementById(sectionId).classList.add('active');
      event.target.classList.add('active');
    }
    
    function performSearch() {
      const searchTerm = document.getElementById('searchInput').value;
      const resultsView = document.getElementById('resultsView');
      
      if (searchTerm.trim() === '') {
        resultsView.innerHTML = `
          <div class="empty-state">
            <p>Please enter a search term</p>
          </div>
        `;
        return;
      }
      
      // Show loading
      resultsView.innerHTML = `
        <div class="loading">
          <div class="loading-spinner"></div>
          <p style="color: var(--gray);">Searching database...</p>
        </div>
      `;
      
      // AJAX call to search
      fetch('search_ajax.php?term=' + encodeURIComponent(searchTerm))
        .then(response => response.json())
        .then(data => {
          if (data.success && data.results.length > 0) {
            let html = '<h4>Search Results for: "' + searchTerm + '"</h4>';
            html += '<p style="margin-bottom: 15px; color: #666; font-size: 14px;">Click on a result to access your dashboard (verification required)</p>';
            data.results.forEach(result => {
              html += `
                <div class="result-item" onclick="viewResume(${result.id})">
                  <h4>${result.name}</h4>
                  <p><strong>Email:</strong> ${result.email}</p>
                  <p><strong>Phone:</strong> ${result.phone}</p>
                  <p><strong>Address:</strong> ${result.address}</p>
                  <p style="margin-top: 10px; font-size: 12px; color: #1abc9c;">
                    <strong>👉 Click here to verify and access your dashboard</strong>
                  </p>
                </div>
              `;
            });
            resultsView.innerHTML = html;
          } else {
            resultsView.innerHTML = `
              <div class="no-results">
                <h3>No results found</h3>
                <p>No profiles match "${searchTerm}". Try different keywords or check your spelling.</p>
              </div>
            `;
          }
        })
        .catch(error => {
          resultsView.innerHTML = `
            <div class="no-results">
              <div class="no-results-icon">❌</div>
              <h3>Search Error</h3>
              <p>An error occurred while searching. Please try again later.</p>
            </div>
          `;
        });
    }
    
    function viewResume(id) {
      // Redirect to birthdate verification first, then to dashboard
      window.location.href = 'verify-birthdate.php?id=' + id;
    }
    
    // Allow Enter key to search
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          performSearch();
        }
      });
    });
  </script>
</body>
</html>