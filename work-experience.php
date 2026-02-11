<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Experience</title>
</head>
<body>
    
    <form action="work-experience.php" method="post">
        <h3>Work Experience</h3>

        <label for="job_title">Job Title</label>
        <input type="text" id="job_title" name="job_title">
        <br>
        <label for="city">City/Town</label>
        <input type="text" id="city" name="city">
        <br>
        <label for="employer">Employer</label>
        <input type="text" id="employer" name="employer">
        <br>
        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" name="start_date">
        <br>
        <label for="end_date">End Date</label>
        <input type="date" id="end_date" name="end_date">
        <br>
        
        <label for="description">Description</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea>
        <br>
        <button onclick="addAnotherWorkExperience()">Add Another Work Experience</button>
        <input type="submit" value="next">


        <!-- dynamic nalang to since you can enter multiple work experiences -->
    </form>
</body>
</html>