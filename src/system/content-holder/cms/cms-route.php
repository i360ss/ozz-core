<?php
use Ozz\Core\Router;
use Ozz\Core\Request;
use Cms\controller\CMSAdminController;

// CMS Routes
Router::get('/lang/{lang}', function(Request $request){
  switch_language($request->urlParam('lang'));
  return back();
});

Router::getGroup(['auth', 'admin_access'], 'admin', [
  ADMIN_PATH                                       => [CMSAdminController::class, 'dashboard'],
  ADMIN_PATH.'/posts'                              => [CMSAdminController::class, 'post_type_listing'],
  ADMIN_PATH.'/posts/create/{post_type}'           => [CMSAdminController::class, 'post_create_view'],
  ADMIN_PATH.'/posts/edit/{post_type}/{post_id}'   => [CMSAdminController::class, 'post_edit_view'],
  ADMIN_PATH.'/posts/{post_type}'                  => [CMSAdminController::class, 'post_listing'],
  ADMIN_PATH.'/blocks'                             => [CMSAdminController::class, 'blocks_listing'],
  ADMIN_PATH.'/blocks/{id}'                        => [CMSAdminController::class, 'block'],
  ADMIN_PATH.'/media'                              => [CMSAdminController::class, 'media_manager'],
  ADMIN_PATH.'/media/items'                        => [CMSAdminController::class, 'media_get_items_json'],
  ADMIN_PATH.'/taxonomy'                           => [CMSAdminController::class, 'taxonomy_listing'],
  ADMIN_PATH.'/taxonomy/{slug}'                    => [CMSAdminController::class, 'taxonomy'],
  ADMIN_PATH.'/taxonomy/create'                    => [CMSAdminController::class, 'taxonomy_create_view'],
  ADMIN_PATH.'/taxonomy/edit/{id}'                 => [CMSAdminController::class, 'taxonomy_edit_view'],
  ADMIN_PATH.'/forms'                              => [CMSAdminController::class, 'forms_listing'],
  ADMIN_PATH.'/forms/{form}'                       => [CMSAdminController::class, 'form'],
  ADMIN_PATH.'/forms/create/{form}'                => [CMSAdminController::class, 'form_create_entry_view'],
  ADMIN_PATH.'/forms/edit/{form}/{entry_id}'       => [CMSAdminController::class, 'form_edit_entry_view'],
  ADMIN_PATH.'/forms/{form}/entries'               => [CMSAdminController::class, 'form_entries'],
  ADMIN_PATH.'/forms/{form}/entry/{id}'            => [CMSAdminController::class, 'form_entry'],
  ADMIN_PATH.'/settings'                           => [CMSAdminController::class, 'settings'],
]);

Router::post('/form/track', [CMSAdminController::class, 'form_tracking']);
Router::postGroup(['auth', 'admin_access'], [
  ADMIN_PATH.'/global-search'                      => [CMSAdminController::class, 'global_search'],
  ADMIN_PATH.'/posts/create/{post_type}'           => [CMSAdminController::class, 'post_create'],
  ADMIN_PATH.'/posts/update/{post_type}/{post_id}' => [CMSAdminController::class, 'post_update'],
  ADMIN_PATH.'/posts/delete'                       => [CMSAdminController::class, 'post_delete'],
  ADMIN_PATH.'/post/change-status'                 => [CMSAdminController::class, 'post_change_status'],
  ADMIN_PATH.'/post/duplicate'                     => [CMSAdminController::class, 'post_duplicate'],
  ADMIN_PATH.'/media/action'                       => [CMSAdminController::class, 'media_action'],
  ADMIN_PATH.'/taxonomy/create'                    => [CMSAdminController::class, 'taxonomy_create'],
  ADMIN_PATH.'/taxonomy/update'                    => [CMSAdminController::class, 'taxonomy_update'],
  ADMIN_PATH.'/taxonomy/delete'                    => [CMSAdminController::class, 'taxonomy_delete'],
  ADMIN_PATH.'/taxonomy/create-term'               => [CMSAdminController::class, 'taxonomy_create_term'],
  ADMIN_PATH.'/taxonomy/update-term'               => [CMSAdminController::class, 'taxonomy_update_term'],
  ADMIN_PATH.'/taxonomy/delete-term'               => [CMSAdminController::class, 'taxonomy_delete_term'],
  ADMIN_PATH.'/forms/update_entry/{entry_id}'      => [CMSAdminController::class, 'form_update_entry'],
  ADMIN_PATH.'/forms/update_entry_status'          => [CMSAdminController::class, 'form_update_entry_status'],
  ADMIN_PATH.'/forms/delete_entry'                 => [CMSAdminController::class, 'form_delete_entry'],
  ADMIN_PATH.'/settings/change-pass'               => [CMSAdminController::class, 'change_password'],
  ADMIN_PATH.'/settings/change-info'               => [CMSAdminController::class, 'change_info'],
]);
