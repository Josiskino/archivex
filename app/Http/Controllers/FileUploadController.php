<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Log pour diagnostiquer les problèmes
        Log::info('Upload attempt', [
            'user_id' => Auth::id(),
            'file_size' => $request->file('file')?->getSize(),
            'file_type' => $request->file('file')?->getMimeType(),
            'file_name' => $request->file('file')?->getClientOriginalName(),
            'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN'),
            'csrf_token_length' => strlen($request->header('X-CSRF-TOKEN', '')),
        ]);

        // Validation des fichiers avec des limites plus réalistes
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max (correspond à la nouvelle config)
            'folder' => 'nullable|string|max:255',
            'assigned_users' => 'nullable|array',
            'assigned_groups' => 'nullable|array',
            'permissions' => 'nullable|array'
        ], [
            'file.max' => 'Le fichier ne peut pas dépasser 50MB.',
            'file.required' => 'Aucun fichier n\'a été sélectionné.',
            'file.file' => 'Le fichier uploadé n\'est pas valide.'
        ]);

        try {
            $file = $request->file('file');
            $folder = $request->input('folder', '');
            
            // Vérifier le type MIME pour les PDF
            $mimeType = $file->getMimeType();
            $allowedMimes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/bmp',
                'image/tiff',
                'text/plain',
                'text/html',
                'text/css',
                'text/javascript',
                'application/json',
                'application/xml',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/csv',
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'video/mp4',
                'video/avi',
                'video/mov',
                'video/wmv',
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'audio/mp4'
            ];
            
            // Vérification spéciale pour les PDF
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension === 'pdf' && $mimeType !== 'application/pdf') {
                // Certains PDF peuvent avoir des types MIME différents
                Log::info('PDF with non-standard MIME type', [
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'file_name' => $file->getClientOriginalName()
                ]);
                // On accepte quand même si l'extension est .pdf
            } elseif (!in_array($mimeType, $allowedMimes)) {
                Log::warning('Invalid MIME type', [
                    'mime_type' => $mimeType, 
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $extension
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Type de fichier non autorisé: ' . $mimeType . ' (Extension: ' . $extension . ')'
                ], 400);
            }
            
            // Générer un nom unique pour le fichier
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Définir le chemin de stockage
            $path = $folder ? "uploads/{$folder}" : 'uploads';
            
            // Créer le dossier s'il n'existe pas
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
            
            // Stocker le fichier
            $filePath = $file->storeAs($path, $filename, 'public');
            
            // Créer un enregistrement en base de données (vous pouvez créer un modèle File plus tard)
            $fileData = [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $mimeType,
                'folder' => $folder,
                'uploaded_at' => now(),
                'user_id' => Auth::id(),
                'assigned_users' => $request->input('assigned_users', []),
                'assigned_groups' => $request->input('assigned_groups', []),
                'permissions' => $request->input('permissions', ['read'])
            ];
            
            // Ici vous pourriez sauvegarder en base de données
            // File::create($fileData);
            
            Log::info('File uploaded successfully', [
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Fichier téléchargé avec succès',
                'file' => $fileData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Upload error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getFiles(Request $request)
    {
        $folder = $request->input('folder', '');
        $userId = Auth::id();
        
        try {
            $files = [];
            $totalSize = 0;
            
            // Définir le chemin base
            $basePath = storage_path('app/public/uploads');
            $searchPath = $folder ? "{$basePath}/{$folder}" : $basePath;
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($basePath)) {
                mkdir($basePath, 0755, true);
            }
            
            // Créer les dossiers standard s'ils n'existent pas
            $standardFolders = ['documents', 'images', 'videos', 'audio'];
            foreach ($standardFolders as $stdFolder) {
                $folderPath = "{$basePath}/{$stdFolder}";
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }
            }
            
            // Lister les fichiers dans le dossier demandé
            if (is_dir($searchPath)) {
                $items = scandir($searchPath);
                
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') continue;
                    
                    $itemPath = "{$searchPath}/{$item}";
                    $isDir = is_dir($itemPath);
                    
                    if ($isDir) {
                        // C'est un dossier
                        $files[] = [
                            'id' => $folder ? "{$folder}/{$item}" : $item,
                            'name' => $item,
                            'type' => 'folder',
                            'createdAt' => date('c', filectime($itemPath)),
                            'updatedAt' => date('c', filemtime($itemPath)),
                            'parent' => $folder ?: null
                        ];
                    } else {
                        // C'est un fichier
                        $fileSize = filesize($itemPath);
                        $totalSize += $fileSize;
                        
                        // Déterminer le type MIME
                        $mimeType = mime_content_type($itemPath) ?: 'application/octet-stream';
                        
                        // Générer l'URL d'accès au fichier
                        $fileUrl = "/storage/uploads/" . ($folder ? $folder . '/' : '') . $item;
                        
                        // Déterminer si c'est une image pour la prévisualisation
                        $isImage = str_starts_with($mimeType, 'image/');
                        
                        $files[] = [
                            'id' => uniqid(),
                            'name' => $item,
                            'type' => 'file',
                            'size' => $fileSize,
                            'mimeType' => $mimeType,
                            'createdAt' => date('c', filectime($itemPath)),
                            'updatedAt' => date('c', filemtime($itemPath)),
                            'parent' => $folder ?: null,
                            'url' => $fileUrl,
                            'preview' => $isImage ? $fileUrl : null,
                            'isImage' => $isImage,
                            'extension' => pathinfo($item, PATHINFO_EXTENSION)
                        ];
                    }
                }
            }
            
            // Calculer l'espace utilisé total
            $totalUsedBytes = $this->calculateDirectorySize($basePath);
            $totalUsedGB = round($totalUsedBytes / (1024 * 1024 * 1024), 1);
            $totalGB = 25; // Limite de 25GB
            $percentage = min(($totalUsedGB / $totalGB) * 100, 100);
            
            // Trier les fichiers : dossiers en premier, puis par nom
            usort($files, function($a, $b) {
                if ($a['type'] !== $b['type']) {
                    return $a['type'] === 'folder' ? -1 : 1;
                }
                return strcasecmp($a['name'], $b['name']);
            });
            
            return response()->json([
                'success' => true,
                'files' => $files,
                'storage' => [
                    'used' => $totalUsedGB,
                    'total' => $totalGB,
                    'usedFormatted' => "{$totalUsedGB} GB",
                    'totalFormatted' => "{$totalGB} GB",
                    'percentage' => round($percentage)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des fichiers: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function calculateDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $size;
    }
    
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent' => 'nullable|string'
        ]);
        
        $folderData = [
            'id' => uniqid(),
            'name' => $request->input('name'),
            'type' => 'folder',
            'createdAt' => now()->toISOString(),
            'updatedAt' => now()->toISOString(),
            'parent' => $request->input('parent'),
            'user_id' => Auth::id()
        ];
        
        // Ici vous sauvegarderiez en base de données
        // Folder::create($folderData);
        
        return response()->json([
            'success' => true,
            'message' => 'Dossier créé avec succès',
            'folder' => $folderData
        ]);
    }
    
    public function deleteItems(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string'
        ]);
        
        $ids = $request->input('ids');
        $userId = Auth::id();
        
        // Ici vous supprimeriez de la base de données
        // File::whereIn('id', $ids)->where('user_id', $userId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => count($ids) . ' élément(s) supprimé(s) avec succès'
        ]);
    }

    // Nouvelle méthode pour assigner des fichiers (Admin uniquement)
    public function assignFile(Request $request, $fileId)
    {
        // Vérifier que l'utilisateur est admin
        if (!$this->userHasPermission('files.assign')) {
            return response()->json([
                'success' => false,
                'message' => 'Permission insuffisante'
            ], 403);
        }

        $request->validate([
            'users' => 'nullable|array',
            'groups' => 'nullable|array',
            'permissions' => 'required|array',
            'permissions.*' => 'in:read,write,delete'
        ]);

        // Ici vous mettriez à jour les assignations en base
        // $file = File::findOrFail($fileId);
        // $file->assignedUsers()->sync($request->users ?? []);
        // $file->assignedGroups()->sync($request->groups ?? []);
        // $file->permissions = $request->permissions;
        // $file->save();

        return response()->json([
            'success' => true,
            'message' => 'Fichier assigné avec succès'
        ]);
    }

    // Vérification des permissions utilisateur
    private function userHasPermission($permission)
    {
        $user = Auth::user();
        if (!$user) return false;

        // Simulation - à remplacer par votre logique réelle
        if (str_contains($user->email, 'admin')) return true;
        if (str_contains($user->email, 'manager') && in_array($permission, ['files.read', 'files.write', 'files.assign'])) return true;
        
        return in_array($permission, ['files.read', 'files.write']);
    }
}
