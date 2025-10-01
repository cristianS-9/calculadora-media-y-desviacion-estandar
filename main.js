document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".card");
  const isTouchDevice =
    "ontouchstart" in window || navigator.maxTouchPoints > 0;

  cards.forEach((card) => {
    const content = card.querySelector(".card-content");
    const rotationFactor =
      parseFloat(card.getAttribute("data-rotation-factor")) || 2;

    if (!isTouchDevice) {
      card.addEventListener("mousemove", (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateY = (rotationFactor * (x - centerX)) / centerX;
        const rotateX = (-rotationFactor * (y - centerY)) / centerY;

        content.style.transform = `
                          rotateX(${rotateX}deg)
                          rotateY(${rotateY}deg)
                        `;

        card.style.setProperty("--x", `${(x / rect.width) * 100}%`);
        card.style.setProperty("--y", `${(y / rect.height) * 100}%`);
      });

      card.addEventListener("mouseleave", () => {
        content.style.transform = "rotateX(0) rotateY(0)";

        content.style.transition = "transform 0.5s ease";
        setTimeout(() => {
          content.style.transition = "";
        }, 500);
      });
    }

    const randomDelay = Math.random() * 2;
    card.style.animation = `cardFloat 4s infinite alternate ease-in-out ${randomDelay}s`;
  });

  const style = document.createElement("style");
  style.textContent = `
                @keyframes cardFloat {
                  0% {
                    transform: translateY(0);
                  }
                  100% {
                    transform: translateY(-5px);
                  }
                }

                @media (min-width: 768px) {
                  @keyframes cardFloat {
                    0% {
                      transform: translateY(0);
                    }
                    100% {
                      transform: translateY(-8px);
                    }
                  }
                }

                @media (min-width: 1024px) {
                  @keyframes cardFloat {
                    0% {
                      transform: translateY(0);
                    }
                    100% {
                      transform: translateY(-10px);
                    }
                  }
                }
            `;
  document.head.appendChild(style);

  const buttons = document.querySelectorAll(".card-button");
  buttons.forEach((button) => {
    button.addEventListener("click", (e) => {
      // ripple
      const ripple = document.createElement("span");
      ripple.classList.add("ripple");
      button.appendChild(ripple);

      const rect = button.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height) * 2;

      ripple.style.width = ripple.style.height = `${size}px`;
      ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
      ripple.style.top = `${e.clientY - rect.top - size / 2}px`;

      ripple.classList.add("active");

      setTimeout(() => {
        ripple.remove();
      }, 500);
    });
  });

  const cuadro = document.getElementById("lista-numeros");
  if (cuadro) {
    cuadro.scrollTop = cuadro.scrollHeight;
  }
  /* ===== Toggle de tema claro/oscuro ===== */
  const THEME_KEY = 'app-theme-mode'; // 'light' | 'dark'
  const toggleBtn = document.getElementById('theme-toggle');
  const prefersMQ = window.matchMedia('(prefers-color-scheme: dark)');

  function applyTheme(mode, persist = true) {
    // Añadimos clase para animación de transición
    document.body.classList.add('theme-transition');
    document.body.setAttribute('data-theme', mode);
    // Remover clase después de la duración de la animación CSS
    window.clearTimeout(applyTheme._tId);
    applyTheme._tId = window.setTimeout(() => {
      document.body.classList.remove('theme-transition');
    }, 650); // ligeramente mayor que la animación (600ms)
    if (toggleBtn) {
      const sun = toggleBtn.querySelector('.icon-sun');
      const moon = toggleBtn.querySelector('.icon-moon');
      const label = toggleBtn.querySelector('.label-text');
      if (mode === 'dark') {
        toggleBtn.setAttribute('aria-label', 'Cambiar a modo claro');
        toggleBtn.dataset.mode = 'dark';
        if (sun) sun.style.display = 'none';
        if (moon) moon.style.display = 'block';
        if (label) label.textContent = 'Dark';
      } else {
        toggleBtn.setAttribute('aria-label', 'Cambiar a modo oscuro');
        toggleBtn.dataset.mode = 'light';
        if (sun) sun.style.display = 'block';
        if (moon) moon.style.display = 'none';
        if (label) label.textContent = 'Light';
      }
    }
    if (persist) localStorage.setItem(THEME_KEY, mode);
  }

  // Determinar tema inicial: preferencia guardada > preferencia sistema > light
  const stored = localStorage.getItem(THEME_KEY);
  const initial = stored || (prefersMQ.matches ? 'dark' : 'light');
  applyTheme(initial, false);

  // Escuchar cambios de sistema si el usuario no cambió manualmente
  let userForced = !!stored; // si existe guardado, el usuario ya eligió
  prefersMQ.addEventListener('change', (e) => {
    if (!userForced) {
      applyTheme(e.matches ? 'dark' : 'light', false);
    }
  });

  // Evento de click en botón toggle
  toggleBtn?.addEventListener('click', () => {
    const current = document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    userForced = true;
    applyTheme(next, true);
  });
});
