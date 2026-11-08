// Ortak form gönderimi.
// Kullanım: <form data-api="/api/..." [data-success="#basariMesaji"] [data-hide="#formIcerigi"]>
// Sayfa script'leri gönderimden önce kontrol için 'wbn:beforesubmit' olayını dinleyip
// event.preventDefault() ile gönderimi durdurabilir.
(function () {
  function fieldWrapper(form, name) {
    const input = form.querySelector('[name="' + name + '"], [name="' + name + '[]"]');
    return input ? (input.closest('.field') || input.parentElement) : null;
  }

  function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => el.remove());
    form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
    const box = form.querySelector('.form-error');
    if (box) box.hidden = true;
  }

  function showErrors(form, message, errors) {
    let box = form.querySelector('.form-error');
    if (!box) {
      box = document.createElement('div');
      box.className = 'form-error';
      box.setAttribute('role', 'alert');
      const submit = form.querySelector('[type="submit"]');
      submit.insertAdjacentElement('afterend', box);
    }
    box.textContent = message || 'Gönderim sırasında bir sorun oluştu. Lütfen tekrar deneyin.';
    box.hidden = false;

    let first = null;
    Object.entries(errors || {}).forEach(([name, text]) => {
      const wrapper = fieldWrapper(form, name);
      if (!wrapper) return;
      wrapper.classList.add('has-error');
      const note = document.createElement('small');
      note.className = 'field-error';
      note.textContent = text;
      wrapper.appendChild(note);
      first = first || wrapper;
    });
    (first || box).scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function showSuccess(form, message) {
    const target = form.dataset.success && document.querySelector(form.dataset.success);
    const hide = (form.dataset.hide && document.querySelector(form.dataset.hide)) || form;
    hide.style.display = 'none';

    if (target) {
      target.style.display = 'block';
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      target.setAttribute('role', 'status');
      target.tabIndex = -1;
      target.focus({ preventScroll: true });
      return;
    }
    const box = document.createElement('div');
    box.className = 'form-success';
    box.setAttribute('role', 'status');
    box.innerHTML = '<div class="form-success__icon" aria-hidden="true">✓</div><h3>Teşekkür ederiz</h3><p></p>';
    box.querySelector('p').textContent = message;
    box.tabIndex = -1;
    hide.insertAdjacentElement('afterend', box);
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    box.focus({ preventScroll: true });
  }

  document.querySelectorAll('form[data-api]').forEach(form => {
    const button = form.querySelector('[type="submit"]');
    const label = button ? button.textContent : '';

    form.addEventListener('submit', async event => {
      event.preventDefault();
      clearErrors(form);

      const check = new CustomEvent('wbn:beforesubmit', { cancelable: true });
      if (!form.dispatchEvent(check)) return;
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      if (button) {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.textContent = 'Gönderiliyor...';
      }

      try {
        const response = await fetch(form.dataset.api, {
          method: 'POST',
          body: new FormData(form),
          headers: { Accept: 'application/json' }
        });
        const data = await response.json().catch(() => ({}));

        if (response.ok && data.ok) {
          showSuccess(form, data.message || 'Gönderiminiz alındı.');
          return;
        }
        showErrors(form, data.message, data.errors);
      } catch (error) {
        showErrors(form, 'Bağlantı hatası oluştu. Lütfen internet bağlantınızı kontrol edip tekrar deneyin.');
      }

      if (button) {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.textContent = label;
      }
    });
  });
})();
