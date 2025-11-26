<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Рейси</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; margin: 5px; }
        .btn-success { background: #28a745; }
        .nav { background: #f4f4f4; padding: 10px; margin: 20px 0; }
        .nav a { margin: 0 15px; text-decoration: none; color: #333; }
        .nav a:hover { color: #007bff; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-completed { color: #6c757d; }
        .status-planned { color: #ffc107; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">Головна</a>
        <a href="index.php?controller=vehicles">Автомобілі</a>
        <a href="index.php?controller=drivers">Водії</a>
        <a href="index.php?controller=trips">Рейси</a>
    </div>

    <h1>Управління рейсами</h1>
    
    <a href="index.php?controller=trips&action=create" class="btn">Додати рейс</a>
    <a href="index.php?controller=trips&action=active" class="btn btn-success">Активні рейси</a>
    
    <?php if (isset($error)): ?>
        <p style="color: red;">Помилка: <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($trips)): ?>
        <table>
            <tr>
                <th>Автомобіль</th>
                <th>Водій</th>
                <th>Маршрут</th>
                <th>Відстань</th>
                <th>Час початку</th>
                <th>Час закінчення</th>
                <th>Паливо</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
            <?php foreach ($trips as $trip): ?>
            <tr>
                <td><?php echo htmlspecialchars($trip['license_plate'] . ' (' . $trip['brand'] . ' ' . $trip['model'] . ')'); ?></td>
                <td><?php echo htmlspecialchars($trip['driver_name']); ?></td>
                <td>
                    <?php echo htmlspecialchars($trip['route_name']); ?><br>
                    <small><?php echo htmlspecialchars($trip['start_point'] . ' → ' . $trip['end_point']); ?></small>
                </td>
                <td><?php echo $trip['distance_km']; ?> км</td>
                <td><?php echo $trip['start_time'] ? date('d.m.Y H:i', strtotime($trip['start_time'])) : '-'; ?></td>
                <td><?php echo $trip['end_time'] ? date('d.m.Y H:i', strtotime($trip['end_time'])) : '-'; ?></td>
                <td><?php echo $trip['fuel_consumed'] ? $trip['fuel_consumed'] . ' л' : '-'; ?></td>
                <td>
                    <span class="status-<?php echo $trip['status']; ?>">
                        <?php
                        $statuses = [
                            'planned' => 'Запланований',
                            'active' => 'Активний',
                            'completed' => 'Завершений'
                        ];
                        echo $statuses[$trip['status']] ?? $trip['status'];
                        ?>
                    </span>
                </td>
                <td>
                    <a href="index.php?controller=trips&action=edit&id=<?php echo $trip['id']; ?>">Редагувати</a>
                    <a href="index.php?controller=trips&action=delete&id=<?php echo $trip['id']; ?>" 
                       onclick="return confirm('Видалити рейс?')">Видалити</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Немає рейсів для відображення</p>
    <?php endif; ?>
</body>
</html>
