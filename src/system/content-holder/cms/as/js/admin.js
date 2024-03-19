// Modules
import NavBar from './modules/NavBar';
import AlertBar from './modules/AlertBar';
import RepeaterField from './modules/RepeaterField';
import PostTabs from './modules/PostTabs';
import BlockEditor from './modules/BlockEditor';
import MediaManager from './modules/MediaManager';
import MediaManagerPopup from './modules/MediaManagerPopup';
import MultiSelector from './modules/MultiSelector';
import ChangeTheme from './modules/ChangeTheme';
import SlugUpdate from './modules/SlugUpdate';
import RelocatePostInfoComp from './modules/RelocatePostInfoComp';
import InitOzzWyg from './modules/InitOzzWyg';
import Taxonomy from './modules/Taxonomy';

(() => {
  // Ozz CMS Modules
  NavBar();
  AlertBar();
  PostTabs();
  BlockEditor();
  MediaManager();
  MediaManagerPopup();
  MultiSelector();
  ChangeTheme();
  SlugUpdate();
  RelocatePostInfoComp();
  InitOzzWyg();
  Taxonomy();

  const repeaterField = new RepeaterField();
  repeaterField.initRepeater(false, MediaManagerPopup);
})();
