<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { 
    Upload as UploadIcon, 
    FileText, 
    Image, 
    FileVideo, 
    Music, 
    File, 
    X, 
    Check, 
    AlertCircle,
    Folder,
    Plus
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { ref, reactive, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Télécharger des fichiers',
        href: '/upload',
    },
];

interface FileUpload {
    id: string;
    file: File;
    progress: number;
    status: 'pending' | 'uploading' | 'completed' | 'error';
    error?: string;
}

const files = ref<FileUpload[]>([]);
const isDragOver = ref(false);
const fileInput = ref<HTMLInputElement>();
const selectedFolder = ref('');

// Dossiers disponibles
const folders = [
    { value: '', label: 'Racine' },
    { value: 'documents', label: 'Documents' },
    { value: 'images', label: 'Images' },
    { value: 'videos', label: 'Vidéos' },
    { value: 'audio', label: 'Audio' },
    { value: 'presentations', label: 'Présentations' },
    { value: 'rapports', label: 'Rapports' },
];

const stats = computed(() => {
    const total = files.value.length;
    const completed = files.value.filter(f => f.status === 'completed').length;
    const uploading = files.value.filter(f => f.status === 'uploading').length;
    const pending = files.value.filter(f => f.status === 'pending').length;
    const errors = files.value.filter(f => f.status === 'error').length;
    
    return { total, completed, uploading, pending, errors };
});

const getFileIcon = (file: File) => {
    const type = file.type;
    if (type.startsWith('image/')) return Image;
    if (type.startsWith('video/')) return FileVideo;
    if (type.startsWith('audio/')) return Music;
    if (type.includes('pdf') || type.includes('document') || type.includes('text')) return FileText;
    return File;
};

const getFileColor = (file: File) => {
    const type = file.type;
    if (type.startsWith('image/')) return 'text-green-600';
    if (type.startsWith('video/')) return 'text-purple-600';
    if (type.startsWith('audio/')) return 'text-yellow-600';
    if (type.includes('pdf')) return 'text-red-600';
    if (type.includes('document') || type.includes('text')) return 'text-blue-600';
    return 'text-gray-600';
};

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const generateId = () => Math.random().toString(36).substr(2, 9);

const addFiles = (newFiles: FileList | File[]) => {
    const fileArray = Array.from(newFiles);
    
    // Types de fichiers autorisés
    const allowedTypes = [
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
    
    // Extensions autorisées (fallback)
    const allowedExtensions = [
        'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff',
        'txt', 'html', 'htm', 'css', 'js', 'json', 'xml',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv',
        'zip', 'rar', '7z', 'mp4', 'avi', 'mov', 'wmv',
        'mp3', 'wav', 'ogg', 'm4a'
    ];
    
    fileArray.forEach(file => {
        // Vérifier si le fichier n'est pas déjà ajouté
        const exists = files.value.some(f => 
            f.file.name === file.name && 
            f.file.size === file.size && 
            f.file.lastModified === file.lastModified
        );
        
        if (!exists) {
            // Validation du type de fichier
            const fileExtension = file.name.split('.').pop()?.toLowerCase();
            const isValidType = allowedTypes.includes(file.type) || 
                               (fileExtension && allowedExtensions.includes(fileExtension));
            
            if (!isValidType) {
                console.warn(`Type de fichier non autorisé: ${file.type} (${file.name})`);
                // On ajoute quand même mais avec un statut d'erreur
                const fileUpload: FileUpload = {
                    id: generateId(),
                    file,
                    progress: 0,
                    status: 'error',
                    error: `Type de fichier non autorisé: ${file.type}`
                };
                files.value.push(fileUpload);
                return;
            }
            
            // Validation de la taille (50MB max)
            const maxSize = 50 * 1024 * 1024;
            if (file.size > maxSize) {
                const fileUpload: FileUpload = {
                    id: generateId(),
                    file,
                    progress: 0,
                    status: 'error',
                    error: `Fichier trop volumineux: ${formatFileSize(file.size)} (max: 50MB)`
                };
                files.value.push(fileUpload);
                return;
            }
            
            const fileUpload: FileUpload = {
                id: generateId(),
                file,
                progress: 0,
                status: 'pending'
            };
            files.value.push(fileUpload);
        }
    });
};

const removeFile = (id: string) => {
    const index = files.value.findIndex(f => f.id === id);
    if (index > -1) {
        files.value.splice(index, 1);
    }
};

const uploadFile = async (fileUpload: FileUpload) => {
    fileUpload.status = 'uploading';
    fileUpload.progress = 0;
    
    const formData = new FormData();
    formData.append('file', fileUpload.file);
    formData.append('folder', selectedFolder.value);
    
    try {
        // Créer une requête avec suivi de progression
        const xhr = new XMLHttpRequest();
        
        // Suivre la progression de l'upload
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                fileUpload.progress = Math.round((e.loaded / e.total) * 100);
            }
        });
        
        // Promesse pour gérer la réponse
        const uploadPromise = new Promise((resolve, reject) => {
            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            resolve(response);
                        } else {
                            reject(new Error(response.message || 'Erreur lors de l\'upload'));
                        }
                    } catch (e) {
                        reject(new Error('Réponse invalide du serveur'));
                    }
                } else if (xhr.status === 419) {
                    reject(new Error('Erreur CSRF: Veuillez rafraîchir la page et réessayer'));
                } else if (xhr.status === 413) {
                    reject(new Error('Fichier trop volumineux (max 40MB)'));
                } else if (xhr.status === 400) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        reject(new Error(response.message || 'Fichier non autorisé'));
                    } catch (e) {
                        reject(new Error('Fichier non autorisé'));
                    }
                } else {
                    reject(new Error(`Erreur serveur: ${xhr.status}`));
                }
            };
            
            xhr.onerror = () => reject(new Error('Erreur réseau'));
            xhr.ontimeout = () => reject(new Error('Timeout de la requête'));
        });
        
        // Obtenir le token CSRF de manière plus robuste
        let csrfToken = '';
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            csrfToken = metaTag.getAttribute('content') || '';
        }
        
        // Si pas de token dans le meta, essayer de le récupérer via une requête
        if (!csrfToken) {
            try {
                const tokenResponse = await fetch('/api/csrf-token', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (tokenResponse.ok) {
                    const tokenData = await tokenResponse.json();
                    csrfToken = tokenData.token;
                }
            } catch (e) {
                console.warn('Impossible de récupérer le token CSRF:', e);
            }
        }
        
        // Configurer et envoyer la requête
        xhr.open('POST', '/api/upload');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.timeout = 300000; // 5 minutes timeout
        
        // Vérifier la taille du fichier côté client
        const maxSize = 50 * 1024 * 1024; // 50MB
        if (fileUpload.file.size > maxSize) {
            throw new Error(`Fichier trop volumineux: ${formatFileSize(fileUpload.file.size)} (max: 50MB)`);
        }
        
        xhr.send(formData);
        
        // Attendre la réponse
        await uploadPromise;
        
        fileUpload.status = 'completed';
        fileUpload.progress = 100;
    } catch (error) {
        fileUpload.status = 'error';
        fileUpload.error = error instanceof Error ? error.message : 'Erreur lors du téléchargement';
        
        // Log de l'erreur pour debug
        console.error('Upload error for file:', fileUpload.file.name, error);
    }
};

