<?php
/**
 * Simple flash message helpers using session.
 */
function set_flash(string $message, string $type = 'success'): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [$message, $type];
}

function display_flash(): void {
    if (!empty($_SESSION['flash'])) {
        [$message, $type] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = preg_replace('/[^a-z]/', '', $type) ?: 'info';
        echo '<div class="alert alert-' . htmlspecialchars($type) . '">' . htmlspecialchars($message) . '</div>';
    }
}
