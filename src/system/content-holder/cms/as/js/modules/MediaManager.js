import EmbedMedia from "./EmbedMedia";
import { openPopup } from "../utils/Popup";

export default () => {
  if (document.querySelectorAll( '.ozz-media-manager' ).length === 0) return;

  const
  MediaManager = document.querySelector('.ozz-media-manager'),
  mediaFileItems = MediaManager.querySelectorAll('.ozz-media-manager__item.media-file'),
  mediaViewer = MediaManager.querySelector('.ozz-media-manager__viewer'),
  actionButtons = MediaManager.querySelectorAll('.ozz-media-manager .popup-trigger');
  mediaFileItems.forEach(mediaFile => {
    mediaFile.addEventListener('click', (e) => {
      e.preventDefault();
      mediaFileItems.forEach(el => {
        el.classList.remove('active');
      });
      mediaFile.classList.add('active');
      const fileInfo = JSON.parse(mediaFile.getAttribute('data-fileInfo'));
      // Media item Embed DOM
      let fileInfoThumbnail = EmbedMedia(fileInfo);
      // File Info DOM
      const fileInfoDOM = `<div class="ozz-media-manager__info">
      <div class="ozz-media-manager__info-thumbnail">${fileInfoThumbnail}</div>
        <ul class="ozz-media-manager__info-info">
          <li><strong>Name:</strong> ${fileInfo.name}</li>
          <li><strong>Size:</strong> ${fileInfo.size}</li>
          <li><strong>URL:</strong> <a href="${fileInfo.absolute_url}" class="link" target="_blank">${fileInfo.absolute_url}</a></li>
          <li><strong>Created:</strong> ${fileInfo.created}</li>
          <li><strong>Modified:</strong> ${fileInfo.modified}</li>
          <li><strong>Access:</strong> ${fileInfo.access}</li>
          <li>
            <form action="${DATA.CMS_URL}media/action?q=delete_file" method="post" class="media-delete-form">
              <input type="hidden" value="${fileInfo.dir + fileInfo.name}" name="ozz_media_file_name_delete">
              <input type="submit" value="Delete File" class="button mini danger">
            </form>
          </li>
        </ul>
      </div>`;
      mediaViewer.innerHTML = fileInfoDOM;
      MediaManager.classList.add('viewer-active');
      mediaViewer.classList.add('active');
      mediaViewer.querySelector( 'form.media-delete-form').addEventListener('submit', ( e ) => {
        if (!confirm('The File will be deleted permanently. Are you sure?')) {
          e.preventDefault();
        }
      });
    });
  });
  // Media Actions
  actionButtons.forEach(action => {
    const actionFormDOM = action.querySelector('.hidden-action-form');
    if (actionFormDOM) {
      action.addEventListener('click', () => {
        openPopup(actionFormDOM.outerHTML);
      })
    }
  });
}
