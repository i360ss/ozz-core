// Show alert
const showAlert = ( message, style='success' ) => {
  const alertBar = document.querySelector('.common-alert-bar');
  alertBar.innerHTML = `<span class="message ${style}">${message}</span>`;
  setTimeout(() => {
    alertBar.classList.add('active');
    setTimeout(() => {
      alertBar.classList.remove('active');
      setTimeout(() => { alertBar.innerHTML = ''; }, 300);
    }, 6000);
  }, 300);
};

export { showAlert };