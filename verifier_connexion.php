<?php
session_start();

if (isset($_SESSION['id_user'])) {
    echo json_encode(['is_logged_in' => true]);
} else {
    echo json_encode(['is_logged_in' => false]);
}
?>
