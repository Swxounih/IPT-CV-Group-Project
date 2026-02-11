<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education and Qualifications</title>
</head>
<body>
    <form action="education.php" method="post">
        <h3>Education</h3>

        <label for="degree">Degree</label>
        <input type="text" id="degree" name="degree">
        <br>
        <label for="institution">Institution</label>
        <input type="text" id="institution" name="institution">
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

        <button onclick="">add another education</button>

        <input type="submit" value="next">
    </form>
</body>
</html>