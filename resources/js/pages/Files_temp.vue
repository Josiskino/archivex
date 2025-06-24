<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Folder,
    FolderPlus,
    FileText,
    Image,
    FileVideo,
    Music,
    File,
    MoreVertical,
    Download,
    Trash2,
    Eye,
    Grid,
    List,
    Search,
    Upload,
    HardDrive,
    Calendar,
    ArrowUp
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { ref, computed, onMounted, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Mes fichiers',
        href: '/files',
    },
];

interface FileItem {
    id: string;
    name: string;
    type: 'folder' | 'file';
    size?: number;
    mimeType?: string;
    createdAt: string;
    updatedAt: string;
    parent?: string;
    preview?: string;
    extension?: string;
}

interface StorageInfo {
    used: number;
    total: number;
    usedFormatted: string;
    totalFormatted: string;
    percentage: number;
}

// État de l'application
const currentPath = ref('');
const viewMode = ref<'grid' | 'list'>('grid');
const searchQuery = ref('');
const isCreateFolderOpen = ref(false);
const newFolderName = ref('');
const selectedItems = ref<string[]>([]);
const isLoading = ref(false);

// État pour l'aperçu d'image
const isImagePreviewOpen = ref(false);
const previewImage = ref<FileItem | null>(null);

// Variables pour gérer le double-clic
const clickTimeout = ref<NodeJS.Timeout | null>(null);
const clickCount = ref(0);

// Données réactives
const storageInfo = ref<StorageInfo>({
    used: 0,
    total: 25,
    usedFormatted: '0 GB',
    totalFormatted: '25 GB',
    percentage: 0
});

const files = ref<FileItem[]>([]);

