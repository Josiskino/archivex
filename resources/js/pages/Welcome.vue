<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Archive, Shield, FolderOpen, Search, Cloud, ArrowRight, Users, Gauge } from 'lucide-vue-next';

defineProps<{
    appName?: string;
    appDescription?: string;
    features?: Array<{
        title: string;
        description: string;
        icon: string;
    }>;
}>();

const defaultFeatures = [
    {
        title: 'Sécurité AES-256',
        description: 'Chiffrement militaire pour protéger vos données',
        icon: 'shield'
    },
    {
        title: 'Organisation intelligente',
        description: 'Tri automatique par type, date et métadonnées',
        icon: 'folder'
    },
    {
        title: 'Recherche avancée',
        description: 'Trouvez n\'importe quel fichier en quelques secondes',
        icon: 'search'
    },
    {
        title: 'Synchronisation cloud',
        description: 'Accédez à vos archives depuis n\'importe où',
        icon: 'cloud'
    }
];

const getIcon = (iconName: string) => {
    const icons = {
        shield: Shield,
        folder: FolderOpen,
        search: Search,
        cloud: Cloud
    };
    return icons[iconName] || Archive;
};
</script>

<template>
    <Head :title="appName || 'ArchiveX - Solution d\'archivage moderne'" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
        <!-- Navigation -->
        <nav class="relative z-10 bg-white/80 backdrop-blur-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-3">
                        <Archive class="h-8 w-8 text-blue-600" />
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ appName || 'ArchiveX' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                    Dashboard
                            <ArrowRight class="ml-2 h-4 w-4" />
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                                class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors duration-200"
                    >
                                Connexion
                    </Link>
                    <Link
                        :href="route('register')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                    >
                                Inscription
                                <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </template>
                    </div>
                </div>
            </div>
            </nav>

        <!-- Hero Section -->
        <main class="relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="text-center">
                    <h1 class="text-5xl font-bold text-gray-900 mb-6">
                        {{ appDescription || 'Votre solution complète d\'archivage et de gestion de fichiers sécurisée.' }}
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Organisez, sécurisez et retrouvez tous vos fichiers grâce à notre plateforme d'archivage intelligente. 
                        Chiffrement militaire, recherche avancée et synchronisation cloud inclus.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors duration-200 shadow-lg hover:shadow-xl"
                        >
                            Commencer gratuitement
                            <ArrowRight class="ml-2 h-5 w-5" />
                        </Link>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-8 py-3 border border-gray-300 text-gray-700 rounded-lg text-lg font-semibold hover:bg-gray-50 transition-colors duration-200"
                        >
                            Déjà client ? Se connecter
                        </Link>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="mt-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div 
                        v-for="(feature, index) in (features || defaultFeatures)" 
                        :key="index"
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200"
                    >
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                            <component :is="getIcon(feature.icon)" class="h-6 w-6 text-blue-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600">{{ feature.description }}</p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="mt-20 bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                        <div>
                            <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
                                <Users class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">10,000+</div>
                            <div class="text-gray-600">Utilisateurs actifs</div>
                        </div>
                        <div>
                            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                                <Archive class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">1M+</div>
                            <div class="text-gray-600">Fichiers archivés</div>
                        </div>
                        <div>
                            <div class="flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mx-auto mb-4">
                                <Gauge class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">99.9%</div>
                            <div class="text-gray-600">Disponibilité</div>
                        </div>
                    </div>
                </div>
                </div>
            </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <Archive class="h-6 w-6 text-blue-400" />
                        <span class="text-xl font-bold">{{ appName || 'ArchiveX' }}</span>
                    </div>
                    <div class="text-gray-400">
                        © 2024 ArchiveX. Tous droits réservés.
                    </div>
                </div>
        </div>
        </footer>
    </div>
</template>
