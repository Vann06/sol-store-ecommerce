<template>
    <aside class="sidebar">
      <nav>
        <RouterLink to="/account/orders" class="nav-link" active-class="active">
          <i class="fa fa-shopping-cart"></i> Orders
        </RouterLink>
        <RouterLink to="/account/address" class="nav-link" active-class="active">
          <i class="fa fa-truck"></i> Address
        </RouterLink>
        <RouterLink to="/account/password" class="nav-link" active-class="active">
          <i class="fa fa-key"></i> Password
        </RouterLink>
        <RouterLink to="/account/details" class="nav-link" active-class="active">
          <i class="fa fa-user"></i> Account Detail
        </RouterLink>
        <RouterLink to="/account/logout" class="nav-link">
          <i class="fa fa-sign-out-alt"></i> Logout
        </RouterLink>
      </nav>
    </aside>
  </template>
  
  <style scoped>
  .sidebar {
    width: 250px;
    padding: 20px;
  }
  
  .nav-link {
    display: block;
    padding: 10px;
    color: #333;
    font-weight: 500;
    margin-bottom: 8px;
  }
  
  .nav-link i {
    margin-right: 8px;
  }
  
  .nav-link.active {
    background-color: #f3f3f3;
    color: #8b0000;
    border-radius: 8px;
  }
  </style>
  