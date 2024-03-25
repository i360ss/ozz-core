export default () => {
  // Delete entry
  const formEntryDeleteTriggers = document.querySelector('form.delete-entry');
  if (!formEntryDeleteTriggers) return;

  formEntryDeleteTriggers.addEventListener("submit", function(event) {
    if (!confirm('The Entry will be deleted permanently. Are you sure?')) {
      event.preventDefault();
    }
  });
}
