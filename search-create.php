<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CV Management</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body { 
      font-family: Arial, sans-serif; 
      background-color: #f5f5f5;
    }
    
    /* Navigation Bar */
    .navbar {
      background-color: #2c3e50;
      padding: 15px 0;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 10px;
      padding: 0 20px;
      align-items: center;
      justify-content: center;
    }
    
    .nav-btn {
      background-color: #34495e;
      color: white;
      border: none;
      padding: 10px 25px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
      transition: background-color 0.3s ease;
      width: 400px;
    }
    
    .nav-btn:hover {
      background-color: #1abc9c;
    }
    
    .nav-btn.active {
      background-color: #1abc9c;
      font-weight: bold;
    }
    
    /* Main Container */
    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    /* Section Styling */
    .section {
      display: none;
    }
    
    .section.active {
      display: block;
    }
    
    .section-content {
      background-color: white;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    h1 {
      color: #2c3e50;
      margin-bottom: 20px;
      font-size: 28px;
    }
    
    h3 { 
      color: #333; 
      margin-bottom: 15px;
    }
    
    .btn-container { 
      display: flex; 
      gap: 10px;
      margin-top: 30px;
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .btn-container button {
      padding: 18px 50px;
      font-size: 18px;
      min-width: 300px;
    }
    
    input[type="submit"], 
    button {
      padding: 12px 25px; 
      cursor: pointer;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      background-color: #1abc9c;
      color: white;
      transition: background-color 0.3s ease;
    }
    
    input[type="submit"]:hover, 
    button:hover {
      background-color: #16a085;
    }
    
    .search-box {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
    }
    
    .search-box input {
      flex: 1;
      padding: 10px;
      border: 1px solid #bdc3c7;
      border-radius: 4px;
    }
    
    .search-box button {
      padding: 10px 25px;
    }
    
    .results-section {
      margin-top: 20px;
      min-height: 200px;
      border: 1px solid #ecf0f1;
      border-radius: 4px;
      padding: 20px;
      background-color: #fafafa;
    }
    
    .result-item {
      background-color: white;
      border-left: 4px solid #1abc9c;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .result-item:hover {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transform: translateX(5px);
    }
    
    .result-item h4 {
      color: #2c3e50;
      margin-bottom: 5px;
    }
    
    .result-item p {
      color: #666;
      margin: 5px 0;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-btn {
        padding: 8px 20px;
        font-size: 14px;
      }
      
      .section-content {
        padding: 20px;
      }
      
      h1 {
        font-size: 22px;
      }
      
      .container {
        margin: 20px auto;
      }
      
      .btn-container button {
        padding: 15px 30px;
        font-size: 16px;
        min-width: 150px;
      }
      
      .search-box {
        flex-direction: column;
      }
      
      .search-box input {
        width: 100%;
      }
      
      .search-box button {
        width: 100%;
        padding: 12px;
      }
    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-container">
      <button class="nav-btn active" onclick="showSection('main')">Main</button>
      <button class="nav-btn" onclick="showSection('search')">Search</button>
    </div>
  </nav>
  
  <!-- Main Container -->
  <div class="container">
    
    <!-- Main Section -->
    <section id="main" class="section active">
      <div class="section-content">
        <h1>Welcome to CV Management System</h1>
        <p>This is your professional CV management platform. Add and manage your professional information here.</p>
        
        <div style="margin-top: 30px; padding: 20px; background-color: #ecf0f1; border-radius: 4px;">
          <h3>Website Information</h3>
          <p><strong>About:</strong> A comprehensive CV management system that allows you to create, edit, and manage your professional resume.</p>
          <p style="margin-top: 10px;"><strong>Features:</strong></p>
          <ul style="margin-left: 20px; margin-top: 10px;">
            <li>Personal Information Management</li>
            <li>Education & Qualifications</li>
            <li>Work Experience Tracking</li>
            <li>Skills & Competencies</li>
            <li>References</li>
            <li>Career Objectives</li>
            <li>Search & Filter Functionality</li>
          </ul>
        </div>
        
        <div class="btn-container">
            <button type="button" onclick="window.location.href='personal-information.php'">Create your own CV</button>
        </div>
      </div>
    </section>
    
    <!-- Search Section -->
    <section id="search" class="section">
      <div class="section-content">
        <h1>Search Personal CV</h1>
        
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Enter search term (name, email, or skills)">
          <button onclick="performSearch()">Search</button>
        </div>
        
        <div class="results-section" id="resultsView">
          <p style="color: #7f8c8d; text-align: center;">Enter a search term and click Search to view results</p>
        </div>
      </div>
    </section>
    
  </div>
  
  <script>
    function showSection(sectionId) {
      // Hide all sections
      const sections = document.querySelectorAll('.section');
      sections.forEach(section => {
        section.classList.remove('active');
      });
      
      // Remove active class from all nav buttons
      const navBtns = document.querySelectorAll('.nav-btn');
      navBtns.forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Show selected section
      document.getElementById(sectionId).classList.add('active');
      
      // Add active class to clicked button
      event.target.classList.add('active');
    }
    
    function performSearch() {
      const searchTerm = document.getElementById('searchInput').value;
      const resultsView = document.getElementById('resultsView');
      
      if (searchTerm.trim() === '') {
        resultsView.innerHTML = '<p style="color: #7f8c8d; text-align: center;">Please enter a search term</p>';
        return;
      }
      
      // Show loading
      resultsView.innerHTML = '<p style="color: #7f8c8d; text-align: center;">Searching...</p>';
      
      // AJAX call to search
      fetch('search_ajax.php?term=' + encodeURIComponent(searchTerm))
        .then(response => response.json())
        .then(data => {
          if (data.success && data.results.length > 0) {
            let html = '<h4>Search Results for: "' + searchTerm + '"</h4>';
            data.results.forEach(result => {
              html += `
                <div class="result-item" onclick="viewResume(${result.id})">
                  <h4>${result.name}</h4>
                  <p><strong>Email:</strong> ${result.email}</p>
                  <p><strong>Phone:</strong> ${result.phone}</p>
                  <p><strong>Address:</strong> ${result.address}</p>
                </div>
              `;
            });
            resultsView.innerHTML = html;
          } else {
            resultsView.innerHTML = `
              <div class="result-item">
                <strong>Search Results for: "${searchTerm}"</strong>
                <p style="margin-top: 10px;">No results found. Please refine your search.</p>
              </div>
            `;
          }
        })
        .catch(error => {
          resultsView.innerHTML = `
            <div class="result-item">
              <strong>Error</strong>
              <p style="margin-top: 10px;">An error occurred while searching. Please try again.</p>
            </div>
          `;
        });
    }
    
    function viewResume(id) {
      window.location.href = 'view_resume.php?id=' + id;
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