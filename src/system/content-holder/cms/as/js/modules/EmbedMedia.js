export default (fileInfo) => {
  let fileInfoThumbnail = '';
  if ( fileInfo.format == 'image' ) {
    fileInfoThumbnail = `<img src="${fileInfo.absolute_url}">`;
  }
  else if ( fileInfo.format == 'svg' ) {
    fileInfoThumbnail = `<object type="image/svg+xml" data="${fileInfo.absolute_url}">
      <img src="${fileInfo.absolute_url}" />
    </object>`;
  }
  else if ( fileInfo.format == 'video' ) {
    fileInfoThumbnail = `<video width="100%" controls>
      <source src="${fileInfo.absolute_url}" type="video/mp4">
      <source src="${fileInfo.absolute_url}" type="video/ogg">
    </video>`;
  }
  else if ( fileInfo.format == 'audio' ) {
    fileInfoThumbnail = `<audio controls>
      <source src="${fileInfo.absolute_url}" type="audio/ogg">
      <source src="${fileInfo.absolute_url}" type="audio/mpeg">
    </audio>`;
  }
  else if ( fileInfo.format == 'vimeo' ) {
    fileInfoThumbnail = `<iframe src="${fileInfo.absolute_url}" width="100%" frameborder="0" picture-in-picture allowfullscreen></iframe>`;
  }
  else if ( fileInfo.format == 'youtube' ) {
    fileInfoThumbnail = `<iframe width="100%" src="${fileInfo.absolute_url}"></iframe>`;
  }
  else if ( fileInfo.format == 'pdf' ) {
    fileInfoThumbnail = `<object data="${fileInfo.absolute_url}" type="application/pdf" width="100%" height="400px">
      <p>Unable to display PDF file. <a href="${fileInfo.absolute_url}">Download</a> instead.</p>
    </object>`;
  }
  else if ( ['word', 'powerpoint', 'pptx', 'excel', 'odt', 'ods'].includes(fileInfo.format) ) {
    fileInfoThumbnail = `<object data="${fileInfo.absolute_url}"
      type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" width="100%" height="400px">
      <p>Unable to display the file.<br>Click <a href="${fileInfo.absolute_url}" target="_blank">here</a> to open it</p>
    </object>`;
  }
  else if ( ['text', 'swf', 'json'].includes(fileInfo.format) ) {
    fileInfoThumbnail = `<iframe width="100%" height="400px" src="${fileInfo.absolute_url}" frameborder="0"></iframe>`;
  }
  else {
    fileInfoThumbnail = `<span class="icon ${fileInfo.format}"></span>`;
  }

  return fileInfoThumbnail;
}
