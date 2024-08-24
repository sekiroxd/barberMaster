<!DOCTYPE html>
<html>
<head>
    <title>Szalon Hozzáadása</title>
</head>
<body>
    <h1>Szalon Hozzáadása</h1>
    <form action="add_salon.php" method="POST">
        <label for="name">Szalon neve:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="address">Cím:</label>
        <input type="text" id="address" name="address" required><br>

        <label for="city">Város:</label>
        <input type="text" id="city" name="city" required><br>

        <label for="rating">Értékelés:</label>
        <input type="number" step="0.1" id="rating" name="rating" required><br>

        <button type="submit">Hozzáadás</button>
    </form>
</body>
</html>