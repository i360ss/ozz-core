export default () => {
  const alertBar = document.querySelector('.common-alert-bar');
  if (alertBar && alertBar.querySelectorAll('.message').length > 0) {
    setTimeout(() => {
      alertBar.classList.add('active');
      setTimeout(() => {
        alertBar.classList.remove('active');
        setTimeout(() => { alertBar.innerHTML = ''; }, 300);
      }, 8000);
    }, 300);
  }
}
