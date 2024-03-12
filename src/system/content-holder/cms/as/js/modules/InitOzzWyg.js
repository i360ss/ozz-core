import OzzWyg from '../vendor/ozz-wyg';

export default () => {
  if (typeof OzzWyg === 'function') {
    new OzzWyg({ selector: '[data-ozz-wyg]' });
  }
}
