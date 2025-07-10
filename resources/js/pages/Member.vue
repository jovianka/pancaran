<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { onMounted, onBeforeUnmount } from 'vue'


const selectedMember = ref<Member | null>(null);
const showEditModal = ref(false);

const roles = ['admin', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'coordinator', 'peserta', 'anggota']; // bisa diganti ambil dari props kalau dinamis
const selectedRole = ref('');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activity / Members',
        href: '/members',
    },
];

const popupOpenId = ref<number | null>(null);

function togglePopup(id: number) {
  popupOpenId.value = popupOpenId.value === id ? null : id;
}

interface MemberRole {
  id: number;
  name: string;
}

interface MemberUserMajor {
  name: string;
}

interface MemberUser {
  id: number;
  name: string;
  nim: string;
  major?: MemberUserMajor | null;
  avatar?: string | null;
}

interface Member {           // ⇠ satu baris = satu record EventUser
  id: number;                // id baris event_user
  user: MemberUser | null;   // relasi user
  role?: MemberRole | null;  // relasi event_role
}

const props = defineProps<{
  members: {
    data: Member[];
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
  };
}>();

const showingStart = computed(() => props.members.from ?? 0);
const showingEnd = computed(() => props.members.to ?? 0);
const totalRows = computed(() => props.members.total);

function goPrev() {
  const prev = props.members.links.find(l => l.label.toLowerCase().includes('previous') && l.url);
  if (prev?.url) router.visit(prev.url);
}

function goNext() {
  const next = props.members.links.find(l => l.label.toLowerCase().includes('next') && l.url);
  if (next?.url) router.visit(next.url);
}

function editRole(member: Member) {
  selectedMember.value = member;
  selectedRole.value = member.role?.name ?? '';
  showEditModal.value = true;
}

function removeUser(member: Member) {
  const userName = member.user?.name ?? 'user';
  if (confirm(`Yakin ingin menghapus ${userName} dari event ini?`)) {
    router.delete(`/event-user/${member.id}`, {
      preserveScroll: true,
      onSuccess: () => alert('User berhasil dihapus'),
    });
  }
}

function updateRole() {
  if (!selectedMember.value) return;

  router.put(`/event-user/${selectedMember.value.id}`, {
    role: selectedRole.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      alert('Role berhasil diperbarui');
      showEditModal.value = false;
    }
  });
}

function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement;
  if (!target.closest('.popup-control')) {
    popupOpenId.value = null;
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));

</script>

<template>
  <Head title="Members" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Back Button -->
<div class="px-4 sm:px-8 mt-4">
  <!-- Link href sesuaikan dengan tujuan anda aslinya setelah back -->
  <Link href="/dashboard" class="inline-flex items-center text-sm font-medium text-indigo-700 hover:text-indigo-900 dark:text-indigo-400">
    <!-- Icon panah -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
         stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 19l-7-7 7-7" />
    </svg>
    Back
  </Link>
</div>

    <!-- Header -->
    <div class="px-4 sm:px-8 pt-6">
      <div class="flex items-center justify-between bg-indigo-900 text-white px-4 py-2 rounded-t-lg">
        <span class="font-semibold">{{ totalRows }} Members</span>
        <div class="flex items-center space-x-4 text-sm">
          <span>{{ showingStart }} – {{ showingEnd }} dari {{ totalRows }}</span>
          <button @click="goPrev" :disabled="showingStart === 1"
                  class="disabled:opacity-30 p-1 rounded hover:bg-indigo-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button @click="goNext" :disabled="showingEnd === totalRows"
                  class="disabled:opacity-30 p-1 rounded hover:bg-indigo-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="px-4 sm:px-8">
      <div class="bg-white border rounded-b-lg shadow-sm overflow-x-auto dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
              <th class="px-6 py-3 text-left font-medium text-gray-500">NIM</th>
              <th class="px-6 py-3 text-left font-medium text-gray-500">Major</th>
              <th class="px-6 py-3 text-left font-medium text-gray-500">Position</th>
              <th class="px-6 py-3 text-left font-medium text-gray-500">Actions</th>

            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="member in props.members.data" :key="member.id" class="hover:bg-gray-500">
              <td class="px-6 py-4 flex items-center gap-3">
                <div v-if="member.user?.avatar" class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 shrink-0">
              <img :src="member.user.avatar" alt="Avatar" class="w-full h-full object-cover">
            </div>
                {{ member.user?.name ?? '–' }}
              </td>
              <td class="px-6 py-4">{{ member.user?.nim ?? '–' }}</td>
              <td class="px-6 py-4">{{ member.user?.major?.name ?? '–' }}</td>
              <td class="px-6 py-4">{{ member.role?.name ?? '–' }}</td>


              <td class="px-6 py-4 relative">
                <!-- Tombol Trigger -->
                <button
                  @click="togglePopup(member.id)"
                  class="text-gray-600 hover:text-gray-900 dark:text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 popup-control"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="5" cy="12" r="2" />
                    <circle cx="12" cy="12" r="2" />
                    <circle cx="19" cy="12" r="2" />
                  </svg>
                </button>

                <!-- Mini Popup -->
                <div
                  v-if="popupOpenId === member.id"
                  class="absolute z-50 top-10 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-md w-40 popup-control"
                >
                  <button
                    @click="editRole(member)"
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 popup-control"
                  >
                    ✏️ Edit Role
                  </button>
                  <button
                    @click="removeUser(member)"
                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 popup-control"
                  >
                    🗑️ Remove
                  </button>
                </div>
              </td>



          </tr>

          </tbody>
        </table>
      </div>

      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-96 shadow-lg dark:bg-gray-900">
    <h2 class="text-lg font-semibold mb-4">Edit Role</h2>
    <select v-model="selectedRole" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-black dark:text-white px-3 py-2 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      <option disabled value="" class="text-gray-400 dark:text-gray-500" >Pilih Role</option>
      <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
    </select>
    <div class="flex justify-end gap-2">
      <button @click="showEditModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-black dark:text-white rounded hover:bg-gray-400 dark:hover:bg-gray-600 text-sm">
        Cancel
      </button>
      <button @click="updateRole" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
        Update
      </button>
    </div>
  </div>
</div>


      <!-- Pagination -->
      <div class="flex justify-end mt-4 gap-2">
        <Link
          v-for="link in props.members.links"
          :key="link.label"
          :href="link.url ?? ''"
          v-html="link.label"
          class="px-3 py-1 rounded-md text-sm"
          :class="{
            'bg-indigo-600 text-white': link.active,
            'hover:bg-gray-500': !link.active && link.url,
            'text-gray-400 cursor-default': !link.url
          }"
        />
      </div>
    </div>
  </AppLayout>
</template>
