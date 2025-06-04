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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['flash'])) {
        [$message, $type] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = preg_replace('/[^a-z]/', '', $type) ?: 'info';
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($message)
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
            . '</div>';
        echo '<div class="alert alert-' . htmlspecialchars($type) . '">' . htmlspecialchars($message) . '</div>';
    }
}
