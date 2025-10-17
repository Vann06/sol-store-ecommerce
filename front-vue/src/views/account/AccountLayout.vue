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
    min-height: 100vh;
    box-sizing: border-box;
    background: linear-gradient(135deg, #fdf8f0 0%, #f4ead6 50%, #f0e4d0 100%);
    margin-top: 80px; 
    padding-top: var(--header-height);
  }
  
  .account-content {
    display: flex;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px;
    gap: 30px;
    box-sizing: border-box;
    min-height: calc(100vh - 80px);
  }
  
  .account-view {
    flex: 1;
    background-color: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    min-height: fit-content;
    position: relative;
  }

  .account-view::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    pointer-events: none;
    background: linear-gradient(135deg, rgba(122,0,25,0.15), rgba(240,192,64,0.12));
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .account-content {
      padding: 20px;
      gap: 20px;
    }
    
    .account-view {
      padding: 30px;
    }
  }

  @media (max-width: 768px) {
    .account-content {
      flex-direction: column;
      padding: 15px;
      gap: 20px;
    }
    
    .account-view {
      padding: 20px;
    }
  }

  @media (max-width: 480px) {
    .account-layout {
      margin-top: 70px;
    }
    
    .account-content {
      padding: 10px;
    }
    
    .account-view {
      padding: 15px;
      border-radius: 12px;
    }
  }
  </style>
