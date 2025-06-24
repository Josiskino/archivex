<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Shield,
    Users,
    UserPlus,
    User,
    Crown,
    Settings,
    Edit,
    Trash2,
    Eye,
    Lock,
    Unlock,
    Search,
    Filter,
    MoreVertical,
    Check,
    X,
    Plus,
    Mail,
    Calendar,
    UserCheck,
    UserX,
    Group
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { ref, computed, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Gestion des droits',
        href: '/permissions',
    },
];

interface Role {
    id: string;
    name: string;
    description: string;
    color: string;
    permissions: string[];
    usersCount: number;
}

interface UserPermission {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    role: Role;
    groups: Group[];
    lastLogin: string;
    status: 'active' | 'inactive' | 'suspended';
    createdAt: string;
}

interface Group {
    id: string;
    name: string;
    description: string;
    permissions: string[];
    usersCount: number;
    color: string;
}

interface Permission {
    id: string;
    name: string;
    description: string;
    category: string;
}

// État de l'application
const activeTab = ref<'users' | 'groups' | 'roles'>('users');
const searchQuery = ref('');
const selectedFilter = ref('all');
const isCreateUserOpen = ref(false);
const isCreateGroupOpen = ref(false);
const isCreateRoleOpen = ref(false);
const selectedItems = ref<string[]>([]);

// Données simulées
const permissions = ref<Permission[]>([
    { id: '1', name: 'files.read', description: 'Lire les fichiers', category: 'Fichiers' },
    { id: '2', name: 'files.write', description: 'Écrire des fichiers', category: 'Fichiers' },
    { id: '3', name: 'files.delete', description: 'Supprimer des fichiers', category: 'Fichiers' },
    { id: '4', name: 'users.read', description: 'Voir les utilisateurs', category: 'Utilisateurs' },
    { id: '5', name: 'users.write', description: 'Modifier les utilisateurs', category: 'Utilisateurs' },
    { id: '6', name: 'users.delete', description: 'Supprimer les utilisateurs', category: 'Utilisateurs' },
    { id: '7', name: 'admin.access', description: 'Accès administration', category: 'Administration' },
]);

const roles = ref<Role[]>([
    {
        id: '1',
        name: 'Administrateur',
        description: 'Accès complet au système',
        color: 'red',
        permissions: ['1', '2', '3', '4', '5', '6', '7'],
        usersCount: 2
    },
    {
        id: '2',
        name: 'Gestionnaire',
        description: 'Gestion des fichiers et utilisateurs',
        color: 'blue',
        permissions: ['1', '2', '3', '4', '5'],
        usersCount: 5
    },
    {
        id: '3',
        name: 'Utilisateur',
        description: 'Accès de base aux fichiers',
        color: 'green',
        permissions: ['1', '2'],
        usersCount: 25
    },
    {
        id: '4',
        name: 'Lecture seule',
        description: 'Lecture des fichiers uniquement',
        color: 'gray',
        permissions: ['1'],
        usersCount: 8
    }
]);

const groups = ref<Group[]>([
    {
        id: '1',
        name: 'Équipe Technique',
        description: 'Développeurs et techniciens',
        permissions: ['1', '2', '3'],
        usersCount: 8,
        color: 'purple'
    },
    {
        id: '2',
        name: 'Équipe Commerciale',
        description: 'Commerciaux et marketing',
        permissions: ['1', '2'],
        usersCount: 12,
        color: 'orange'
    },
    {
        id: '3',
        name: 'Direction',
        description: 'Direction et management',
        permissions: ['1', '2', '3', '4', '5'],
        usersCount: 4,
        color: 'indigo'
    }
]);

