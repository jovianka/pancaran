<template>
    <div class="flex h-screen p-4 text-black">
      
  
      <!-- Main Content -->
      <div class="flex-1 bg-gray-100 p-6">
        <div class="mb-4">
          <button class="text-sm text-gray-600 mb-2">← Back</button>
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold">69 Members</h2>
              <div class="text-sm text-gray-500">1 - 10 dari 69</div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">NIM</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Major</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Sub Committee</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Position</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="member in members" :key="member.nim">
                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                      <div class="w-8 h-8 bg-gray-300 rounded-full mr-3"></div>
                      {{ member.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ member.nim }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ member.major }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ member.subCommittee }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ member.position }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const members = [
    { name: 'Ahmad Kasim', nim: '2308561090', major: 'Informatika', subCommittee: 'Inti', position: 'Ketua' },
    { name: 'Ahmad Dani', nim: '2308561011', major: 'Farmasi', subCommittee: 'Inti', position: 'Wakil Ketua' },
    { name: 'Ahmad Sandi', nim: '2308561049', major: 'Biologi', subCommittee: 'Inti', position: 'Sekretaris' },
    { name: 'Ahmad Jordi', nim: '2308561049', major: 'Kimia', subCommittee: 'Inti', position: 'Bendahara' },
    { name: 'Ahmad Tung Tung', nim: '2308561049', major: 'Biologi', subCommittee: 'Acara', position: 'Ketua Divisi' },
    { name: 'Ahmad Kasarung', nim: '2308561049', major: 'Fisika', subCommittee: 'Acara', position: 'Anggota' },
    { name: 'Ahmad Vilia', nim: '2308561049', major: 'Biologi', subCommittee: 'Acara', position: 'Anggota' },
    { name: 'Ahmad Reno', nim: '2308561049', major: 'Biologi', subCommittee: 'SPTK', position: 'Ketua Divisi' },
    { name: 'Ahmad Sini', nim: '2308561049', major: 'Matematika', subCommittee: 'SPTK', position: 'Wakil Ketua Divisi' },
  ]
  </script>
  
  <style scoped>
  /* Custom styles if needed */
  </style>