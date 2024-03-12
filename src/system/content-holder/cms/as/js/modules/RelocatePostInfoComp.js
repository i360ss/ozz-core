export default () => {
  const postInfoComp =  document.getElementById('post-info-comp');
  if (postInfoComp === null) return;

  const postSidebar = document.querySelector('.post-edit-view__sidebar');
  postSidebar.insertBefore(postInfoComp, postSidebar.firstChild);
}
