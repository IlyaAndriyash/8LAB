<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid profile ID');
}

$id = $_GET['id'];
$db = new PDO('mysql:host=localhost;dbname=u68818', 'u68818', '9972335', [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Profile not found');
}

if (!empty($_SESSION['login'])) {
    $stmt = $db->prepare("SELECT application_id FROM users WHERE login = ?");
    $stmt->execute([$_SESSION['login']]);
    $application_id = $stmt->fetchColumn();
    if ($application_id != $id) {
        die('Access denied');
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .profile { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="profile">
        <h2>Ваш профиль (ID: <?php echo htmlspecialchars($id); ?>)</h2>
        <p><strong>ФИО:</strong> <?php echo htmlspecialchars($data['fio']); ?></p>
        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($data['phone']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email']); ?></p>
        <p><strong>Дата рождения:</strong> <?php echo htmlspecialchars($data['dob']); ?></p>
        <p><strong>Пол:</strong> <?php echo htmlspecialchars($data['gender']); ?></p>
        <p><strong>Биография:</strong> <?php echo htmlspecialchars($data['bio']); ?></p>
        <p><strong>Контракт:</strong> <?php echo $data['contract'] ? 'Да' : 'Нет'; ?></p>
        <?php
        $stmt = $db->prepare("SELECT pl.name FROM programming_languages pl JOIN application_languages al ON pl.id = al.language_id WHERE al.application_id = ?");
        $stmt->execute([$id]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($languages) {
            echo '<p><strong>Языки программирования:</strong> ' . implode(', ', array_map('htmlspecialchars', $languages)) . '</p>';
        }
        ?>
        <?php if (!empty($_SESSION['login'])): ?>
            <a href="/8LAB/index.php">Редактировать</a>
        <?php endif; ?>
    </div>
</body>
</html>