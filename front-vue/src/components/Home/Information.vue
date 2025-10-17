<template>
  <section class="info-section" ref="sectionEl" :class="{ 'is-mounted': isMounted }">
      <div class="info-item">
    <div class="icon-circle">
      <img src="/img/truck.svg" alt="Envíos" class="icon-svg" />
        </div>
  <h3>Envíos a toda Guatemala</h3>
  <p>Recibe tus pedidos en la puerta de tu hogar.</p>
      </div>
  
      <div class="info-item">
    <div class="icon-circle">
      <img src="/img/medal.svg" alt="Satisfacción garantizada" class="icon-svg" />
        </div>
        <h3>Satisfacción garantizada</h3>
        <p>
          Compra con confianza: si no te encanta, te devolvemos tu dinero.
        </p>
      </div>
  
      <div class="info-item">
    <div class="icon-circle">
      <img src="/img/shield.svg" alt="Pago seguro" class="icon-svg" />
        </div>
        <h3>Pago seguro</h3>
        <p>
          Tu seguridad es nuestra prioridad. Tus pagos están protegidos con nosotros.
        </p>
      </div>
    </section>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'
  const isMounted = ref(false)
  const sectionEl = ref(null)
  onMounted(() => {
    // simple mount reveal; could be extended to IntersectionObserver if needed
    requestAnimationFrame(() => { isMounted.value = true })
  })
  </script>

  <style scoped>
  .icon-svg{
    filter: invert(16%) sepia(97%) saturate(744%) hue-rotate(353deg) brightness(88%) contrast(106%);
    width: 45%;
  }
  .info-section {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    padding: 40px 20px;
    background-color: var(--surface, #fff);
    text-align: center;
    gap: 20px;
  }
  
  .info-item {
    flex: 1 1 250px;
    max-width: 300px;
    padding: 10px;
    transition: transform 220ms ease, box-shadow 220ms ease;
    will-change: transform;
    opacity: 0; transform: translateY(12px);
  }
  .info-section.is-mounted .info-item { opacity: 1; transform: translateY(0); }
  .info-section.is-mounted .info-item:nth-child(1) { animation: fadeUp 520ms ease both; animation-delay: 40ms; }
  .info-section.is-mounted .info-item:nth-child(2) { animation: fadeUp 520ms ease both; animation-delay: 140ms; }
  .info-section.is-mounted .info-item:nth-child(3) { animation: fadeUp 520ms ease both; animation-delay: 240ms; }
  @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

  .info-item:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(31,41,55,0.08); }
  /* Golden glow on hover around the card edge */
  .info-item:hover {
    outline: 2px solid transparent; /* reserve space */
    background: linear-gradient(var(--surface), var(--surface)) padding-box,
                linear-gradient(135deg, rgba(255,221,75,0.8), rgba(255,192,16,0.65)) border-box;
    border-radius: 14px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(113,18,18,0.10), 0 4px 12px rgba(255,221,75,0.20);
  }
  .icon-circle { transition: transform 220ms ease, background-color 220ms ease; }
  .info-item:hover .icon-circle { transform: scale(1.05); background-color: rgba(255, 221, 75, 0.45); }
  
  .icon-circle {
    width: 40px;
    height: 40px;
    margin: 0 auto 10px;
    background-color: rgba(255, 221, 75, 0.35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .icon-circle i {
    color: #8b0000;
    font-size: 20px;
  }
  
  h3 {
    font-size: 1.1rem;
    color: var(--brand-strong);
    font-weight: bold;
    margin-bottom: 8px;
  }
  
  p {
    font-size: 0.9rem;
    color: #555;
  }

  /* Respect reduced motion */
  @media (prefers-reduced-motion: reduce) {
    .info-item, .info-section.is-mounted .info-item, .info-item:hover, .icon-circle { transition: none !important; animation: none !important; transform: none !important; opacity: 1 !important; }
  }
  </style>
  