const users = ref<UserPermission[]>([
    {
        id: '1',
        name: 'Marie Dubois',
        email: 'marie.dubois@archivex.fr',
        avatar: '/avatars/marie.jpg',
        role: roles.value[0],
        groups: [groups.value[2]],
        lastLogin: '2024-01-15T14:30:00Z',
        status: 'active',
        createdAt: '2023-06-15T10:00:00Z'
    },
    {
        id: '2',
        name: 'Jean Martin',
        email: 'jean.martin@archivex.fr',
        role: roles.value[1],
        groups: [groups.value[0]],
        lastLogin: '2024-01-15T09:15:00Z',
        status: 'active',
        createdAt: '2023-08-20T14:00:00Z'
    },
    {
        id: '3',
        name: 'Sophie Laurent',
        email: 'sophie.laurent@archivex.fr',
        role: roles.value[2],
        groups: [groups.value[1]],
        lastLogin: '2024-01-14T16:45:00Z',
        status: 'active',
        createdAt: '2023-09-10T11:30:00Z'
    },
    {
        id: '4',
        name: 'Pierre Durand',
        email: 'pierre.durand@archivex.fr',
        role: roles.value[2],
        groups: [groups.value[1]],
        lastLogin: '2024-01-12T13:20:00Z',
        status: 'inactive',
        createdAt: '2023-11-05T09:00:00Z'
    },
    {
        id: '5',
        name: 'Emma Rousseau',
        email: 'emma.rousseau@archivex.fr',
        role: roles.value[3],
        groups: [],
        lastLogin: '2024-01-10T08:30:00Z',
        status: 'suspended',
        createdAt: '2023-12-01T15:45:00Z'
    }
]);

// Computed
const filteredUsers = computed(() => {
    let result = users.value;
    
    // Filtrer par statut
    if (selectedFilter.value !== 'all') {
        result = result.filter(user => user.status === selectedFilter.value);
    }
    
    // Filtrer par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(user => 
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            user.role.name.toLowerCase().includes(query)
        );
    }
    
    return result;
});

const userStats = computed(() => {
    const total = users.value.length;
    const active = users.value.filter(u => u.status === 'active').length;
    const inactive = users.value.filter(u => u.status === 'inactive').length;
    const suspended = users.value.filter(u => u.status === 'suspended').length;
    
    return { total, active, inactive, suspended };
});

const roleStats = computed(() => {
    return roles.value.map(role => ({
        ...role,
        percentage: Math.round((role.usersCount / users.value.length) * 100)
    }));
});

// Méthodes
const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'inactive': return 'bg-gray-100 text-gray-800';
        case 'suspended': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'active': return UserCheck;
        case 'inactive': return User;
        case 'suspended': return UserX;
        default: return User;
    }
};

const getRoleColor = (role: Role) => {
    return `bg-${role.color}-100 text-${role.color}-800`;
};

const getGroupColor = (group: Group) => {
    return `bg-${group.color}-100 text-${group.color}-800`;
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const formatLastLogin = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now.getTime() - date.getTime());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'Hier';
    if (diffDays < 7) return `Il y a ${diffDays} jours`;
    return formatDate(dateString);
};

const toggleUserStatus = (userId: string) => {
    const user = users.value.find(u => u.id === userId);
    if (user) {
        user.status = user.status === 'active' ? 'inactive' : 'active';
    }
};

const deleteUser = (userId: string) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        const index = users.value.findIndex(u => u.id === userId);
        if (index > -1) {
            users.value.splice(index, 1);
        }
    }
};

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const getPermissionName = (permissionId: string) => {
    const permission = permissions.value.find(p => p.id === permissionId);
    return permission ? permission.name : '';
};
</script>

