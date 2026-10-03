const form = document.getElementById('receiptForm');
const receipt = document.getElementById('receipt');
const fileName = document.getElementById('fileName');
const errorMessage = document.getElementById('errorMessage');
const newMemberCheck = document.getElementById('newMemberCheck');
const renewalCheck = document.getElementById('renewalCheck');
const corporateCheck = document.getElementById('corporateCheck');
const individualCheck = document.getElementById('individualCheck');

function syncMembershipChoices() {
  if (newMemberCheck.checked) {
    renewalCheck.checked = false;
    renewalCheck.disabled = true;
  } else if (renewalCheck.checked) {
    newMemberCheck.checked = false;
    newMemberCheck.disabled = true;
  } else {
    newMemberCheck.disabled = false;
    renewalCheck.disabled = false;
  }
}

newMemberCheck.addEventListener('change', syncMembershipChoices);
renewalCheck.addEventListener('change', syncMembershipChoices);

function syncMemberTypeChoices() {
  if (corporateCheck.checked) {
    individualCheck.checked = false;
    individualCheck.disabled = true;
  } else if (individualCheck.checked) {
    corporateCheck.checked = false;
    corporateCheck.disabled = true;
  } else {
    corporateCheck.disabled = false;
    individualCheck.disabled = false;
  }
}

corporateCheck.addEventListener('change', syncMemberTypeChoices);
individualCheck.addEventListener('change', syncMemberTypeChoices);

receipt.addEventListener('change', () => {
  if (receipt.files && receipt.files[0]) {
    fileName.textContent = receipt.files[0].name;
    fileName.style.display = 'block';
  } else {
    fileName.textContent = '';
    fileName.style.display = 'none';
  }
});

// Gönderim assets/js/forms.js ile yapılır; burada yalnızca Üye Durumu seçimini kontrol ediyoruz.
form.addEventListener('wbn:beforesubmit', (event) => {
  errorMessage.style.display = 'none';

  if (!newMemberCheck.checked && !renewalCheck.checked) {
    event.preventDefault();
    errorMessage.textContent = 'Lütfen Üye Durumu alanında bir seçenek işaretleyin.';
    errorMessage.style.display = 'block';
    document.getElementById('membershipChoiceGroup').scrollIntoView({behavior:'smooth', block:'center'});
  }
});