const uploadAll = async () => {
    const pendingFiles = files.value.filter(f => f.status === 'pending');
    
    // Upload en parallèle (max 3 à la fois)
    const batchSize = 3;
    for (let i = 0; i < pendingFiles.length; i += batchSize) {
        const batch = pendingFiles.slice(i, i + batchSize);
        await Promise.all(batch.map(uploadFile));
    }
};

const retryFile = (id: string) => {
    const fileUpload = files.value.find(f => f.id === id);
    if (fileUpload) {
        fileUpload.status = 'pending';
        fileUpload.error = undefined;
        uploadFile(fileUpload);
    }
};

const clearCompleted = () => {
    files.value = files.value.filter(f => f.status !== 'completed');
};

const clearAll = () => {
    files.value = [];
};

// Gestion du drag & drop
const onDragOver = (e: DragEvent) => {
    e.preventDefault();
    isDragOver.value = true;
};

const onDragLeave = (e: DragEvent) => {
    e.preventDefault();
    isDragOver.value = false;
};

const onDrop = (e: DragEvent) => {
    e.preventDefault();
    isDragOver.value = false;
    
    if (e.dataTransfer?.files) {
        addFiles(e.dataTransfer.files);
    }
};

const onFileSelect = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        addFiles(target.files);
    }
};

const selectFiles = () => {
    fileInput.value?.click();
};
</script>

