<?php
// Логирование всех входящих данных
file_put_contents('request_log.txt', "===== New Request =====\n", FILE_APPEND);
file_put_contents('request_log.txt', "Time: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Логирование заголовков
file_put_contents('request_log.txt', "Headers:\n", FILE_APPEND);
file_put_contents('request_log.txt', print_r(getallheaders(), true), FILE_APPEND);

// Логирование POST-данных
file_put_contents('request_log.txt', "POST data:\n", FILE_APPEND);
file_put_contents('request_log.txt', print_r($_POST, true), FILE_APPEND);

// Логирование файлов
file_put_contents('request_log.txt', "FILES data:\n", FILE_APPEND);
file_put_contents('request_log.txt', print_r($_FILES, true), FILE_APPEND);

// Логирование сырых данных (например, JSON)
file_put_contents('request_log.txt', "Raw input:\n", FILE_APPEND);
file_put_contents('request_log.txt', file_get_contents('php://input'), FILE_APPEND);

file_put_contents('request_log.txt', "\n\n", FILE_APPEND);

// Обработка загружаемых файлов
if ($_FILES && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "File uploaded successfully!";
    } else {
        $error = "File upload failed! Error code: " . $_FILES['file']['error'];
        file_put_contents('upload_errors.txt', $error . "\n", FILE_APPEND);
        echo $error;
    }
    exit;
}

const USERNAME = 'admin';
const PASSWORD = 'password';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD) {
        $_SESSION['authenticated'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $files = array_diff(scandir('uploads'), array('.', '..'));

    if (isset($_GET['delete'])) {
        $fileToDelete = $_GET['delete'];
        if (in_array($fileToDelete, $files)) {
            unlink('./uploads/' . $fileToDelete);
            header('Location: index.php');
            exit;
        }
    }

    echo "
    <html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Панель управления</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                width: 80%;
                max-width: 900px;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                padding: 20px;
            }
            h1 {
                text-align: center;
                color: #333;
            }
            .btn {
                color: green;
                padding: 10px 15px;
                text-decoration: none;
                border-radius: 5px;
                margin: 5px;
            }
            .file-list {
                margin-top: 20px;
            }
            .file-item {
                background-color: #fff;
                padding: 15px;
                margin: 10px 0;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .file-item a {
                color: #007bff;
                text-decoration: none;
            }
            .file-item a:hover {
                text-decoration: underline;
            }
            .upload-form {
                margin-bottom: 20px;
            }
            .upload-form input[type='file'] {
                margin-bottom: 10px;
            }
            .text-center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Панель управления</h1>
            <p class='text-center'>Добро пожаловать! Вы вошли как администратор.</p>
            <div class='text-center'>
                <a href='?logout' class='btn'>Выход</a>
            </div>
            <h2>Логи</h2>
            <div class='file-list'>
                ";

                foreach ($files as $file) {
                    $fileSize = filesize('./uploads/' . $file);
                    $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';

                    echo "
                    <div class='file-item'>
                        <span>$file ($fileSizeFormatted)</span>
                        <div>
                            <a href='./uploads/$file' class='btn' download>Скачать</a>
                            <a href='?delete=$file' class='btn'>Удалить</a>
                        </div>
                    </div>
                    ";
                }

                echo "
            </div>
        </div>
    </body>
    </html>
    ";
} else {
    echo "
    <html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Авторизация</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .login-container {
                background-color: #fff;
                padding: 20px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                border-radius: 5px;
                width: 300px;
                text-align: center;
            }
            .login-container h2 {
                margin-bottom: 20px;
                color: #333;
            }
            .login-container input {
                width: 100%;
                padding: 10px;
                margin: 5px 0;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            .login-container button {
                background-color: #007bff;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 5px;
                width: 100%;
                cursor: pointer;
            }
            .login-container button:hover {
                background-color: #0056b3;
            }
            .error {
                color: red;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class='login-container'>
            <h2>Авторизация</h2>
            <form method='POST'>
                <input type='text' name='username' placeholder='Логин' required><br>
                <input type='password' name='password' placeholder='Пароль' required><br>
                <button type='submit'>Войти</button>
            </form>
            ";
            if (isset($error)) {
                echo "<div class='error'>$error</div>";
            }
            echo "
        </div>
    </body>
    </html>
    ";
}
?>