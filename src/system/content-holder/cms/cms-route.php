<?php
use Ozz\Core\Router;
use Ozz\Core\Request;
use Ozz\Core\Response;
use App\controller\admin\CMSAdminController;

// CMS Routes
Router::get('/lang/{lang}', function(Request $request){
  switch_language($request->urlParam('lang'));
  return back();
});

Router::getGroup(['auth'], 'admin', [
  '/admin'                                    => [CMSAdminController::class, 'dashboard'],
  '/admin/posts'                              => [CMSAdminController::class, 'post_type_listing'],
  '/admin/posts/create/{post_type}'           => [CMSAdminController::class, 'post_create_view'],
  '/admin/posts/edit/{post_type}/{post_id}'   => [CMSAdminController::class, 'post_edit_view'],
  '/admin/posts/{post_type}'                  => [CMSAdminController::class, 'post_listing'],

  '/admin/blocks'                             => [CMSAdminController::class, 'blocks_listing'],

  '/admin/media'                              => [CMSAdminController::class, 'media_manager'],
]);

Router::postGroup(['auth'], [
  '/admin/posts/create/{post_type}'           => [CMSAdminController::class, 'post_create'],
  '/admin/posts/update/{post_type}/{post_id}' => [CMSAdminController::class, 'post_update'],
  '/admin/posts/delete'                       => [CMSAdminController::class, 'post_delete'],
  '/admin/post/change-status'                 => [CMSAdminController::class, 'post_change_status'],
  '/admin/post/duplicate'                     => [CMSAdminController::class, 'post_duplicate'],
]);
