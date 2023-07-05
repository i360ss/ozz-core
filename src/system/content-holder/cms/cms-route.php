<?php
use Ozz\Core\Router;
use Ozz\Core\Request;
use Ozz\Core\Response;
use App\controller\admin\CMS_AdminController;

// CMS Routes
Router::get('/lang/{lang}', function(Request $request){
  switch_language($request->urlParam('lang'));
  return back();
});

Router::getGroup(['auth'], 'admin', [
  '/admin'                                    => [CMS_AdminController::class, 'dashboard'],
  '/admin/posts'                              => [CMS_AdminController::class, 'post_type_listing'],
  '/admin/posts/create/{post_type}'           => [CMS_AdminController::class, 'post_create_view'],
  '/admin/posts/edit/{post_type}/{post_id}'   => [CMS_AdminController::class, 'post_edit_view'],
  '/admin/posts/{post_type}'                  => [CMS_AdminController::class, 'post_listing'],
  
  '/admin/blocks'                             => [CMS_AdminController::class, 'blocks_listing'],
]);

Router::postGroup(['auth'], [
  '/admin/posts/create/{post_type}'           => [CMS_AdminController::class, 'post_create'],
  '/admin/posts/update/{post_type}/{post_id}' => [CMS_AdminController::class, 'post_update'],
  '/admin/posts/delete'                       => [CMS_AdminController::class, 'post_delete'],
  '/admin/post/change-status'                 => [CMS_AdminController::class, 'post_change_status'],
  '/admin/post/duplicate'                     => [CMS_AdminController::class, 'post_duplicate'],
]);
