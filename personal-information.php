<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
</head>
<body>
    <form action="personal-information.php" method="post">

        <h3>Personal Information</h3>
        <label for="photo">Photo:</label>
        <input type="file" id="photo" name="photo">
        <br>
        <label for="given_name">Given Name:</label>
        <input type="text" id="given_name" name="given_name">
        <br>
        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name">
        <br>
        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname">
        <br>
        <label for="extension">Extension:</label>
        <input type="text" id="extension" name="extension">
        <br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <br>
        <label for="birthdate">Date of Birth</label>
        <input type="date" name="birthdate" id="birthdate">
        <br>
        <label for="birthplace">Place of Birth</label>
        <input type="text" name="birthplace" id="birthplace" placeholder="Place of Birth">
        <br>
        <label for="civil_status">Civil Status:</label>
        <select id="civil_status" name="civil_status">
            <option value="single">Single</option>
            <option value="married">Married</option>
            <option value="divorced">Divorced</option>
            <option value="widowed">Widowed</option>
        </select>



        <h4>Contact Information</h4>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
        <br>
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone">
        <br>
        <label for="address">Address</label>
        <input type="text" id="address" name="address">
        <br>
        <label for="website">Website</label>
        <input type="url" id="website" name="website">
        <br>

        <input type="submit" value="next">

    </form>
</body>
</html>