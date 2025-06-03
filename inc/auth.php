<?php
/**
 * Authentication & authorisation helpers
 */
require_once __DIR__.'/config.php';

/**
 * Require that the visitor be logged in, otherwise redirect to login
 */
function require_login(): void {
    if (!is_logged()) {
        header('Location: /login.php');
        exit;
    }
}
