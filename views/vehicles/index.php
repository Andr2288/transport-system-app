<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Автомобілі</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; }
        .nav { background: #f4f4f4; padding: 10px; margin: 20px 0; }
        .nav a { margin: 0 15px; text-decoration: none; color: #333; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">Головна</a>
        <a href="index.php?controller=vehicles">Автомобілі</a>
        <a href="index.php?controller=drivers">Водії</a>
        <a href="index.php?controller=trips">Рейси</a>
    </div>

    <h1>Управління автомобілями</h1>
    
    <a href="index.php?controller=vehicles&action=create" class="btn">Додати автомобіль</a>
    
    <table>
        <tr>
            <th>Номер</th>
            <th>Марка</th>
            <th>Модель</th>
            <th>Рік</th>
            <th>Вантажність</th>
            <th>Водій</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        <?php foreach ($vehicles as $vehicle): ?>
        <tr>
            <td><?php echo htmlspecialchars($vehicle['license_plate']); ?></td>
            <td><?php echo htmlspecialchars($vehicle['brand']); ?></td>
            <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
            <td><?php echo $vehicle['year']; ?></td>
            <td><?php echo $vehicle['capacity']; ?> т</td>
            <td><?php echo htmlspecialchars($vehicle['driver_name'] ?? 'Не призначений'); ?></td>
            <td><?php echo htmlspecialchars($vehicle['status']); ?></td>
            <td>
                <a href="index.php?controller=vehicles&action=edit&id=<?php echo $vehicle['id']; ?>">Редагувати</a>
                <a href="index.php?controller=vehicles&action=delete&id=<?php echo $vehicle['id']; ?>" 
                   onclick="return confirm('Видалити автомобіль?')">Видалити</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
