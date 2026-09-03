<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Pastikan function.php di-load jika belum
require_once __DIR__ . '/../function.php';

// Deteksi base URL secara dinamis
$baseUrlManuallySet = defined('BASE_URL') && BASE_URL !== '/';
$baseUrl = '/';
if (defined('BASE_URL')) {
    $baseUrl = rtrim(BASE_URL, '/') . '/';
}
if (!$baseUrlManuallySet) {
    $scriptFilename  = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
    $projectRootPath = realpath(dirname(__DIR__));
    $rawRequestPath  = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

    if ($scriptFilename && $projectRootPath && str_starts_with($scriptFilename, $projectRootPath)) {
        $relativePath = substr($scriptFilename, strlen($projectRootPath));
        if ($relativePath !== '' && str_ends_with($rawRequestPath, $relativePath)) {
            $computedBase = substr($rawRequestPath, 0, -strlen($relativePath));
            $baseUrl = ($computedBase === '' ? '/' : rtrim($computedBase, '/') . '/');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'EduCare - Platform Belajar Digital') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS v4 Compiled -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>assets/css/output.css">
    <!-- Custom style -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>assets/css/style.css">
    
    <!-- Alpine.js & Lucide Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- i18n: Fitur Bahasa (ID/EN) -->
    <script src="<?= htmlspecialchars($baseUrl) ?>assets/js/i18n.js" defer></script>
    
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .text-gradient {
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-mesh {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 0%, rgba(168, 85, 247, 0.03) 0px, transparent 50%);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: none; }
        }
        .animate-fadeIn {
            animation: fadeInUp .35s ease both;
        }
    </style>
    <?php if (!empty($pageHeadCSS)): ?>
    <style>
        <?= $pageHeadCSS ?>
    </style>
    <?php endif; ?>
</head>
<body class="bg-mesh text-slate-800 antialiased overflow-x-hidden">
