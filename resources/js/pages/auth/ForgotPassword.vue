<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, ArrowLeft } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Mot de passe oublié - ArchiveX" />

    <AuthSplitLayout 
        title="Mot de passe oublié ?" 
        description="Nous vous enverrons un lien pour réinitialiser votre mot de passe"
    >
        <div v-if="status" class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center">
                <Mail class="h-5 w-5 text-green-600 mr-3" />
                <div class="text-sm text-green-800 font-medium">
                    {{ status }}
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <Label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse email
                </Label>
                <Input 
                    id="email" 
                    type="email" 
                    name="email" 
                    required
                    autofocus
                    autocomplete="email" 
                    v-model="form.email" 
                    placeholder="votre@email.com"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3"
                />
                <InputError :message="form.errors.email" class="mt-2" />
                <p class="mt-2 text-xs text-gray-500">
                    Entrez l'adresse email associée à votre compte ArchiveX
                </p>
            </div>

            <div class="pt-2">
                <Button 
                    type="submit"
                    class="group relative flex w-full justify-center items-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    <Mail v-else class="mr-2 h-4 w-4" />
                    {{ form.processing ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
                </Button>
            </div>

            <div class="text-center pt-4 border-t border-gray-200">
                <TextLink 
                    :href="route('login')" 
                    class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200"
                >
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Retour à la connexion
                </TextLink>
            </div>
        </form>

        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">
                Problème d'accès ?
            </h3>
            <p class="text-xs text-blue-700 leading-relaxed">
                Si vous ne recevez pas l'email de réinitialisation, vérifiez votre dossier spam 
                ou contactez notre support technique pour obtenir de l'aide.
            </p>
        </div>
    </AuthSplitLayout>
</template>
