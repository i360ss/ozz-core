import { SetState } from './State';

/**
 * Bind events after close
 * @param {Function} bindAfterClose 
 */
const closePopup = ( bindAfterClose=false ) => {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = document.querySelector('#cms-popup-content');

  popupContent.innerHTML = '';
  popup.classList.remove('active');
  SetState('popup_opened', false);

  if (typeof bindAfterClose == 'function') {
    bindAfterClose();
  }
}

/**
 * Open default popup
 * @param {Element} popupDOM 
 * @param {Function} bindEvent 
 * @param {Function} afterCloseCallback 
 */
const openPopup = ( popupDOM, bindEvent=false, afterCloseCallback=false ) => {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = popup.querySelector('#cms-popup-content'),
    closeTrigger = popup.querySelector('#cms-popup-close');

  popupContent.innerHTML = '';
  popupContent.insertAdjacentHTML('beforeend', typeof popupDOM === 'object' ? popupDOM.outerHTML : popupDOM);
  popup.classList.add('active');
  SetState('popup_opened', true);

  // Binding Events
  if (typeof bindEvent == 'function') {
    bindEvent(popupContent);
  }

  // Focus field
  const inputsOnPopup = popupContent.querySelectorAll('input[type="text"]');
  if ( inputsOnPopup.length > 0 ) {
    inputsOnPopup[0].focus();
  }

  // Close popup by trigger
  closeTrigger.addEventListener('click', () => {
    closePopup(afterCloseCallback);
  }, {once: true});

  // Close popup by click on Esc
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closePopup(afterCloseCallback);
    }
  }, {once: true});
}

export { openPopup, closePopup };
