export default () => {
  const
    updateThis = document.querySelector('.j-title-onchange'),
    getFrom = document.querySelector('.ozz-fm .default-post-title');

  if (getFrom) {
    getFrom.addEventListener('keyup', () => {
      const slug = getFrom.value.replace(/\s+/g, '-').toLowerCase().replace(/[^\w\-]+/g, '');
      document.querySelector('.ozz-fm .default-post-slug').value = slug;
      if (updateThis) {
        updateThis.innerHTML = getFrom.value;
      }
    });
  }
}
