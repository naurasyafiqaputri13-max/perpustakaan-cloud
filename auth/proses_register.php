<?php
session_start();

require_once __DIR__ . '/../config/koneksi.php';

// Memastikan koneksi database tersedia
$db = $conn ?? $koneksi ?? null;

if (!$db) {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php");
}

// Pastikan data dikirim melalui form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// Mengambil data dari form
$nama = trim($_POST['nama'] ?? '');
$nim = trim($_POST['nim'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if ($nama === '' || $nim === '' || $email === '' || $password === '') {
    die("Semua kolom wajib diisi.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Format email tidak valid.");
}

if (strlen($password) < 8) {
    die("Password minimal 8 karakter.");
}

// Memeriksa apakah NIM atau email sudah digunakan
$cek = $db->prepare(
    "SELECT id FROM users WHERE nim = ? OR email = ?"
);

$cek->bind_param("ss", $nim, $email);
$cek->execute();

$hasil = $cek->get_result();

if ($hasil->num_rows > 0) {
    die("NIM atau email sudah terdaftar.");
}

$cek->close();

// Mengamankan password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Pendaftaran selalu menggunakan role mahasiswa
$role = 'mahasiswa';

// Menyimpan data pengguna
$stmt = $db->prepare(
    "INSERT INTO users (nim, nama, email, password, role)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $nim,
    $nama,
    $email,
    $password_hash,
    $role
);

if ($stmt->execute()) {
    header("Location: ../index.php?status=register_success");
    exit;
} else {
    die("Registrasi gagal: " . $stmt->error);
}

$stmt->close();
$db->close();
?>