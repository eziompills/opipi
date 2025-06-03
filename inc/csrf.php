<?php
/**
 * CSRF helper – patched 2025‑06‑03
 * Accepts token via POST field `csrf_token` **or** HTTP header `X‑CSRF‑Token`.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Return the current CSRF token (and create one if needed).
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Convenience field to drop inside forms.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
           htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Throws 403 if token is missing or invalid on every POST request.
 * Accepts token either in the body (`csrf_token`) or in the
 * `X-CSRF-Token` request header (pratique pour fetch/AJAX).
 */
function verify_request(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sentToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!hash_equals($_SESSION['csrf_token'] ?? '', $sentToken)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }
    }
}

// Always make sure there’s a token ready for subsequent GET‑>POST flows
csrf_token(); 
?>
