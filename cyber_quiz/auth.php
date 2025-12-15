<?php
// auth.php - session helpers
session_start();

function require_user() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?role=user');
        exit;
    }
}

function require_admin() {
    if (empty($_SESSION['is_admin'])) {
        header('Location: login.php?role=admin');
        exit;
    }
}
