<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills</title>
</head>
<body>
    <form action="skills.php" method="post">
        <h3>Skills and Competencies</h3>

        <label for="skills">Skills and Competencies</label><br>
        <input type="text" name="skills" id="skills">
        <br>
        <label for="level">Level of Competency</label>
        <select id="level" name="level">
            <option value="">Select Level</option>
            <option value="Expert">Expert</option>
            <option value="Experienced">Experienced</option>
            <option value="Skillful">Skillful</option>
            <option value="intermediate">Intermediate</option>
            <option value="Beginner">Beginner</option>
        </select>
        <br>
        <input type="submit" value="next">

    </form>
</body>
</html>