// Fonctions API
const loadFiles = async () => {
    isLoading.value = true;
    try {
        const response = await fetch(`/api/files?folder=${currentPath.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        if (!response.ok) {
            throw new Error('Erreur lors du chargement des fichiers');
        }
        
        const data = await response.json();
        
        if (data.success) {
            files.value = data.files || [];
            storageInfo.value = data.storage || storageInfo.value;
        } else {
            console.error('Erreur API:', data.message);
        }
    } catch (error) {
        console.error('Erreur lors du chargement des fichiers:', error);
    } finally {
        isLoading.value = false;
    }
};

// Computed
const filteredFiles = computed(() => {
    let result = files.value;
    
    // Filtrer par recherche
    if (searchQuery.value) {
        result = result.filter(file => 
            file.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    
    // Les fichiers sont déjà filtrés par dossier côté API
    return result;
});

const currentFolder = computed(() => {
    if (!currentPath.value) return null;
    return files.value.find(f => f.id === currentPath.value && f.type === 'folder');
});

const pathBreadcrumbs = computed(() => {
    const crumbs = [{ name: 'Racine', path: '' }];
    if (currentFolder.value) {
        crumbs.push({ name: currentFolder.value.name, path: currentFolder.value.id });
    }
    return crumbs;
});

const fileStats = computed(() => {
    const total = filteredFiles.value.length;
    const folders = filteredFiles.value.filter(f => f.type === 'folder').length;
    const filesCount = filteredFiles.value.filter(f => f.type === 'file').length;
    
    return { total, folders, files: filesCount };
});

// Méthodes
const getFileIcon = (file: FileItem) => {
    if (file.type === 'folder') return Folder;
    
    const mimeType = file.mimeType || '';
    if (mimeType.startsWith('image/')) return Image;
    if (mimeType.startsWith('video/')) return FileVideo;
    if (mimeType.startsWith('audio/')) return Music;
    if (mimeType.includes('pdf') || mimeType.includes('document')) return FileText;
    return File;
};

const getFileColor = (file: FileItem) => {
    if (file.type === 'folder') return 'text-blue-600';
    
    const mimeType = file.mimeType || '';
    if (mimeType.startsWith('image/')) return 'text-green-600';
    if (mimeType.startsWith('video/')) return 'text-purple-600';
    if (mimeType.startsWith('audio/')) return 'text-yellow-600';
    if (mimeType.includes('pdf')) return 'text-red-600';
    if (mimeType.includes('document')) return 'text-blue-600';
    return 'text-gray-600';
};

const formatFileSize = (bytes?: number) => {
    if (!bytes) return '';
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const openFolder = (folderId: string) => {
    currentPath.value = folderId;
    loadFiles(); // Recharger les fichiers du nouveau dossier
};

const goBack = () => {
    currentPath.value = '';
    loadFiles(); // Recharger les fichiers de la racine
};

const goToPath = (path: string) => {
    currentPath.value = path;
    loadFiles(); // Recharger les fichiers du chemin sélectionné
};

const createFolder = async () => {
    if (!newFolderName.value.trim()) return;
    
    try {
        const response = await fetch('/api/folders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                name: newFolderName.value.trim(),
                parent: currentPath.value || null
            })
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                newFolderName.value = '';
                isCreateFolderOpen.value = false;
                loadFiles(); // Recharger la liste des fichiers
            }
        }
    } catch (error) {
        console.error('Erreur lors de la création du dossier:', error);
    }
};

const toggleSelection = (itemId: string) => {
    const index = selectedItems.value.indexOf(itemId);
    if (index > -1) {
        selectedItems.value.splice(index, 1);
    } else {
        selectedItems.value.push(itemId);
    }
};

// Gestion du double-clic pour les images
const handleFileClick = (file: FileItem) => {
    clickCount.value++;
    
    if (clickCount.value === 1) {
        // Premier clic - attendre pour voir s'il y a un double-clic
        clickTimeout.value = setTimeout(() => {
            // Simple clic - sélectionner le fichier
            if (file.type === 'folder') {
                openFolder(file.id);
            } else {
                toggleSelection(file.id);
            }
            clickCount.value = 0;
        }, 300); // Délai de 300ms pour détecter le double-clic
    } else if (clickCount.value === 2) {
        // Double-clic
        if (clickTimeout.value) {
            clearTimeout(clickTimeout.value);
            clickTimeout.value = null;
        }
        
        if (file.type === 'file' && file.mimeType?.startsWith('image/')) {
            // C'est une image - ouvrir l'aperçu
            openImagePreview(file);
        } else if (file.type === 'folder') {
            // C'est un dossier - ouvrir
            openFolder(file.id);
        } else {
            // Autre type de fichier - télécharger ou ouvrir
            downloadFile(file);
        }
        
        clickCount.value = 0;
    }
};

// Ouvrir l'aperçu d'image
const openImagePreview = (file: FileItem) => {
    previewImage.value = file;
    isImagePreviewOpen.value = true;
};

// Fermer l'aperçu d'image
const closeImagePreview = () => {
    isImagePreviewOpen.value = false;
    previewImage.value = null;
};

// Télécharger un fichier
const downloadFile = (file: FileItem) => {
    if (file.url) {
        const link = document.createElement('a');
        link.href = file.url;
        link.download = file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

const selectAll = () => {
    if (selectedItems.value.length === filteredFiles.value.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = filteredFiles.value.map(f => f.id);
    }
};

const deleteSelected = async () => {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer ${selectedItems.value.length} élément(s) ?`)) {
        return;
    }
    
    try {
        const response = await fetch('/api/files/delete', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                ids: selectedItems.value
            })
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                selectedItems.value = [];
                loadFiles(); // Recharger la liste des fichiers
            }
        }
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
    }
};

const getItemPreview = (file: FileItem) => {
    if (file.type === 'folder') {
        const folderFiles = files.value.filter(f => f.parent === file.id);
        return `${folderFiles.length} élément(s)`;
    }
    return formatFileSize(file.size);
};

