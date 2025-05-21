<script setup lang="ts">
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { cn } from '@/lib/utils';
import { SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: true,
});

const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user as User);

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => user.value.avatar && user.value.avatar !== '');
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar && user.avatar" :src="`/storage/${user.avatar}`" :alt="user.name" />
        <!-- Custom avatar fallback -->
        <div v-show="!user.avatar" :class="cn('bg-muted flex size-full items-center justify-center rounded-full')">
            {{ getInitials(user.name) }}
        </div>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="props.showEmail" class="text-muted-foreground truncate text-xs">{{ user.email }}</span>
    </div>
</template>
