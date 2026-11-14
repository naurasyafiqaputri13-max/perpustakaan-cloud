<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Memeriksa status login dan hak akses pengguna.
 *
 * Contoh:
 * cek_session();          // Semua pengguna yang login
 * cek_session('admin');   // Khusus admin
 * cek_session('mahasiswa'); // Khusus mahasiswa
 */
function cek_session($role_diizinkan = null)
{
    // Memeriksa apakah pengguna sudah login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php?error=belum_login");
        exit;
    }

    // Memeriksa hak akses berdasarkan role
    if ($role_diizinkan !== null) {
        $roles = (array) $role_diizinkan;

        if (!in_array($_SESSION['role'], $roles, true)) {
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../mahasiswa/dashboard.php");
            }

            exit;
        }
    }
}
?>