// Fonctions utilitaires pour les types de fichiers
const getFileTypeLabel = (mimeType?: string) => {
    if (!mimeType) return 'Fichier';
    
    if (mimeType.startsWith('image/')) return 'Image';
    if (mimeType.startsWith('video/')) return 'Vidéo';
    if (mimeType.startsWith('audio/')) return 'Audio';
    if (mimeType.includes('pdf')) return 'PDF';
    if (mimeType.includes('document') || mimeType.includes('word')) return 'Document';
    if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) return 'Tableur';
    if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return 'Présentation';
    if (mimeType.includes('text')) return 'Texte';
    if (mimeType.includes('zip') || mimeType.includes('archive')) return 'Archive';
    
    return 'Fichier';
};

const getFileTypeColor = (mimeType?: string) => {
    if (!mimeType) return 'border-gray-300 text-gray-600';
    
    if (mimeType.startsWith('image/')) return 'border-green-300 text-green-700 bg-green-50';
    if (mimeType.startsWith('video/')) return 'border-purple-300 text-purple-700 bg-purple-50';
    if (mimeType.startsWith('audio/')) return 'border-yellow-300 text-yellow-700 bg-yellow-50';
    if (mimeType.includes('pdf')) return 'border-red-300 text-red-700 bg-red-50';
    if (mimeType.includes('document') || mimeType.includes('word')) return 'border-blue-300 text-blue-700 bg-blue-50';
    if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) return 'border-emerald-300 text-emerald-700 bg-emerald-50';
    if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return 'border-orange-300 text-orange-700 bg-orange-50';
    if (mimeType.includes('text')) return 'border-slate-300 text-slate-700 bg-slate-50';
    if (mimeType.includes('zip') || mimeType.includes('archive')) return 'border-indigo-300 text-indigo-700 bg-indigo-50';
    
    return 'border-gray-300 text-gray-600 bg-gray-50';
};

// Charger les fichiers au montage du composant
onMounted(() => {
    loadFiles();
});

// Watcher pour recharger les fichiers quand le chemin change
watch(currentPath, () => {
    selectedItems.value = []; // Réinitialiser la sélection
});
</script>

