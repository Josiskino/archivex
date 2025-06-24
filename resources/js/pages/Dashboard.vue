<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Archive, 
    FileText, 
    Upload, 
    Download, 
    Search, 
    Clock, 
    Shield, 
    HardDrive,
    TrendingUp,
    Plus,
    Eye,
    MoreVertical,
    Folder,
    Image,
    FileVideo,
    Music,
    Zap
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Données simulées pour le dashboard
const stats = {
    totalFiles: 1247,
    totalSize: '15.6 GB',
    totalFolders: 89,
    recentUploads: 23,
    storageUsed: 65, // pourcentage
    securityLevel: 'AES-256'
};

const recentFiles = [
    {
        id: 1,
        name: 'Contrat_Client_2024.pdf',
        type: 'pdf',
        size: '2.4 MB',
        uploadedAt: '2024-01-15 14:30',
        folder: 'Documents Légaux'
    },
    {
        id: 2,
        name: 'Présentation_Projet.pptx',
        type: 'presentation',
        size: '8.1 MB',
        uploadedAt: '2024-01-15 11:22',
        folder: 'Présentations'
    },
    {
        id: 3,
        name: 'Photo_Équipe_2024.jpg',
        type: 'image',
        size: '3.2 MB',
        uploadedAt: '2024-01-14 16:45',
        folder: 'Photos'
    },
    {
        id: 4,
        name: 'Rapport_Mensuel_Janvier.docx',
        type: 'document',
        size: '1.8 MB',
        uploadedAt: '2024-01-14 09:15',
        folder: 'Rapports'
    },
    {
        id: 5,
        name: 'Formation_Video.mp4',
        type: 'video',
        size: '45.2 MB',
        uploadedAt: '2024-01-13 13:20',
        folder: 'Formations'
    }
];

const quickActions = [
    {
        title: 'Télécharger des fichiers',
        description: 'Ajouter de nouveaux documents',
        icon: Upload,
        color: 'blue',
        action: 'upload',
        href: '/upload'
    },
    {
        title: 'Créer un dossier',
        description: 'Organiser vos documents',
        icon: Folder,
        color: 'green',
        action: 'create-folder'
    },
    {
        title: 'Recherche avancée',
        description: 'Trouver rapidement vos fichiers',
        icon: Search,
        color: 'purple',
        action: 'search'
    },
    {
        title: 'Sauvegarde',
        description: 'Exporter vos archives',
        icon: Download,
        color: 'orange',
        action: 'backup'
    }
];

const getFileIcon = (type: string) => {
    switch (type) {
        case 'pdf':
        case 'document':
            return FileText;
        case 'image':
            return Image;
        case 'video':
            return FileVideo;
        case 'audio':
            return Music;
        case 'presentation':
            return FileText;
        default:
            return FileText;
    }
};

const getFileColor = (type: string) => {
    switch (type) {
        case 'pdf':
            return 'text-red-600';
        case 'document':
            return 'text-blue-600';
        case 'image':
            return 'text-green-600';
        case 'video':
            return 'text-purple-600';
        case 'audio':
            return 'text-yellow-600';
        case 'presentation':
            return 'text-orange-600';
        default:
            return 'text-gray-600';
    }
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- En-tête avec salutation -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Bonjour ! 👋
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400">
                        Voici un aperçu de votre activité d'archivage
                    </p>
                </div>
                <Button as-child class="bg-blue-600 hover:bg-blue-700">
                    <Link :href="route('upload')">
                        <Plus class="w-4 h-4 mr-2" />
                        Nouveau fichier
                    </Link>
                </Button>
            </div>

            <!-- Statistiques principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Documents</CardTitle>
                        <Archive class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalFiles.toLocaleString() }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            <span class="text-green-600">+{{ stats.recentUploads }}</span> ce mois
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Espace Utilisé</CardTitle>
                        <HardDrive class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalSize }}</div>
                        <Progress :value="stats.storageUsed" class="mt-2" />
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                            {{ stats.storageUsed }}% de 25 GB utilisé
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Dossiers</CardTitle>
                        <Folder class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalFolders }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Bien organisé
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Sécurité</CardTitle>
                        <Shield class="h-4 w-4 text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.securityLevel }}</div>
                        <div class="flex items-center mt-1">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                Chiffrement actif
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Actions rapides -->
                <div class="lg:col-span-1">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center">
                                <Zap class="w-5 h-5 mr-2 text-blue-600" />
                                Actions rapides
                            </CardTitle>
                            <CardDescription>
                                Effectuez des tâches courantes rapidement
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <Link v-for="action in quickActions" :key="action.action" 
                                 :href="action.href || '#'"
                                 class="flex items-center space-x-3 p-3 rounded-lg border hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                <div :class="`w-10 h-10 rounded-lg flex items-center justify-center bg-${action.color}-100 dark:bg-${action.color}-900/20`">
                                    <component :is="action.icon" :class="`w-5 h-5 text-${action.color}-600`" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-sm">{{ action.title }}</h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-400">{{ action.description }}</p>
                                </div>
                            </Link>
                        </CardContent>
                    </Card>
                </div>

                <!-- Fichiers récents -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle class="flex items-center">
                                        <Clock class="w-5 h-5 mr-2 text-green-600" />
                                        Fichiers récents
                                    </CardTitle>
                                    <CardDescription>
                                        Vos derniers documents archivés
                                    </CardDescription>
                                </div>
                                <Button variant="outline" size="sm">
                                    Voir tout
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="file in recentFiles" :key="file.id" 
                                     class="flex items-center space-x-4 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <component :is="getFileIcon(file.type)" :class="`w-5 h-5 ${getFileColor(file.type)}`" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-sm truncate">{{ file.name }}</h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <Badge variant="secondary" class="text-xs">{{ file.folder }}</Badge>
                                            <span class="text-xs text-slate-500">{{ file.size }}</span>
                                            <span class="text-xs text-slate-500">{{ formatDate(file.uploadedAt) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <Button variant="ghost" size="sm">
                                            <Eye class="w-4 h-4" />
                                        </Button>
                                        <Button variant="ghost" size="sm">
                                            <MoreVertical class="w-4 h-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Activité et tendances -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center">
                            <TrendingUp class="w-5 h-5 mr-2 text-purple-600" />
                            Activité de la semaine
                        </CardTitle>
                        <CardDescription>
                            Tendance de vos archives
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Lundi</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-3/4 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">15 fichiers</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Mardi</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-2/3 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">12 fichiers</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Mercredi</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-full h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">20 fichiers</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Jeudi</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-1/2 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">10 fichiers</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Vendredi</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-4/5 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">16 fichiers</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center">
                            <Archive class="w-5 h-5 mr-2 text-green-600" />
                            Types de fichiers
                        </CardTitle>
                        <CardDescription>
                            Répartition de vos documents
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <FileText class="w-4 h-4 text-blue-600" />
                                    <span class="text-sm">Documents</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-3/5 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">60%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <Image class="w-4 h-4 text-green-600" />
                                    <span class="text-sm">Images</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-1/4 h-2 bg-green-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">25%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <FileVideo class="w-4 h-4 text-purple-600" />
                                    <span class="text-sm">Vidéos</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-1/8 h-2 bg-purple-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">10%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <Music class="w-4 h-4 text-yellow-600" />
                                    <span class="text-sm">Audio</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
                                        <div class="w-1/20 h-2 bg-yellow-600 rounded-full"></div>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">5%</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
