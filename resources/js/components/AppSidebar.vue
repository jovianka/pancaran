<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { AwardIcon, BookOpen, CalendarClock, CompassIcon, FileBadge, Folder, MailIcon, User as UserIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage() as { props: { auth?: { user?: { type: string } } } };
const userType = computed(() => page.props.auth?.user?.type);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Explore',
            href: '/explore',
            icon: CompassIcon,
        },
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: UserIcon,
        },
        {
            title: 'Database SKP',
            href: '/database-skp',
            icon: AwardIcon,
        },
    ];

    if (userType.value === 'student') {
        items.push({
            title: 'Certificate',
            href: '/certificate',
            icon: FileBadge,
        });
        items.push({
            title: 'Invitations',
            href: '/invitations',
            icon: MailIcon,
        });
        items.push({
            title: 'Activity',
            href: '/activity',
            icon: CalendarClock,
        });
    } else {
        items.push({
            title: 'Activity',
            href: '/activity',
            icon: CalendarClock,
            children: [
                { title: 'Your Activity', href: '/activity' },
                { title: 'Create an Event', href: '/create-event' },
            ],
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/jovianka/pancaran',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
