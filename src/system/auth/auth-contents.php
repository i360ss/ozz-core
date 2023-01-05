<?php
/**
 * Auth Migration Generate Content
 */
$auth_migration_content = "<?php
/**
 * User Migration
 */
use Ozz\Core\system\migration\Schema;

class Users {

  public function up(){
    Schema::createTable('users', [
      'user_id'           => ['int', 'ai', 'primary'],
      'username'          => ['str:150'],
      'first_name'        => ['str:150'],
      'last_name'         => ['str:150'],
      'email'             => ['str:150', 'unique'],
      'password'          => ['txt'],
      'role'              => ['str:20'],
      'status'            => ['str:20'],
      'activation_key'    => ['txt'],
      'avatar'            => ['txt'],
      'registered_at'     => ['datetime'],
      'email_verified_at' => ['datetime'],
      'last_change_at'    => ['datetime'],
    ]);
  }

  public function down(){
    Schema::dropTable('users');
  }
}";


/**
 * Auth Controller Generate Content
 */
$auth_controller_content = "<?php
namespace App\controller;

use Ozz\Core\Controller;
use Ozz\Core\Request;

class UsersController extends Controller {

  public function index(){
    
  }

}";


/**
 * Auth Model Generate Content
 */
$auth_model_content = "<?php
namespace App\model;

use Ozz\Core\Model;

class Users extends Model {

  protected \$table = 'users';

}";