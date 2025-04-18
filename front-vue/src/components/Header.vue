<template>
    <header class="header">
      <div class="header__container">
        <div class="header__logo">
          <a href="#">
            <img src="/img/logo.png" alt="Logo" />
          </a>
        </div>
  
        <nav class="header__nav">
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Categories <span class="dropdown-icon">▾</span></a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">FAQ</a></li>
          </ul>
        </nav>
  
        <div class="header__tools">
          <div class="search-box">
            <input type="text" placeholder="Search products" />
            <img src="/img/search-icon.svg" alt="Search" class="icon-img" />
          </div>
          <a href="#" class="icon-button">
            <img src="/img/cart-icon.svg" alt="Cart" class="icon-img" />
          </a>
          <a href="#" class="icon-button">
            <img src="/img/user-icon.svg" alt="User" class="icon-img" />
          </a>
        </div>
      </div>
    </header>
  </template>
  
  <style scoped>
  .header {
    background-color: #fff;
    border-top: 5px solid #8B0000;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.05);
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
  }
  
  .header__container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
  }
  
  .header__logo img {
    height: 55px;
  }
  
  .header__nav ul {
    list-style: none;
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    gap: 25px;
  }
  
  .header__nav ul li {
    padding: 10px 16px;
    border-radius: 6px;
    transition: background-color 0.3s;
    cursor: pointer;
  }
  
  .header__nav ul li:hover {
    background-color: #8B0000;
  }
  
  .header__nav ul li:hover a {
    color: white;
  }
  
  .header__nav a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    font-size: 15px;
  }
  
  .dropdown-icon {
    font-size: 10px;
    margin-left: 4px;
  }
  
  .header__tools {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #ddd;
    padding: 6px 12px;
    border-radius: 6px;
    background-color: #fff;
  }
  
  .search-box input {
    border: none;
    outline: none;
    font-size: 14px;
    width: 160px;
  }
  
  .icon-img {
    width: 20px;
    height: 20px;
  }
  
  @media (max-width: 768px) {
    .header__container {
      flex-direction: column;
      gap: 12px;
      align-items: flex-start;
    }
    .header__tools {
      width: 100%;
      justify-content: flex-start;
      gap: 12px;
    }
    .search-box input {
      width: 100%;
    }
  }
  
  body {
    padding-top: 90px;
    /* adjust depending on header height to prevent overlap */
  }
  </style>