<template>
    <div class="account-layout">
      <MiniHeader />
      <div class="account-content">
        <Sidebar />
        <div class="account-view">
          <router-view />
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Sidebar from '@/components/Account/Sidebar.vue'
  import MiniHeader from '@/components/Account/MiniHeader.vue'
  </script>
  
  <style scoped>
  .account-layout {
    width: 100vw;
    min-height: 100%;
    box-sizing: border-box;
    background-color: white;
  
    margin-top: 100px; 
  }
  
  .account-content {
    display: flex;
    width: 100%;
    padding: 40px 80px;
    gap: 40px;
    box-sizing: border-box;
  }
  
  .account-view {
    flex: 1;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
  }
  </style>
  