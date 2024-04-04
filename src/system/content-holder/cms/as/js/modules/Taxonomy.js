import { send } from '../utils/Fetch';
import { showAlert } from '../utils/Alert';
import { CommonDelete } from '../utils/CommonDelete';

export default () => {
  const $base_url = `${DATA.CMS_URL}taxonomy/`;
  const initTaxonomy = () => {
    // Delete Taxonomy
    const taxonomyDeleteTriggers = document.querySelectorAll('.taxonomy-listing__delete');
    if (!taxonomyDeleteTriggers || taxonomyDeleteTriggers.length === 0) return;

    taxonomyDeleteTriggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const taxonomyID = trigger.getAttribute('data-taxonomy-id');
        CommonDelete(
          $base_url+'delete',
          'POST',
          { taxonomyID: taxonomyID },
          'The Taxonomy will be deleted permanently. Are you sure?'
        );
      });
    });
  };

  const initTerm = () => {
    // Edit terms
    const termEditTriggers = document.querySelectorAll('.terms-table__edit-term');
    if (!termEditTriggers || termEditTriggers.length === 0) return;

    termEditTriggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const
          tr = trigger.closest('tr'),
          termID = tr.querySelector('input[name="term-id"]').value,
          name = tr.querySelector('input[name="term-name"]').value,
          slug = tr.querySelector('input[name="term-slug"]').value;

        const data = {
          termID: termID,
          name: name,
          slug: slug
        };

        const save = send($base_url+'update-term', 'POST', JSON.stringify(data));
        save.then(response => {
          showAlert( response.message, response.status );
        });
      });
    });

    // Delete term
    const termDeleteTriggers = document.querySelectorAll('.terms-table__delete-term');
    termDeleteTriggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const termID = trigger.closest('tr').querySelector('input[name="term-id"]').value;
        CommonDelete(
          $base_url+'delete-term',
          'POST',
          { termID: termID },
          'The Term will be deleted permanently. Are you sure?'
        );
      });
    });
  };

  initTaxonomy();
  initTerm();
}
