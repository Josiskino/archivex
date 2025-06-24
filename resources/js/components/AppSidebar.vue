<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Folder, Shield, Upload } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

// Récupérer les données utilisateur depuis Inertia
const page = usePage();
const auth = computed(() => page.props.auth);

// Fonction pour vérifier les permissions (à adapter selon votre système)
const hasPermission = (permission: string) => {
    // Pour l'instant, simulation basée sur l'email admin
    // À remplacer par votre logique de permissions réelle
    const user = auth.value?.user;
    if (!user) return false;
    
    // Admin a tous les droits
    if (user.email?.includes('admin')) return true;
    
    // Gestionnaire a certains droits
    if (user.email?.includes('manager')) {
        return ['files.read', 'files.write', 'files.delete', 'users.read'].includes(permission);
    }
    
    // Utilisateur normal a droits basiques
    return ['files.read', 'files.write'].includes(permission);
};

// Navigation principale avec permissions
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        }
    ];

    // Mes fichiers - accessible à tous les utilisateurs authentifiés
    items.push({
        title: 'Mes fichiers',
        href: '/files',
        icon: Folder,
    });

    // Upload - accessible à tous les utilisateurs avec permission d'écriture
    if (hasPermission('files.write')) {
        items.push({
            title: 'Télécharger',
            href: '/upload',
            icon: Upload,
        });
    }

    // Gestion des droits - uniquement pour admin et gestionnaires
    if (hasPermission('users.read')) {
        items.push({
            title: 'Gestion des droits',
            href: '/permissions',
            icon: Shield,
        });
    }

    return items;
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
