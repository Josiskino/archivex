<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Archive, Shield, FolderOpen, Search, Cloud, Upload, Download, Users, Zap, Lock, FileText } from 'lucide-vue-next';
</script>

<template>
    <Head title="Accueil - ArchiveX" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-sm border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white">
                            <Archive class="w-6 h-6" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">ArchiveX</h1>
                            <p class="text-xs text-slate-500">Solution d'archivage moderne</p>
                        </div>
                    </div>

                    <!-- Auth buttons -->
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('login')" 
                            class="text-slate-600 hover:text-blue-600 transition-colors font-medium"
                        >
                            Se connecter
                        </Link>
                        <Link 
                            :href="route('register')" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            Commencer
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 lg:py-32 pt-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium mb-6 animate-fade-in-up opacity-0" style="animation-delay: 0.2s;">
                        <Zap class="w-4 h-4 mr-2 animate-pulse" />
                        Nouvelle génération d'archivage
                    </div>
                    
                    <h1 class="text-4xl lg:text-6xl font-bold text-slate-900 leading-tight mb-6 animate-fade-in-up opacity-0" style="animation-delay: 0.4s;">
                        Vos fichiers,
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent relative">
                            <span class="animate-gradient-x bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 bg-clip-text text-transparent bg-[length:200%_100%]">
                                organisés
                            </span>
                        </span>
                        pour l'éternité
                    </h1>
                    
                    <p class="text-xl text-slate-600 mb-8 leading-relaxed max-w-3xl mx-auto animate-fade-in-up opacity-0" style="animation-delay: 0.6s;">
                        ArchiveX révolutionne la gestion de vos documents avec un chiffrement AES-256, 
                        une organisation intelligente et une recherche instantanée.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12 animate-fade-in-up opacity-0" style="animation-delay: 0.8s;">
                        <Link 
                            :href="route('register')" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold hover:scale-105 hover:shadow-lg"
                        >
                            <Upload class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" />
                            Commencer gratuitement
                        </Link>
                        <Link 
                            :href="route('login')" 
                            class="inline-flex items-center justify-center px-6 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-all duration-300 font-semibold hover:scale-105 hover:shadow-lg"
                        >
                            <FileText class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" />
                            Se connecter
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 max-w-md mx-auto animate-fade-in-up opacity-0" style="animation-delay: 1s;">
                        <div class="text-center group cursor-pointer">
                            <div class="text-2xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors duration-300 animate-count-up">1M+</div>
                            <div class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors duration-300">Fichiers archivés</div>
                        </div>
                        <div class="text-center group cursor-pointer">
                            <div class="text-2xl font-bold text-slate-900 group-hover:text-green-600 transition-colors duration-300 animate-count-up" style="animation-delay: 0.2s;">99.9%</div>
                            <div class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors duration-300">Disponibilité</div>
                        </div>
                        <div class="text-center group cursor-pointer">
                            <div class="text-2xl font-bold text-slate-900 group-hover:text-purple-600 transition-colors duration-300 animate-count-up" style="animation-delay: 0.4s;">256-bit</div>
                            <div class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors duration-300">Chiffrement</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">
                        Pourquoi choisir ArchiveX ?
                    </h2>
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                        Une solution complète pour sécuriser, organiser et retrouver vos fichiers en un clic
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-blue-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-300">
                            <Shield class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Sécurité maximale</h3>
                            <p class="text-slate-600 leading-relaxed">Chiffrement AES-256 de bout en bout pour protéger vos données les plus sensibles</p>
                        </div>
                        <div class="flex items-center text-blue-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-green-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-colors duration-300">
                            <FolderOpen class="w-6 h-6 text-green-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Organisation intelligente</h3>
                            <p class="text-slate-600 leading-relaxed">Tri automatique par type, date et contenu grâce à notre IA avancée</p>
                        </div>
                        <div class="flex items-center text-green-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-yellow-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-600 transition-colors duration-300">
                            <Search class="w-6 h-6 text-yellow-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Recherche instantanée</h3>
                            <p class="text-slate-600 leading-relaxed">Trouvez n'importe quel fichier en quelques secondes grâce à notre moteur de recherche</p>
                        </div>
                        <div class="flex items-center text-yellow-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-purple-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-600 transition-colors duration-300">
                            <Cloud class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Synchronisation cloud</h3>
                            <p class="text-slate-600 leading-relaxed">Accédez à vos fichiers depuis n'importe où, sur tous vos appareils</p>
                        </div>
                        <div class="flex items-center text-purple-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-red-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-600 transition-colors duration-300">
                            <Users class="w-6 h-6 text-red-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Collaboration</h3>
                            <p class="text-slate-600 leading-relaxed">Partagez vos archives en toute sécurité avec votre équipe</p>
                        </div>
                        <div class="flex items-center text-red-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="group relative bg-white rounded-2xl p-8 shadow-lg border border-slate-200 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-6 right-6 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 transition-colors duration-300">
                            <Download class="w-6 h-6 text-indigo-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Sauvegarde automatique</h3>
                            <p class="text-slate-600 leading-relaxed">Vos fichiers sont automatiquement sauvegardés et versionnés</p>
                        </div>
                        <div class="flex items-center text-indigo-600 font-medium text-sm">
                            <span>En savoir plus</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                    Prêt à révolutionner votre archivage ?
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    Rejoignez des milliers d'utilisateurs qui font confiance à ArchiveX pour sécuriser leurs données
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link 
                        :href="route('register')" 
                        class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors font-semibold text-lg"
                    >
                        <Archive class="w-5 h-5 mr-2" />
                        Commencer maintenant
                    </Link>
                    <Link 
                        :href="route('login')" 
                        class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 transition-colors font-semibold text-lg"
                    >
                        Se connecter
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white">
                            <Archive class="w-5 h-5" />
                        </div>
                        <span class="text-xl font-bold">ArchiveX</span>
                    </div>
                    <p class="text-slate-400 mb-8">
                        La solution d'archivage moderne pour sécuriser et organiser vos fichiers.
                    </p>
                    <div class="border-t border-slate-800 pt-8 text-slate-400">
                        <p>&copy; 2024 ArchiveX. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes gradient-x {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes count-up {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-gradient-x {
    animation: gradient-x 3s ease-in-out infinite;
}

.animate-count-up {
    animation: count-up 0.6s ease-out forwards;
}
</style> 