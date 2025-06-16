<!-- TabPage.vue -->
<template>
    <Tabs default-value="account" class="w-[400px]">
      <TabsList class="grid w-full grid-cols-2">
        <TabsTrigger value="account">Account</TabsTrigger>
        <TabsTrigger value="password">Password</TabsTrigger>
      </TabsList>
      <TabsContent value="account">Account Settings Content</TabsContent>
      <TabsContent value="password">Change Password Content</TabsContent>
    </Tabs>
  </template>
  
  <script setup>
  import { Tabs, TabsList, TabsTrigger, TabsContent } from 'shadcn-vue'
  </script>
  