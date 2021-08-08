<?php

use Mahan4\m;
use Mahan4\view;
?>

<?php
try {
    $path = self::UI_PATH.'global/head.php';
    (file_exists($path))
        ? require_once $path
        : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
} catch (Exception $e) {
    die($e->getMessage());
}
?>

Maintenance

<?php
try {
    $path = self::UI_PATH.'global/footer.php';
    (file_exists($path))
        ? require_once $path
        : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
} catch (Exception $e) {
    die($e->getMessage());
}
?>