<template>
    <Head title="Télécharger des fichiers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- En-tête -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Télécharger des fichiers
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400">
                        Glissez-déposez vos fichiers ou cliquez pour les sélectionner
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <select 
                        v-model="selectedFolder"
                        class="px-3 py-2 border border-slate-300 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600"
                    >
                        <option v-for="folder in folders" :key="folder.value" :value="folder.value">
                            {{ folder.label }}
                        </option>
                    </select>
                    <Button @click="selectFiles" variant="outline">
                        <Plus class="w-4 h-4 mr-2" />
                        Sélectionner
                    </Button>
                </div>
            </div>

            <!-- Zone de drop -->
            <Card 
                :class="[
                    'relative transition-all duration-200 cursor-pointer',
                    isDragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/20' : 'border-dashed border-slate-300 dark:border-slate-600'
                ]"
                @dragover="onDragOver"
                @dragleave="onDragLeave"
                @drop="onDrop"
                @click="selectFiles"
            >
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <UploadIcon 
                        :class="[
                            'w-16 h-16 mb-4',
                            isDragOver ? 'text-blue-500' : 'text-slate-400'
                        ]" 
                    />
                    <h3 class="text-lg font-semibold mb-2">
                        {{ isDragOver ? 'Déposez vos fichiers ici' : 'Glissez-déposez vos fichiers' }}
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-center mb-4">
                        ou cliquez pour parcourir vos fichiers
                    </p>
                    <div class="flex flex-wrap justify-center gap-2 text-xs text-slate-500">
                        <span>PDF, DOC, JPG, PNG, MP4, MP3</span>
                        <span>•</span>
                        <span>Max 100MB par fichier</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Input file caché -->
            <input 
                ref="fileInput"
                type="file" 
                multiple 
                class="hidden" 
                @change="onFileSelect"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp4,.mp3,.wav"
            />

            <!-- Statistiques et actions -->
            <div v-if="files.length > 0" class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Badge variant="secondary">{{ stats.total }} fichier(s)</Badge>
                    <Badge v-if="stats.completed > 0" variant="default">{{ stats.completed }} complété(s)</Badge>
                    <Badge v-if="stats.uploading > 0" class="bg-blue-100 text-blue-800">{{ stats.uploading }} en cours</Badge>
                    <Badge v-if="stats.errors > 0" variant="destructive">{{ stats.errors }} erreur(s)</Badge>
                </div>
                <div class="flex items-center space-x-2">
                    <Button @click="clearCompleted" variant="outline" size="sm" v-if="stats.completed > 0">
                        Effacer terminés
                    </Button>
                    <Button @click="clearAll" variant="outline" size="sm">
                        Tout effacer
                    </Button>
                    <Button 
                        @click="uploadAll" 
                        :disabled="stats.pending === 0"
                        class="bg-blue-600 hover:bg-blue-700"
                    >
                        <UploadIcon class="w-4 h-4 mr-2" />
                        Télécharger tout ({{ stats.pending }})
                    </Button>
                </div>
            </div>

            <!-- Liste des fichiers -->
            <div v-if="files.length > 0" class="space-y-4">
                <Card v-for="fileUpload in files" :key="fileUpload.id">
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-4">
                            <!-- Icône de fichier -->
                            <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                <component 
                                    :is="getFileIcon(fileUpload.file)" 
                                    :class="`w-6 h-6 ${getFileColor(fileUpload.file)}`" 
                                />
                            </div>

                            <!-- Informations du fichier -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-sm truncate">{{ fileUpload.file.name }}</h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xs text-slate-500">{{ formatFileSize(fileUpload.file.size) }}</span>
                                    <Badge 
                                        :variant="
                                            fileUpload.status === 'completed' ? 'default' :
                                            fileUpload.status === 'error' ? 'destructive' :
                                            fileUpload.status === 'uploading' ? 'secondary' : 'outline'
                                        "
                                        class="text-xs"
                                    >
                                        {{
                                            fileUpload.status === 'completed' ? 'Terminé' :
                                            fileUpload.status === 'error' ? 'Erreur' :
                                            fileUpload.status === 'uploading' ? 'En cours' : 'En attente'
                                        }}
                                    </Badge>
                                </div>

                                <!-- Barre de progression -->
                                <div v-if="fileUpload.status === 'uploading'" class="mt-2">
                                    <Progress :value="fileUpload.progress" class="h-2" />
                                    <p class="text-xs text-slate-500 mt-1">{{ fileUpload.progress }}%</p>
                                </div>

                                <!-- Message d'erreur -->
                                <div v-if="fileUpload.status === 'error'" class="mt-2 flex items-center space-x-2">
                                    <AlertCircle class="w-4 h-4 text-red-500" />
                                    <span class="text-xs text-red-600">{{ fileUpload.error }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <Button 
                                    v-if="fileUpload.status === 'pending'" 
                                    @click="uploadFile(fileUpload)"
                                    size="sm"
                                    variant="outline"
                                >
                                    <UploadIcon class="w-4 h-4" />
                                </Button>
                                <Button 
                                    v-if="fileUpload.status === 'error'" 
                                    @click="retryFile(fileUpload.id)"
                                    size="sm"
                                    variant="outline"
                                >
                                    Réessayer
                                </Button>
                                <Button 
                                    v-if="fileUpload.status === 'completed'" 
                                    size="sm"
                                    variant="ghost"
                                    disabled
                                >
                                    <Check class="w-4 h-4 text-green-600" />
                                </Button>
                                <Button 
                                    @click="removeFile(fileUpload.id)"
                                    size="sm"
                                    variant="ghost"
                                    :disabled="fileUpload.status === 'uploading'"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Message vide -->
            <div v-if="files.length === 0" class="text-center py-12">
                <Folder class="w-16 h-16 mx-auto text-slate-300 mb-4" />
                <h3 class="text-lg font-medium text-slate-600 dark:text-slate-400 mb-2">
                    Aucun fichier sélectionné
                </h3>
                <p class="text-slate-500 dark:text-slate-500">
                    Commencez par ajouter des fichiers à télécharger
                </p>
            </div>
        </div>
    </AppLayout>
</template> 