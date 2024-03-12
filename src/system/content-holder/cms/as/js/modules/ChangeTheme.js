import { SetState, GetState } from '../utils/State';

export default () => {
  const
  appBody = document.querySelector('body.ozz-cms'),
  changeTrigger = document.getElementById('ozz-color-theme-switcher');

  changeTrigger.addEventListener('change', (e) => {
    SetState('theme', e.target.checked ? 'dark' : 'light');
    appBody.setAttribute('data-theme', GetState('theme'));
  });

  appBody.setAttribute('data-theme', GetState('theme'));
  if (GetState('theme') == 'dark') {
    changeTrigger.checked = true;
  }
}
