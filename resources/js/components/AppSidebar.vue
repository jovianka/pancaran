<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, CalendarClock, CompassIcon, FileBadge, Folder, User as UserIcon } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage() as { props: { auth?: { user?: { type: string } } } }
const userType = computed(() => page.props.auth?.user?.type)

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
        title: 'Activity',
        href: '/activity',
        icon: CalendarClock,
        children: [
            { title: 'Your Activity', href: '/activity' },
            { title: 'Create an Event', href: '/event/create' },
        ],
    },
  ]

  if (userType.value === 'student') {
    items.push({
      title: 'Certificate',
      href: '/certificate',
      icon: FileBadge,
    })
  }

  return items
})


const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
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
