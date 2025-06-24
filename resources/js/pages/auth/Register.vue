<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Archive, Shield, FolderOpen, UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Inscription - ArchiveX" />

    <AuthSplitLayout 
        title="Créer votre compte ArchiveX" 
        description="Commencez à organiser et sécuriser vos fichiers"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-5">
                <div>
                    <Label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nom complet
                    </Label>
                    <Input 
                        id="name" 
                        type="text" 
                        required 
                        autofocus 
                        :tabindex="1" 
                        autocomplete="name" 
                        v-model="form.name" 
                        placeholder="Jean Dupont"
                        class="block w-full rounded-lg border-slate-300 !bg-white !text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 placeholder:text-slate-400 dark:!bg-white dark:!text-slate-900"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <Label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Adresse email
                    </Label>
                    <Input 
                        id="email" 
                        type="email" 
                        required 
                        :tabindex="2" 
                        autocomplete="email" 
                        v-model="form.email" 
                        placeholder="jean@exemple.com"
                        class="block w-full rounded-lg border-slate-300 !bg-white !text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 placeholder:text-slate-400 dark:!bg-white dark:!text-slate-900"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                    <Label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Mot de passe
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="block w-full rounded-lg border-slate-300 !bg-white !text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 placeholder:text-slate-400 dark:!bg-white dark:!text-slate-900"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                    <p class="mt-1 text-xs text-slate-500">
                        Minimum 8 caractères avec chiffres et lettres
                    </p>
                </div>

                <div>
                    <Label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirmer le mot de passe
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="block w-full rounded-lg border-slate-300 !bg-white !text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 placeholder:text-slate-400 dark:!bg-white dark:!text-slate-900"
                    />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <div class="pt-2">
                    <Button 
                        type="submit" 
                        class="group relative flex w-full justify-center items-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200" 
                        tabindex="5" 
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <UserPlus v-else class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Création en cours...' : 'Créer mon compte' }}
                    </Button>
                </div>
            </div>

            <div class="text-center pt-4 border-t border-slate-200">
                <span class="text-sm text-slate-600">
                    Déjà un compte ?
                </span>
                <TextLink 
                    :href="route('login')" 
                    class="ml-1 text-sm font-semibold text-blue-600 hover:text-blue-500 transition-colors duration-200 dark:text-blue-400 dark:hover:text-blue-300" 
                    :tabindex="6"
                >
                    Se connecter
                </TextLink>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 space-y-3">
                <h3 class="text-sm font-semibold text-blue-900 flex items-center">
                    <Archive class="h-4 w-4 mr-2" />
                    Fonctionnalités ArchiveX
                </h3>
                <div class="grid grid-cols-1 gap-2 text-xs text-blue-700">
                    <div class="flex items-center space-x-2">
                        <Shield class="h-3 w-3 flex-shrink-0" />
                        <span>Chiffrement AES-256 de vos fichiers</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <FolderOpen class="h-3 w-3 flex-shrink-0" />
                        <span>Organisation automatique par type de fichier</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Archive class="h-3 w-3 flex-shrink-0" />
                        <span>Compression intelligente et recherche avancée</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs text-slate-500">
                    En créant un compte, vous acceptez nos 
                    <TextLink href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        conditions d'utilisation
                    </TextLink>
                    et notre 
                    <TextLink href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        politique de confidentialité
                    </TextLink>.
                </p>
            </div>
        </form>
    </AuthSplitLayout>
</template>
