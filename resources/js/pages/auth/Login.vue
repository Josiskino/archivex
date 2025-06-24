<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Archive, Shield, FolderOpen } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion - ArchiveX" />

    <AuthSplitLayout 
        title="Connexion à ArchiveX" 
        description="Accédez à vos archives sécurisées"
    >
        <div v-if="status" class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center">
                <div class="text-sm text-green-800 font-medium">
                    {{ status }}
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-5">
                <div>
                    <Label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Adresse email
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="votre@email.com"
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
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="block w-full rounded-lg border-slate-300 !bg-white !text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 placeholder:text-slate-400 dark:!bg-white dark:!text-slate-900"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                    <div v-if="canResetPassword" class="mt-2 text-right">
                        <TextLink 
                            :href="route('password.request')" 
                            class="text-sm text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200 dark:text-blue-400 dark:hover:text-blue-300" 
                            :tabindex="5"
                        >
                            Mot de passe oublié ?
                        </TextLink>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <Checkbox 
                        id="remember" 
                        v-model="form.remember" 
                        :tabindex="3"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                    />
                    <Label for="remember" class="text-sm text-slate-700 select-none">
                        Se souvenir de moi
                    </Label>
                </div>

                <div class="pt-2">
                    <Button 
                        type="submit" 
                        class="group relative flex w-full justify-center items-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200" 
                        :tabindex="4" 
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <Archive v-else class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Connexion en cours...' : 'Se connecter' }}
                    </Button>
                </div>
            </div>

            <div class="text-center pt-4 border-t border-slate-200">
                <span class="text-sm text-slate-600">
                    Pas encore de compte ?
                </span>
                <TextLink 
                    :href="route('register')" 
                    class="ml-1 text-sm font-semibold text-blue-600 hover:text-blue-500 transition-colors duration-200 dark:text-blue-400 dark:hover:text-blue-300" 
                    :tabindex="5"
                >
                    Créer un compte
                </TextLink>
            </div>
            
            <div class="flex items-center justify-center space-x-4 pt-4">
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <Shield class="h-3 w-3" />
                    <span>Sécurisé</span>
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <Archive class="h-3 w-3" />
                    <span>Chiffré</span>
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <FolderOpen class="h-3 w-3" />
                    <span>Organisé</span>
                </div>
            </div>
        </form>
    </AuthSplitLayout>
</template>
