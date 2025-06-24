<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Archive, Shield, FolderOpen, Lock, Search, Cloud } from 'lucide-vue-next';

const page = usePage();
const name = page.props.name || 'ArchiveX';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative lg:grid lg:h-screen lg:grid-cols-2 min-h-screen flex flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:px-0">
        <!-- Sidebar avec thème d'archivage -->
        <div class="relative hidden h-full flex-col p-10 text-white lg:flex overflow-y-auto">
            <!-- Image de fond avec overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-slate-900/95 to-indigo-900/90"></div>
            <div 
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
                style="background-image: url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2134&q=80')"
            ></div>
            <!-- Texture subtile par-dessus -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width=%2760%27%20height=%2760%27%20viewBox=%270%200%2060%2060%27%20xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cg%20fill=%27none%27%20fill-rule=%27evenodd%27%3E%3Cg%20fill=%27%23ffffff%27%20fill-opacity=%270.03%27%3E%3Cpath%20d=%27M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%27/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20" />
            
            <!-- Logo et nom de l'application -->
            <div class="relative z-20 flex items-center text-xl font-bold mb-8">
                <Archive class="mr-3 size-8 text-blue-400 drop-shadow-lg" />
                <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ name }}
                </span>
            </div>
            
            <!-- Contenu principal -->
            <div class="relative z-20 mt-auto space-y-6">
                <!-- Fonctionnalités en grille -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3 rounded-lg bg-white/10 p-4 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-colors duration-300">
                        <Shield class="size-8 text-green-400 drop-shadow-lg" />
                        <div>
                            <p class="text-sm font-medium">Sécurisé</p>
                            <p class="text-xs text-slate-300">Chiffrement AES-256</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 rounded-lg bg-white/10 p-3 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-colors duration-300">
                        <FolderOpen class="size-8 text-blue-400 drop-shadow-lg" />
                        <div>
                            <p class="text-sm font-medium">Organisé</p>
                            <p class="text-xs text-slate-300">Tri intelligent</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 rounded-lg bg-white/10 p-3 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-colors duration-300">
                        <Search class="size-8 text-yellow-400 drop-shadow-lg" />
                        <div>
                            <p class="text-sm font-medium">Recherche</p>
                            <p class="text-xs text-slate-300">Instantanée</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 rounded-lg bg-white/10 p-3 backdrop-blur-sm border border-white/20 hover:bg-white/15 transition-colors duration-300">
                        <Cloud class="size-8 text-purple-400 drop-shadow-lg" />
                        <div>
                            <p class="text-sm font-medium">Cloud</p>
                            <p class="text-xs text-slate-300">Synchronisé</p>
                        </div>
                    </div>
                </div>
                
                <!-- Citation -->
                <blockquote class="space-y-2 border-l-4 border-blue-400 pl-3 bg-black/20 rounded-r-lg p-3 backdrop-blur-sm">
                    <p class="text-base italic font-light leading-relaxed">
                        "Vos fichiers, organisés et sécurisés pour l'éternité"
                    </p>
                    <footer class="text-xs text-slate-300 font-medium">
                        ArchiveX - Solution d'archivage moderne
                    </footer>
                </blockquote>

                <!-- Statistiques -->
                <div class="flex justify-center space-x-8 pt-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400">1M+</div>
                        <div class="text-xs text-slate-400">Fichiers archivés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400">99.9%</div>
                        <div class="text-xs text-slate-400">Disponibilité</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400">256-bit</div>
                        <div class="text-xs text-slate-400">Chiffrement</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenu du formulaire -->
        <div class="lg:p-8 bg-slate-50 h-full flex items-center justify-center overflow-y-auto">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px] py-8 my-auto">
                <!-- En-tête mobile -->
                <div class="flex flex-col space-y-2 text-center lg:hidden">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-blue-100 mb-4">
                        <Archive class="size-8 text-blue-600" />
                    </div>
                </div>
                
                <!-- Titre et description -->
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-slate-600" v-if="description">{{ description }}</p>
                </div>
                
                <!-- Contenu du slot -->
                <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-6">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
