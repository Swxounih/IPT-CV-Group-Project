<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>CV Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/search-create-styles.css">
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo">Curriculum Vitae Manager</div>
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
          <p>A comprehensive CV management system that allows you to create, edit, and manage your professional resume with features designed for modern job seekers.</p>
          
          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Personal Information</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Education & Qualifications</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Work Experience</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Skills & Competencies</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">References</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Career Objectives</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Advanced Search</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
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
            let html = '<div class="results-header">';
            html += '<h4>Search Results for: "' + escapeHtml(searchTerm) + '"</h4>';
            html += '<p style="margin-top: 8px; color: #666; font-size: 14px;">For privacy protection, only name and birthplace are shown. Click to verify identity and access your dashboard.</p>';
            html += '</div>';
            
            data.results.forEach(result => {
              html += `
                <div class="result-item" onclick="viewResume(${result.id})">
                  <div class="result-header-row">
                    
                    <div class="result-main">
                      <h4>${escapeHtml(result.name)}</h4>
                      <p class="result-location"><strong>📍 Birthplace:</strong> ${escapeHtml(result.birthplace)}</p>
                    </div>
                  </div>
                  <div class="result-action">
                    <p style="margin-top: 12px; font-size: 13px; color: #10b981; display: flex; align-items: center; gap: 6px;">
                      <strong>🔒 Click to verify identity and access dashboard</strong>
                    </p>
                  </div>
                </div>
              `;
            });
            resultsView.innerHTML = html;
          } else {
            resultsView.innerHTML = `
              <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h3>No results found</h3>
                <p>No profiles match "${escapeHtml(searchTerm)}". Try different keywords or check your spelling.</p>
              </div>
            `;
          }
        })
        .catch(error => {
          console.error('Search error:', error);
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
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Allow Enter key to search
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          performSearch();
        }
      });
      
      // Auto-focus search input when on search tab
      const searchSection = document.getElementById('search');
      if (searchSection.classList.contains('active')) {
        searchInput.focus();
      }
    });
    
    // Handle window resize for responsive nav
    let resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        // Additional responsive logic if needed
      }, 250);
    });
  </script>
</body>
</html>