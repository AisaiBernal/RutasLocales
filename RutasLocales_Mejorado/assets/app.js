// RutasLocales · interacciones con JavaScript
document.addEventListener('DOMContentLoaded', () => {

  // 1. Menú móvil
  const menuBtn = document.getElementById('menuBtn'), menu = document.getElementById('menu');
  menuBtn?.addEventListener('click', () => {
    const abierto = menu.classList.toggle('abierto');
    menuBtn.setAttribute('aria-expanded', abierto);
  });

  // 2. Mostrar / ocultar contraseña
  document.querySelectorAll('[data-toggle-pass]').forEach(btn => btn.addEventListener('click', () => {
    const input = btn.parentElement.querySelector('input');
    const oculto = input.type === 'password';
    input.type = oculto ? 'text' : 'password';
    btn.textContent = oculto ? 'ocultar' : 'mostrar';
  }));

  // 3. Filtro en vivo (tarjetas y tablas) mientras se escribe
  document.querySelectorAll('[data-live-filter]').forEach(input => {
    const items = () => document.querySelectorAll(input.dataset.liveFilter);
    input.addEventListener('input', () => {
      const t = input.value.trim().toLowerCase();
      let visibles = 0;
      items().forEach(el => {
        const texto = el.dataset.texto || el.textContent.toLowerCase();
        const ok = texto.includes(t);
        el.style.display = ok ? '' : 'none';
        if (ok) visibles++;
      });
      const r = document.getElementById('resumen');
      if (r) r.innerHTML = `<strong>${visibles}</strong> experiencia(s) disponible(s)`;
    });
  });

  // 4. Confirmación antes de acciones destructivas
  document.querySelectorAll('form[data-confirm]').forEach(f =>
    f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));
  document.querySelectorAll('[data-autosubmit]').forEach(s => s.addEventListener('change', () => s.form.submit()));

  // 5. Total dinámico y validación del formulario de reserva
  const formR = document.getElementById('formReserva');
  if (formR) {
    const cant = formR.querySelector('#cantidad'), fecha = formR.querySelector('#fecha');
    const total = document.getElementById('totalPagar'), err = document.getElementById('errReserva');
    const precio = parseFloat(formR.dataset.precio);
    const fmt = n => '$' + Math.round(n).toLocaleString('es-CO');
    cant.addEventListener('input', () => total.textContent = fmt((parseInt(cant.value) || 0) * precio));
    formR.addEventListener('submit', e => {
      const max = parseInt(cant.max), n = parseInt(cant.value);
      let m = '';
      if (!n || n < 1) m = 'Reserva al menos 1 persona.';
      else if (n > max) m = `Solo hay ${max} cupos disponibles.`;
      else if (!fecha.value || fecha.value < fecha.min) m = 'Elige una fecha a partir de mañana.';
      if (m) { e.preventDefault(); err.textContent = m; err.hidden = false; }
    });
  }

  // 6. Validación y medidor de fuerza de contraseña (registro)
  const formReg = document.getElementById('formRegistro');
  if (formReg) {
    const p1 = formReg.querySelector('#password'), p2 = formReg.querySelector('#password2');
    const barra = document.querySelector('#fuerza i'), err = document.getElementById('errForm');
    p1.addEventListener('input', () => {
      const v = p1.value;
      const pts = (v.length >= 8) + /[A-Z]/.test(v) + /\d/.test(v) + /[^A-Za-z0-9]/.test(v);
      barra.style.width = pts * 25 + '%';
      barra.style.background = ['#b3413a', '#b3413a', '#c8963a', '#2a6f86', '#2e5b43'][pts];
    });
    formReg.addEventListener('submit', e => {
      let m = '';
      if (formReg.nombre.value.trim().length < 3) m = 'El nombre es muy corto.';
      else if (!/^\S+@\S+\.\S+$/.test(formReg.email.value)) m = 'Escribe un correo válido.';
      else if (p1.value.length < 8 || !/\d/.test(p1.value) || !/[A-Za-z]/.test(p1.value)) m = 'La contraseña necesita 8+ caracteres, letras y números.';
      else if (p1.value !== p2.value) m = 'Las contraseñas no coinciden.';
      if (m) { e.preventDefault(); err.textContent = m; err.hidden = false; }
    });
  }

  // 7. Contador de caracteres en textareas
  document.querySelectorAll('textarea[data-counter]').forEach(t => {
    const c = t.parentElement.querySelector('.contador');
    const act = () => c.textContent = `${t.value.length} / ${t.maxLength}`;
    t.addEventListener('input', act); act();
  });

  // 8. Pestañas para filtrar la tabla de reservas por estado
  document.querySelectorAll('.tab[data-estado]').forEach(tab => tab.addEventListener('click', () => {
    document.querySelectorAll('.tab[data-estado]').forEach(t => t.classList.remove('activo'));
    tab.classList.add('activo');
    document.querySelectorAll('#tablaReservas tbody tr[data-estado]').forEach(tr =>
      tr.style.display = (tab.dataset.estado === 'todas' || tr.dataset.estado === tab.dataset.estado) ? '' : 'none');
  }));

  // 9. Contadores animados en el dashboard
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-count]').forEach(el => {
    const meta = parseInt(el.dataset.count); if (reduce || !meta) { el.textContent = meta; return; }
    let n = 0; const paso = Math.max(1, Math.ceil(meta / 25));
    const id = setInterval(() => { n = Math.min(meta, n + paso); el.textContent = n; if (n >= meta) clearInterval(id); }, 30);
  });

  // 10. Los avisos se cierran solos
  setTimeout(() => document.querySelectorAll('.flash-wrap .flash').forEach(f => f.classList.add('oculto')), 5000);
});
