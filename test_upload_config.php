<?php
/**
 * Script de test pour diagnostiquer les problèmes d'upload
 * Placez ce fichier dans le dossier public et accédez-y via http://votre-site.com/test_upload_config.php
 */

echo "<h1>Test de Configuration Upload - ArchiveX</h1>";

// Vérifier les configurations PHP
echo "<h2>Configurations PHP</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Configuration</th><th>Valeur</th><th>Statut</th></tr>";

$configs = [
    'upload_max_filesize' => '50M',
    'post_max_size' => '50M',
    'memory_limit' => '512M',
    'max_execution_time' => '300',
    'max_input_time' => '300',
    'max_file_uploads' => '20'
];

foreach ($configs as $config => $recommended) {
    $current = ini_get($config);
    $status = '✅ OK';
    $color = 'green';
    
    if ($config === 'upload_max_filesize' || $config === 'post_max_size') {
        $currentBytes = return_bytes($current);
        $recommendedBytes = return_bytes($recommended);
        if ($currentBytes < $recommendedBytes) {
            $status = '⚠️ Trop petit';
            $color = 'orange';
        }
    }
    
    echo "<tr>";
    echo "<td><strong>$config</strong></td>";
    echo "<td>$current</td>";
    echo "<td style='color: $color;'>$status</td>";
    echo "</tr>";
}

echo "</table>";

// Vérifier les permissions des dossiers
echo "<h2>Permissions des Dossiers</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Dossier</th><th>Existe</th><th>Écriture</th><th>Permissions</th></tr>";

$folders = [
    'storage/app/public' => '../storage/app/public',
    'storage/app/public/uploads' => '../storage/app/public/uploads',
    'bootstrap/cache' => '../bootstrap/cache'
];

foreach ($folders as $name => $path) {
    $exists = is_dir($path);
    $writable = is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>$perms</td>";
    echo "</tr>";
}

echo "</table>";

// Test de création de fichier
echo "<h2>Test de Création de Fichier</h2>";
$testFile = '../storage/app/public/test_upload.txt';
$testContent = 'Test upload ' . date('Y-m-d H:i:s');

try {
    if (file_put_contents($testFile, $testContent)) {
        echo "<p style='color: green;'>✅ Test de création de fichier réussi</p>";
        unlink($testFile); // Nettoyer
    } else {
        echo "<p style='color: red;'>❌ Échec de création de fichier</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Vérifier les extensions PHP
echo "<h2>Extensions PHP Requises</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Extension</th><th>Statut</th></tr>";

$extensions = ['fileinfo', 'gd', 'zip', 'mbstring', 'openssl'];

foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<tr>";
    echo "<td>$ext</td>";
    echo "<td>" . ($loaded ? '✅ Chargée' : '❌ Manquante') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Fonction utilitaire pour convertir les tailles
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

echo "<h2>Recommandations</h2>";
echo "<ul>";
echo "<li>Si des configurations sont trop petites, modifiez le fichier <code>.user.ini</code> dans le dossier public</li>";
echo "<li>Si les permissions sont incorrectes, exécutez: <code>chmod -R 755 storage/</code></li>";
echo "<li>Si des extensions manquent, installez-les via votre gestionnaire de paquets</li>";
echo "</ul>";

echo "<p><strong>Note:</strong> Ce script doit être supprimé après utilisation pour des raisons de sécurité.</p>";
?> 