<template>
    <Head title="Mes fichiers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- En-tête avec navigation et actions -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Mes fichiers
                    </h1>
                    <!-- Breadcrumbs du chemin -->
                    <nav class="flex items-center space-x-2 text-sm text-slate-600 dark:text-slate-400 mt-2">
                        <button 
                            v-for="(crumb, index) in pathBreadcrumbs" 
                            :key="index"
                            @click="goToPath(crumb.path)"
                            :class="[
                                'hover:text-blue-600 transition-colors',
                                index === pathBreadcrumbs.length - 1 ? 'text-slate-900 dark:text-white font-medium' : ''
                            ]"
                        >
                            {{ crumb.name }}
                            <span v-if="index < pathBreadcrumbs.length - 1" class="mx-2">/</span>
                        </button>
                    </nav>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Rechercher..." 
                            class="pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-sm"
                        />
                    </div>
                    <Dialog v-model:open="isCreateFolderOpen">
                        <DialogTrigger as-child>
                            <Button variant="outline">
                                <FolderPlus class="w-4 h-4 mr-2" />
                                Nouveau dossier
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Créer un nouveau dossier</DialogTitle>
                                <DialogDescription>
                                    Donnez un nom à votre nouveau dossier
                                </DialogDescription>
                            </DialogHeader>
                            <div class="space-y-4 pt-4">
                                <input 
                                    v-model="newFolderName"
                                    type="text" 
                                    placeholder="Nom du dossier" 
                                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800"
                                    @keyup.enter="createFolder"
                                />
                                <div class="flex justify-end space-x-2">
                                    <Button variant="outline" @click="isCreateFolderOpen = false">
                                        Annuler
                                    </Button>
                                    <Button @click="createFolder" :disabled="!newFolderName.trim()">
                                        Créer
                                    </Button>
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>
                    <Button as-child class="bg-blue-600 hover:bg-blue-700">
                        <Link :href="route('upload')">
                            <Upload class="w-4 h-4 mr-2" />
                            Télécharger
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Barre de stockage -->
            <Card>
                <CardContent class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <HardDrive class="w-5 h-5 text-blue-600" />
                            <span class="font-medium">Espace de stockage</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">
                            {{ storageInfo.usedFormatted }} / {{ storageInfo.totalFormatted }}
                        </span>
                    </div>
                    <Progress :value="storageInfo.percentage" class="h-2 mb-2" />
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>{{ storageInfo.percentage }}% utilisé</span>
                        <span>{{ (storageInfo.total - storageInfo.used).toFixed(1) }} GB disponible</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Barre d'outils -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-slate-600 dark:text-slate-400">
                        <span>{{ fileStats.total }} élément(s)</span>
                        <span v-if="fileStats.folders > 0">• {{ fileStats.folders }} dossier(s)</span>
                        <span v-if="fileStats.files > 0">• {{ fileStats.files }} fichier(s)</span>
                    </div>
                    <div v-if="selectedItems.length > 0" class="flex items-center space-x-2">
                        <Badge variant="secondary">{{ selectedItems.length }} sélectionné(s)</Badge>
                        <Button @click="deleteSelected" variant="outline" size="sm" class="text-red-600">
                            <Trash2 class="w-4 h-4 mr-1" />
                            Supprimer
                        </Button>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <Button @click="selectAll" variant="outline" size="sm">
                        {{ selectedItems.length === filteredFiles.length ? 'Désélectionner' : 'Tout sélectionner' }}
                    </Button>
                    <div class="flex border border-slate-300 dark:border-slate-600 rounded-lg">
                        <Button 
                            @click="viewMode = 'grid'" 
                            :variant="viewMode === 'grid' ? 'default' : 'ghost'"
                            size="sm"
                            class="rounded-r-none"
                        >
                            <Grid class="w-4 h-4" />
                        </Button>
                        <Button 
                            @click="viewMode = 'list'" 
                            :variant="viewMode === 'list' ? 'default' : 'ghost'"
                            size="sm"
                            class="rounded-l-none"
                        >
                            <List class="w-4 h-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Bouton retour si dans un dossier -->
            <div v-if="currentPath" class="flex items-center">
                <Button @click="goBack" variant="outline" size="sm">
                    <ArrowUp class="w-4 h-4 mr-2" />
                    Retour
                </Button>
            </div>

            <!-- Affichage grille -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <Card 
                    v-for="file in filteredFiles" 
                    :key="file.id"
                    :class="[
                        'cursor-pointer transition-all duration-200 hover:shadow-lg hover:scale-105',
                        selectedItems.includes(file.id) ? 'ring-2 ring-blue-500' : ''
                    ]"
                    @click="handleFileClick(file)"
                    @contextmenu.prevent="toggleSelection(file.id)"
                >
                    <CardContent class="p-4 text-center">
                        <!-- Preview amélioré -->
                        <div class="relative w-16 h-16 mx-auto mb-3 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                            <!-- Image preview -->
                            <img 
                                v-if="file.preview" 
                                :src="file.preview" 
                                :alt="file.name" 
                                class="w-full h-full object-cover rounded-lg"
                                @error="file.preview = null"
                            />
                            <!-- Icône pour les autres types -->
                            <component 
                                v-else 
                                :is="getFileIcon(file)" 
                                :class="`w-8 h-8 ${getFileColor(file)}`" 
                            />
                            
                            <!-- Badge type de fichier -->
                            <div 
                                v-if="file.type === 'file' && file.extension" 
                                class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs px-1 py-0.5 rounded-md font-medium uppercase"
                            >
                                {{ file.extension }}
                            </div>
                        </div>
                        
                        <!-- Informations du fichier -->
                        <h3 class="font-medium text-sm truncate mb-1" :title="file.name">
                            {{ file.name }}
                        </h3>
                        
                        <!-- Taille ou contenu du dossier -->
                        <p class="text-xs text-slate-500 mb-1">
                            {{ getItemPreview(file) }}
                        </p>
                        
                        <!-- Type MIME pour les fichiers -->
                        <div v-if="file.type === 'file'" class="mb-1">
                            <Badge 
                                variant="outline" 
                                class="text-xs"
                                :class="getFileTypeColor(file.mimeType)"
                            >
                                {{ getFileTypeLabel(file.mimeType) }}
                            </Badge>
                        </div>
                        
                        <!-- Date -->
                        <p class="text-xs text-slate-400">
                            {{ formatDate(file.updatedAt) }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Affichage liste -->
            <div v-if="viewMode === 'list'" class="space-y-2">
                <Card 
                    v-for="file in filteredFiles" 
                    :key="file.id"
                    :class="[
                        'cursor-pointer transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800',
                        selectedItems.includes(file.id) ? 'ring-2 ring-blue-500' : ''
                    ]"
                    @click="handleFileClick(file)"
                    @contextmenu.prevent="toggleSelection(file.id)"
                >
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                <img v-if="file.preview" :src="file.preview" :alt="file.name" class="w-10 h-10 object-cover rounded-lg" />
                                <component v-else :is="getFileIcon(file)" :class="`w-5 h-5 ${getFileColor(file)}`" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-sm truncate">{{ file.name }}</h3>
                                <p class="text-xs text-slate-500">{{ getItemPreview(file) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-400">{{ formatDate(file.updatedAt) }}</p>
                                <Badge v-if="file.type === 'file'" variant="outline" class="text-xs mt-1">
                                    {{ file.mimeType?.split('/')[0] || 'fichier' }}
                                </Badge>
                            </div>
                            <Button variant="ghost" size="sm" @click.stop="">
                                <MoreVertical class="w-4 h-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Message vide -->
            <div v-if="filteredFiles.length === 0" class="text-center py-12">
                <Folder class="w-16 h-16 mx-auto text-slate-300 mb-4" />
                <h3 class="text-lg font-medium text-slate-600 dark:text-slate-400 mb-2">
                    {{ searchQuery ? 'Aucun résultat' : 'Dossier vide' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-500 mb-4">
                    {{ searchQuery ? 'Essayez avec d\'autres mots-clés' : 'Commencez par télécharger des fichiers ou créer des dossiers' }}
                </p>
                <div class="flex justify-center space-x-3">
                    <Button v-if="!searchQuery" as-child>
                        <Link :href="route('upload')">
                            <Upload class="w-4 h-4 mr-2" />
                            Télécharger des fichiers
                        </Link>
                    </Button>
                    <Button v-if="!searchQuery" @click="isCreateFolderOpen = true" variant="outline">
                        <FolderPlus class="w-4 h-4 mr-2" />
                        Créer un dossier
                    </Button>
                </div>
            </div>

            <!-- Modal d'aperçu d'image -->
            <Dialog v-model:open="isImagePreviewOpen">
                <DialogContent class="max-w-4xl max-h-[90vh] p-0">
                    <DialogHeader class="p-6 pb-0">
                        <DialogTitle class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <Image class="w-6 h-6 text-blue-600" />
                                <div>
                                    <h3 class="font-semibold">{{ previewImage?.name }}</h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ formatFileSize(previewImage?.size) }} • {{ getFileTypeLabel(previewImage?.mimeType) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    @click="downloadFile(previewImage!)"
                                    v-if="previewImage"
                                >
                                    <Download class="w-4 h-4 mr-2" />
                                    Télécharger
                                </Button>
                                <Button 
                                    variant="ghost" 
                                    size="sm"
                                    @click="closeImagePreview"
                                >
                                    ×
                                </Button>
                            </div>
                        </DialogTitle>
                    </DialogHeader>
                    <div class="p-6 pt-4">
                        <div class="flex justify-center items-center bg-slate-50 dark:bg-slate-900 rounded-lg overflow-hidden">
                            <img 
                                v-if="previewImage?.url" 
                                :src="previewImage.url" 
                                :alt="previewImage.name"
                                class="max-w-full max-h-[60vh] object-contain"
                                @error="closeImagePreview"
                            />
                        </div>
                        <div class="mt-4 text-center text-sm text-slate-600 dark:text-slate-400">
                            <p>Double-cliquez sur une image pour l'agrandir • Clic simple pour sélectionner</p>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template> 