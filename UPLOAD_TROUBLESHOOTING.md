# Guide de Dépannage - Problèmes d'Upload ArchiveX

## Problèmes Identifiés et Solutions

### 1. Erreur 419 (CSRF Token)

**Symptômes :**
- Erreur 419 lors de l'upload
- Message "Page Expired" ou "CSRF token mismatch"

**Solutions :**
1. **Rafraîchir la page** avant d'uploader
2. **Vérifier la session** - se reconnecter si nécessaire
3. **Nettoyer le cache** du navigateur
4. **Vérifier le token CSRF** dans la console du navigateur

**Code de diagnostic :**
```javascript
// Dans la console du navigateur
console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
```

### 2. Problèmes avec les PDF

**Symptômes :**
- Les PDF ne s'uploadent pas même s'ils font moins de 3MB
- Erreur "Type de fichier non autorisé"

**Solutions :**
1. **Vérifier le type MIME** du PDF
2. **Accepter les PDF avec des types MIME non-standard**
3. **Validation par extension** en plus du type MIME

**Types MIME acceptés pour les PDF :**
- `application/pdf` (standard)
- Extension `.pdf` (fallback)

### 3. Limites de Taille de Fichier

**Configurations actuelles :**
- **PHP :** 50MB max
- **Laravel :** 50MB max
- **Client :** 50MB max

**Vérification :**
```bash
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
```

**Si les limites sont trop petites :**
1. Modifier le fichier `.user.ini` dans le dossier `public/`
2. Redémarrer le serveur web
3. Vérifier avec le script de test

### 4. Script de Test

**Utilisation :**
1. Placer `test_upload_config.php` dans le dossier `public/`
2. Accéder à `http://votre-site.com/test_upload_config.php`
3. Vérifier tous les paramètres
4. **Supprimer le fichier après utilisation**

### 5. Logs et Debug

**Activation des logs :**
Les logs d'upload sont maintenant activés dans `storage/logs/laravel.log`

**Informations loggées :**
- Tentative d'upload
- Type MIME et taille du fichier
- Présence du token CSRF
- Erreurs détaillées

**Consultation des logs :**
```bash
tail -f storage/logs/laravel.log
```

### 6. Permissions des Dossiers

**Dossiers requis :**
- `storage/app/public/` (écriture)
- `storage/app/public/uploads/` (écriture)
- `bootstrap/cache/` (écriture)

**Correction des permissions :**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 7. Extensions PHP Requises

**Extensions nécessaires :**
- `fileinfo` (détection du type MIME)
- `gd` (traitement d'images)
- `zip` (archives)
- `mbstring` (chaînes multibytes)
- `openssl` (sécurité)

**Vérification :**
```bash
php -m | grep -E "(fileinfo|gd|zip|mbstring|openssl)"
```

### 8. Configuration Serveur

**Apache (.htaccess) :**
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value memory_limit 512M
php_value max_execution_time 300
```

**Nginx :**
```nginx
client_max_body_size 50M;
```

### 9. Tests de Validation

**Types de fichiers testés :**
- ✅ Images (JPEG, PNG, GIF, WebP, BMP, TIFF)
- ✅ PDF (avec validation spéciale)
- ✅ Documents Office (Word, Excel, PowerPoint)
- ✅ Archives (ZIP, RAR, 7Z)
- ✅ Vidéos (MP4, AVI, MOV, WMV)
- ✅ Audio (MP3, WAV, OGG, M4A)
- ✅ Textes (TXT, HTML, CSS, JS, JSON, XML, CSV)

### 10. Erreurs Courantes

**"Fichier trop volumineux"**
- Vérifier la taille du fichier
- Augmenter les limites PHP
- Vérifier la configuration serveur

**"Type de fichier non autorisé"**
- Vérifier l'extension du fichier
- Vérifier le type MIME
- Ajouter le type dans la liste autorisée

**"Erreur réseau"**
- Vérifier la connexion internet
- Vérifier la configuration du serveur
- Augmenter le timeout

### 11. Optimisations

**Pour les gros fichiers :**
- Upload en chunks (à implémenter)
- Barre de progression
- Reprise en cas d'échec

**Pour la performance :**
- Compression des images
- Validation côté client
- Cache des métadonnées

## Support

En cas de problème persistant :
1. Consulter les logs Laravel
2. Utiliser le script de test
3. Vérifier la configuration serveur
4. Tester avec différents types de fichiers 