<template>
    <Head title="Gestion des droits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- En-tête -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Gestion des droits
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400">
                        Gérez les utilisateurs, groupes et permissions de votre organisation
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <Button @click="isCreateUserOpen = true" class="bg-blue-600 hover:bg-blue-700">
                        <UserPlus class="w-4 h-4 mr-2" />
                        Nouvel utilisateur
                    </Button>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Utilisateurs</CardTitle>
                        <Users class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ userStats.total }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            {{ userStats.active }} actifs
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Rôles</CardTitle>
                        <Crown class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ roles.length }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            {{ permissions.length }} permissions
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Groupes</CardTitle>
                        <Group class="h-4 w-4 text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ groups.length }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Organisation par équipes
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Sécurité</CardTitle>
                        <Shield class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ userStats.suspended }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Utilisateurs suspendus
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Onglets -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'users'"
                        :class="[
                            activeTab === 'users' 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center'
                        ]"
                    >
                        <Users class="w-4 h-4 mr-2" />
                        Utilisateurs ({{ userStats.total }})
                    </button>
                    <button
                        @click="activeTab = 'groups'"
                        :class="[
                            activeTab === 'groups' 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center'
                        ]"
                    >
                        <Group class="w-4 h-4 mr-2" />
                        Groupes ({{ groups.length }})
                    </button>
                    <button
                        @click="activeTab = 'roles'"
                        :class="[
                            activeTab === 'roles' 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center'
                        ]"
                    >
                        <Crown class="w-4 h-4 mr-2" />
                        Rôles ({{ roles.length }})
                    </button>
                </nav>
            </div>

            <!-- Filtres et recherche -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Rechercher..." 
                            class="pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-sm"
                        />
                    </div>
                    <select 
                        v-if="activeTab === 'users'"
                        v-model="selectedFilter"
                        class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-sm"
                    >
                        <option value="all">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                        <option value="suspended">Suspendus</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <Button 
                        v-if="activeTab === 'groups'" 
                        @click="isCreateGroupOpen = true" 
                        variant="outline"
                    >
                        <Plus class="w-4 h-4 mr-2" />
                        Nouveau groupe
                    </Button>
                    <Button 
                        v-if="activeTab === 'roles'" 
                        @click="isCreateRoleOpen = true" 
                        variant="outline"
                    >
                        <Plus class="w-4 h-4 mr-2" />
                        Nouveau rôle
                    </Button>
                </div>
            </div>

            <!-- Contenu des onglets -->
            
            <!-- Onglet Utilisateurs -->
            <div v-if="activeTab === 'users'">
                <Card>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="text-left py-3 px-6 font-medium text-slate-600 dark:text-slate-300">Utilisateur</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600 dark:text-slate-300">Rôle</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600 dark:text-slate-300">Groupes</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600 dark:text-slate-300">Statut</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600 dark:text-slate-300">Dernière connexion</th>
                                        <th class="text-center py-3 px-4 font-medium text-slate-600 dark:text-slate-300">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                    <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                    <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-10 h-10 rounded-full object-cover" />
                                                    <span v-else class="text-sm font-medium">{{ getInitials(user.name) }}</span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-slate-900 dark:text-slate-100 truncate">{{ user.name }}</p>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center truncate">
                                                        <Mail class="w-3 h-3 mr-1 flex-shrink-0" />
                                                        {{ user.email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <Badge :class="getRoleColor(user.role)">
                                                {{ user.role.name }}
                                            </Badge>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex flex-wrap gap-1 max-w-32">
                                                <Badge 
                                                    v-for="group in user.groups.slice(0, 2)" 
                                                    :key="group.id" 
                                                    :class="getGroupColor(group)" 
                                                    variant="outline" 
                                                    class="text-xs"
                                                >
                                                    {{ group.name }}
                                                </Badge>
                                                <Badge v-if="user.groups.length > 2" variant="outline" class="text-xs">
                                                    +{{ user.groups.length - 2 }}
                                                </Badge>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <Badge :class="getStatusColor(user.status)">
                                                <component :is="getStatusIcon(user.status)" class="w-3 h-3 mr-1" />
                                                {{ user.status === 'active' ? 'Actif' : user.status === 'inactive' ? 'Inactif' : 'Suspendu' }}
                                            </Badge>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-sm">
                                                <p class="text-slate-600 dark:text-slate-300">{{ formatLastLogin(user.lastLogin) }}</p>
                                                <p class="text-xs text-slate-400">Créé le {{ formatDate(user.createdAt) }}</p>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center space-x-1">
                                                <Button 
                                                    @click="toggleUserStatus(user.id)" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    class="h-8 w-8 p-0"
                                                    :title="user.status === 'active' ? 'Désactiver' : 'Activer'"
                                                >
                                                    <component :is="user.status === 'active' ? Lock : Unlock" class="w-4 h-4" />
                                                </Button>
                                                <Button 
                                                    variant="ghost" 
                                                    size="sm"
                                                    class="h-8 w-8 p-0"
                                                    title="Modifier"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </Button>
                                                <Button 
                                                    @click="deleteUser(user.id)" 
                                                    variant="ghost" 
                                                    size="sm" 
                                                    class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950"
                                                    title="Supprimer"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Onglet Groupes -->
            <div v-if="activeTab === 'groups'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="group in groups" :key="group.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center">
                                <div :class="`w-3 h-3 rounded-full bg-${group.color}-500 mr-2`"></div>
                                {{ group.name }}
                            </CardTitle>
                            <Button variant="ghost" size="sm">
                                <MoreVertical class="w-4 h-4" />
                            </Button>
                        </div>
                        <CardDescription>{{ group.description }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Utilisateurs</span>
                                <Badge variant="secondary">{{ group.usersCount }}</Badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Permissions</span>
                                <Badge variant="secondary">{{ group.permissions.length }}</Badge>
                            </div>
                            <div class="pt-2">
                                <p class="text-xs text-slate-500 mb-2">Permissions:</p>
                                <div class="flex flex-wrap gap-1">
                                    <Badge v-for="permId in group.permissions.slice(0, 3)" :key="permId" variant="outline" class="text-xs">
                                        {{ getPermissionName(permId) }}
                                    </Badge>
                                    <Badge v-if="group.permissions.length > 3" variant="outline" class="text-xs">
                                        +{{ group.permissions.length - 3 }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Onglet Rôles -->
            <div v-if="activeTab === 'roles'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card v-for="role in roleStats" :key="role.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center">
                                <Crown :class="`w-5 h-5 mr-2 text-${role.color}-600`" />
                                {{ role.name }}
                            </CardTitle>
                            <Button variant="ghost" size="sm">
                                <MoreVertical class="w-4 h-4" />
                            </Button>
                        </div>
                        <CardDescription>{{ role.description }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Utilisateurs assignés</span>
                                <div class="flex items-center space-x-2">
                                    <Badge :class="getRoleColor(role)">{{ role.usersCount }}</Badge>
                                    <span class="text-xs text-slate-500">{{ role.percentage }}%</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-2">Permissions ({{ role.permissions.length }}):</p>
                                <div class="flex flex-wrap gap-1">
                                    <Badge v-for="permId in role.permissions.slice(0, 4)" :key="permId" variant="outline" class="text-xs">
                                        {{ getPermissionName(permId) }}
                                    </Badge>
                                    <Badge v-if="role.permissions.length > 4" variant="outline" class="text-xs">
                                        +{{ role.permissions.length - 4 }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Dialogs de création (simplifié pour l'exemple) -->
            <Dialog v-model:open="isCreateUserOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Créer un nouvel utilisateur</DialogTitle>
                        <DialogDescription>
                            Ajoutez un nouvel utilisateur à votre organisation
                        </DialogDescription>
                    </DialogHeader>
                    <div class="space-y-4 pt-4">
                        <input type="text" placeholder="Nom complet" class="w-full px-3 py-2 border rounded-lg" />
                        <input type="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg" />
                        <select class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Sélectionner un rôle</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                        </select>
                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="isCreateUserOpen = false">Annuler</Button>
                            <Button>Créer</Button>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template> 