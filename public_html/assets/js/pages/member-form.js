// Diğer seçenekleri için metin alanlarını aç/kapat (gönderim: assets/js/forms.js)
(function () {
  const otherCheck = document.getElementById('activeOtherCheck');
  const otherText = document.getElementById('activeOtherText');
  const sektor = document.getElementById('sektor');
  const sektorDiger = document.getElementById('sektorDiger');
  const uzmanlikDigerCheck = document.getElementById('uzmanlikDigerCheck');
  const uzmanlikDiger = document.getElementById('uzmanlikDiger');

  otherText.disabled = true;
  sektorDiger.disabled = true;
  uzmanlikDiger.disabled = true;

  sektor.addEventListener('change', function () {
    const isOther = this.value === 'Diğer';
    sektorDiger.style.display = isOther ? 'block' : 'none';
    sektorDiger.disabled = !isOther;
    sektorDiger.required = isOther;

    if (isOther) {
      sektorDiger.focus();
    } else {
      sektorDiger.value = '';
    }
  });

  uzmanlikDigerCheck.addEventListener('change', function () {
    uzmanlikDiger.style.display = this.checked ? 'block' : 'none';
    uzmanlikDiger.disabled = !this.checked;
    uzmanlikDiger.required = this.checked;

    if (this.checked) {
      uzmanlikDiger.focus();
    } else {
      uzmanlikDiger.value = '';
    }
  });

  otherCheck.addEventListener('change', function () {
    otherText.disabled = !this.checked;
    if (this.checked) {
      otherText.focus();
    } else {
      otherText.value = '';
    }
  });

})();
