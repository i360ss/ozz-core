import { send } from '../utils/Fetch';
import { showAlert } from '../utils/Alert';

/**
 * Common fetch API delete request
 * @param {String} $url 
 * @param {String} $method 
 * @param {Object} $data 
 * @param {String} $confirmation 
 */
const CommonDelete = ($url, $method, $data, $confirmation) => {
  if (confirm($confirmation)) {
    const deleteTerm = send($url, $method, JSON.stringify($data) );
    deleteTerm.then(response => {
      showAlert( response.message, response.status );
      if (response.status == 'success') {
        setTimeout(() => {
          location.reload(); 
        }, 800);
      }
    });
  }
};

export { CommonDelete };