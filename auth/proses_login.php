<?php
session_start();

require_once __DIR__ . '/../config/koneksi.php';

$db = $conn ?? $koneksi ?? null;

if (!$db) {
    die("Koneksi database tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// Mengambil data login
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if ($email === '' || $password === '') {
    die("Email dan password wajib diisi.");
}

// Mencari pengguna berdasarkan email
$stmt = $db->prepare(
    "SELECT id, nim, nama, email, password, role
     FROM users
     WHERE email = ?
     LIMIT 1"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$db->close();

// Memeriksa password
if (!$user || !password_verify($password, $user['password'])) {
    die("Email atau password salah.");
}

// Membuat session baru
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['nim'] = $user['nim'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

// Mengarahkan pengguna sesuai role
if ($user['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}

if ($user['role'] === 'mahasiswa') {
    header("Location: ../mahasiswa/dashboard.php");
    exit;
}

// Jika role tidak dikenal
session_unset();
session_destroy();

die("Role pengguna tidak dikenali.");
?>