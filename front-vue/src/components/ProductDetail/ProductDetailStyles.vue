<template>
  <div aria-hidden="true" style="display:none"></div>
</template>

<script setup>
</script>

<style>
  .pagina-ecommerce {
    font-family: 'Arial', sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  }
  .banner-promocional {
    background-color: #780116;
    color: white;
    text-align: center;
    padding: 8px 10px;
    margin-top:90px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .boton-ordenar {
    background: none;
    border: none;
    color: white;
    text-decoration: underline;
    cursor: pointer;
    margin-left: 5px;
    font-weight: bold;
  }
  .promo-porcentaje {
    background: none;
    color: #ffffff; 
    font-size: 1rem;
    padding: 0 2px;
    border-radius: 0;
    box-shadow: none;
    font-weight: 800;
  }
  @media (max-width: 600px) {
    .promo-porcentaje { font-size: 1rem; }
    .boton-ordenar { margin-left: 0; }
    .banner-promocional { margin-top: 72px; padding: 10px 8px; }
  }
  .navegacion-principal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
  }
  .logo-tienda {
    font-weight: bold;
    font-size: 1.5rem;
    color: #780116;
  }
  .enlaces-navegacion a {
    margin-left: 20px;
    text-decoration: none;
    color: #333;
    transition: color 0.2s;
  }
  .enlaces-navegacion a:hover {
    color: #780116;
  }
  .migas-pan {
    padding: 15px 0;
    color: #666;
  }
  .contenedor-producto {
    display: flex;
    gap: 50px;
    margin: 30px 0;
  }
  .galeria-producto {
    flex: 1;
  }
  .imagen-principal img {
    width: 100%;
    height: auto;
    border-radius: 8px;
  }
  .detalles-producto {
    flex: 1;
  }
  .detalles-producto h1 {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #333;
  }
  .precio {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 30px;
    color: #780116;
  }
  .seleccion-color, .seleccion-talla, .seleccion-cantidad {
    margin-bottom: 25px;
  }
  .opciones-color {
    display: flex;
    gap: 15px;
    margin-top: 10px;
  }
  .opcion-color {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s;
  }
  .opcion-color:hover {
    transform: scale(1.1);
  }
  .opcion-color.activo {
    border-color: #780116;
  }
  .opcion-color.negro {
    background-color: #000;
  }
  .opcion-color.blanco {
    background-color: #fff;
    border: 1px solid #eee;
  }
  .opcion-color.gris {
    background-color: #808080;
  }
  .opciones-talla {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }
  .opcion-talla {
    padding: 8px 15px;
    background: white;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s;
  }
  .opcion-talla:hover {
    border-color: #780116;
  }
  .opcion-talla.activo {
    background: #780116;
    color: white;
    border-color: #780116;
  }
  .control-cantidad {
    display: flex;
    align-items: center;
    margin-top: 10px;
  }
  .boton-cantidad {
    padding: 5px 15px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s;
  }
  .boton-cantidad:hover {
    background: #780116;
    color: white;
    border-color: #780116;
  }
  .cantidad {
    padding: 0 15px;
  }
  .boton-agregar-carrito {
    background: #780116;
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 20px;
    width: 100%;
    border-radius: 4px;
    transition: background-color 0.2s;
  }
  .boton-agregar-carrito:hover {
    background-color: #5c0114;
  }
  .info-envio {
    margin-top: 10px;
    font-size: 0.9rem;
    color: #666;
  }
  .descripcion-producto {
    margin: 50px 0;
    padding-top: 30px;
    border-top: 1px solid #eee;
  }
  .descripcion-producto h2 {
    margin-bottom: 20px;
    color: #333;
  }
  .descripcion-texto {
    margin-bottom: 18px;
    line-height: 1.6;
    color: #555;
  }
  .detalles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
  }
  .detalle-item {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 12px 14px;
  }
  .detalle-label {
    font-size: 12px;
    color: #777;
    letter-spacing: .3px;
  }
  .detalle-value {
    font-size: 15px;
    color: #2b2b2b;
    margin-top: 2px;
  }
  .precio { color: #780116; font-weight: 600; }
  .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    border: 1px solid transparent;
  }
  .badge--ok { background: #f0fff5; color: #137333; border-color: #ccebd7; }
  .badge--out { background: #fff5f5; color: #a31d1d; border-color: #f1cdcd; }
  @media (max-width: 768px) {
    .contenedor-producto {
      flex-direction: column;
      gap: 30px;
    }
  }
</style>
