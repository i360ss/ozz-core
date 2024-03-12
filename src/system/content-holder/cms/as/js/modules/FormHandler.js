export default (e) => {
  e.preventDefault();
  const thisForm = e.target;

  // Store OzzWyg data in a hidden field
  const ozzWygEditors = thisForm.querySelectorAll('[data-ozz-wyg]');
  ozzWygEditors.forEach(wygEditor => {
    const name = wygEditor.getAttribute('data-field-name');
    const value = wygEditor.querySelector('[data-editor-area]').innerHTML;
    const existingHiddenField = thisForm.querySelector(`input[name="${name}"]`);

    if (!existingHiddenField) {
      const hdnField = document.createElement('input');
      hdnField.type = 'hidden';
      hdnField.name = name;
      hdnField.value = value;
      thisForm.appendChild(hdnField);
    } else {
      existingHiddenField.value = value;
    }
  });

  thisForm.submit();
}
