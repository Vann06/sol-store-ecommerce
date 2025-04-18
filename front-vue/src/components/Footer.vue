<template>
    <section class="footer">
      <div class="footer-content">
        <div class="brand">
          <img src="/img/logo.png" alt="Logo" class="logo" />
          <p class="tagline" >Tienda de réplicas de tus series y películas favoritas</p>
          <div class="social">
            <i class="fa fa-facebook"></i>
            <i class="fa fa-instagram"></i>
            <i class="fa fa-youtube"></i>
          </div>
        </div>
        <div class="footer-columns">
          <div>
            <h4 class="white-text">SOPORTE</h4>
            <ul>
              <li><a href="#">FAQ</a></li>
              <li><a href="#">Privacidad</a></li>
            </ul>
          </div>
          <div>
            <h4 class="white-text">COMPAÑIA</h4>
            <ul>
              <li><a href="#">Sobre Nosotros</a></li>
              <li><a href="#">Contactos</a></li>
            </ul>
          </div>
          <div>
            <h4 class="white-text">TIENDA</h4>
            <ul>
              <li><a href="#">Mi Cuenta</a></li>
              <li><a href="#">Checkout</a></li>
              <li><a href="#">Carrito</a></li>
            </ul>
          </div>
          <div>
            <h4 class="white-text">METODOS DE PAGO</h4>
            <img src="/img/payments.png" alt="Payments" class="payments" />
          </div>
        </div>
      </div>
      <div class="footer-bottom">© 2025 DevCut. Todos los derechos reservados.</div>
    </section>
  </template>
  
  <script setup>
  </script>
  
  <style scoped>
.footer {
  background-image: url('/img/footer-bg.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  color: white;
  padding: 40px 20px 20px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

  .footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    width: 100%;
    max-width: 1200px;
  }
  
  .brand {
    flex: 1 1 200px;
    text-align: left;
  }
  
  .logo {
    width: 80px;
    margin-bottom: 10px;
  }
  
  .tagline {
    margin: 10px 0;
    color: white;
  }
  
  .social i {
    margin: 0 8px;
    font-size: 20px;
    color: white;
  }
  
  .footer-columns {
    flex: 3 1 600px;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
  }
  
  .footer-columns h4 {
    margin-bottom: 10px;
    color: white;
  }
  
  .footer-columns ul {
    list-style: none;
    padding: 0;
  }
  
  .footer-columns li {
    margin-bottom: 5px;
  }
  
  .footer-columns a {
    color: white;
    text-decoration: none;
  }
  
  .footer-columns a:hover {
    text-decoration: underline;
  }
  
  .payments {
    width: 120px;
    margin-top: 10px;
  }
  
  .footer-bottom {
    margin-top: 30px;
    font-size: 14px;
    border-top: 1px solid rgb(255, 255, 255);
    padding-top: 15px;
    width: 100%;
    color: white;
  }
  </style>
  