const errorBox = document.querySelector('.error-box');
const errorMsg = document.querySelector('.error-box span');

if (errorMsg && errorMsg.textContent.trim() !== '') {
  errorBox.classList.remove('invisible')
  setTimeout(() => {
    errorBox.style.transition = 'opacity 3s';
    errorBox.style.opacity = '0';
  }, 2000);
}