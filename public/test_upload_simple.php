<?php
/**
 * Test simple d'upload
 * Accédez à http://votre-site.com/test_upload_simple.php
 */

// Vérifier si un fichier a été uploadé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    
    echo "<h2>Résultat de l'upload</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Propriété</th><th>Valeur</th></tr>";
    echo "<tr><td>Nom original</td><td>" . htmlspecialchars($file['name']) . "</td></tr>";
    echo "<tr><td>Taille</td><td>" . number_format($file['size'] / 1024 / 1024, 2) . " MB</td></tr>";
    echo "<tr><td>Type MIME</td><td>" . htmlspecialchars($file['type']) . "</td></tr>";
    echo "<tr><td>Extension</td><td>" . pathinfo($file['name'], PATHINFO_EXTENSION) . "</td></tr>";
    echo "<tr><td>Erreur</td><td>" . $file['error'] . "</td></tr>";
    echo "</table>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "<p style='color: green;'>✅ Upload réussi !</p>";
        
        // Tester le stockage
        $uploadDir = '../storage/app/public/test/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = time() . '_' . $file['name'];
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            echo "<p style='color: green;'>✅ Fichier stocké avec succès: $filepath</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur lors du stockage</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Erreur d'upload: " . $file['error'] . "</p>";
    }
    
    echo "<hr>";
}

// Afficher le formulaire de test
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Upload - ArchiveX</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input[type="file"] { padding: 10px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Test d'Upload - ArchiveX</h1>
    
    <div class="info">
        <h3>Informations système</h3>
        <p><strong>Upload max:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
        <p><strong>Post max:</strong> <?php echo ini_get('post_max_size'); ?></p>
        <p><strong>Memory limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
        <p><strong>Max execution time:</strong> <?php echo ini_get('max_execution_time'); ?>s</p>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="test_file">Sélectionner un fichier à tester:</label>
            <input type="file" id="test_file" name="test_file" accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.txt,.zip">
        </div>
        
        <div class="form-group">
            <button type="submit">Tester l'upload</button>
        </div>
    </form>
    
    <div class="info">
        <h3>Instructions</h3>
        <ul>
            <li>Testez avec différents types de fichiers (PDF, images, documents)</li>
            <li>Testez avec des fichiers de différentes tailles</li>
            <li>Vérifiez les messages d'erreur</li>
            <li>Supprimez ce fichier après les tests</li>
        </ul>
    </div>
</body>
</html> 