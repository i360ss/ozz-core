<?php
return [
  // General
  'CHARSET' => 'utf-8',
  'SESSION_DRIVER' => 'memory',
  'SESSION_COOKIE_NAME' => 'ozz_ses_id',
  'SESSION_LIFETIME' => 1800,
  'SESSION_PREFIX' => 'ozz_ses_',
  'SESSION_DIRECTORY' => 'storage/session',
  'SESSION_SECRET_KEY' => '',
  'SESSION_SECURE_COOKIE' => true,
  'SESSION_HTTP_ONLY' => true,
  'SESSION_DOMAIN' => '',
  'SESSION_PATH' => '/',
  'SESSION_SAME_SITE' => 'Strict', // Strict, Lax or None
  'CSRF_COOKIE_LIFETIME' => 1800,
  'PAGE_CACHE_LIFETIME' => false,
  'MINIFY_HTML' => false,
  'OZZ_EXCEPTION_HANDLER' => true,
  'ERROR_LOG' => false,
  'DEFAULT_ERROR_PAGE_BASE_LAYOUT' => 'layout',
  'DB_DEFAULT_ENGINE' => 'InnoDB',
  'DB_DEFAULT_COLLATION' => 'utf8mb4_unicode_520_ci',
  'DB_DEFAULT_CHARSET' => 'utf8mb4',
  'EMAIL_CHARSET' => 'utf-8',
  'SANITIZE_SVG' => false,
  'SANITIZE_SVG_ALLOWED_ELEMENTS' => [],

  // Auth
  'AUTH_CONTROLLER' => 'AuthController',
  'AUTH_MIDDLEWARE_NAME' => 'AuthMiddleware',
  'AUTH_USERS_TABLE' => 'user',
  'AUTH_META_TABLE' => 'user_meta',
  'AUTH_LOG_TABLE' => 'user_log',
  'AUTH_ACTIVATE_AND_LOGIN_ONCE_SIGNUP' => false,
  'AUTH_SEND_VERIFICATION_MAIL' => true,
  'AUTH_NEW_LOGIN_ALERT' => true,
  'AUTH_PASSWORD_CHANGED_ALERT' => true,
  'AUTH_LOGOUT_ON_PASSWORD_CHANGE' => true,
  'PASSWORD_RESET_LINK_LIFETIME' => 60*30,
  'EMAIL_VERIFICATION_LINK_LIFETIME' => 60*30,
  'AUTH_PASSWORD_RESET_THROTTLE' => [
    'ENABLE' => true,
    'MAX_ATTEMPTS' => 3,
    'PERIOD' => 60*10,
    'DELAY_TIME' => 60
  ],
  'AUTH_LOGIN_THROTTLE' => [
    'ENABLE' => true,
    'MAX_ATTEMPTS' => 3,
    'PERIOD' => 60*2,
    'DELAY_TIME' => 60
  ],
  'AUTH_EMAIL_CHANGE_THROTTLE' => [
    'ENABLE' => true,
    'MAX_ATTEMPTS' => 3,
    'PERIOD' => 60*10,
    'DELAY_TIME' => 60
  ],
  'AUTH_CORE_FIELDS' => [
    'ID_FIELD' => 'user_id',
    'USERNAME_FIELD' => 'username',
    'EMAIL_FIELD' => 'email',
    'PASSWORD_FIELD' => 'password',
    'FIRST_NAME_FIELD' => 'first_name',
    'LAST_NAME_FIELD' => 'last_name',
    'STATUS_FIELD' => 'status',
    'ROLE_FIELD' => 'role',
    'AVATAR_FIELD' => 'avatar',
    'ACTIVATION_KEY_FIELD' => 'activation_key'
  ],
  'AUTH_ALLOWED_FIELDS' => [
    'user_id',
    'username',
    'first_name',
    'last_name',
    'email',
    'password',
    'role',
    'status',
    'avatar',
    'activation_key'
  ],
  'AUTH_USER_ROLES' => [
    'admin' => [
      'landing_page' => '/admin'
    ]
  ],
  'AUTH_VIEWS' => [
    'sign-up' => 'sign-up.phtml',
    'login' => 'login.phtml',
    'forgot-password' => 'forgot-password.phtml',
    'reset-password' => 'reset-password.phtml',
    'verify-account' => 'verify-account.phtml',
    'admin' => 'admin.phtml'
  ],
  'AUTH_PATHS' => [
    'login' => '/login',
    'logout' => '/logout',
    'signup' => '/sign-up',
    'forgot_password' => '/forgot-password',
    'reset_password' => '/reset-password',
    'verify_account' => '/verify-account',
    'verify_email' => '/verify-email'
  ],
  'AUTH_EMAIL_TEMPLATES' => [
    'account-verification' => 'account-verification.phtml',
    'email-change-verification' => 'email-verification.phtml',
    'email-changed-alert' => 'email-changed-alert.phtml',
    'new-login-alert' => 'new-login-alert.phtml',
    'register-alert' => 'register-alert.phtml',
    'password-reset-request' => 'password-reset-request.phtml',
    'password-changed-alert' => 'password-changed-alert.phtml',
  ],

  // Default File Validation
  'DEFAULT_FILE_VALIDATION' => [
    'image'     => [ '1M', 'jpg|jpeg|png|svg|webp' ],
    'document'  => [ '500K', 'pdf|docx|txt|csv|ttf|otf' ],
    'audio'     => [ '6M', 'mp3' ],
    'video'     => [ '12M', 'mp4' ],
    'font'      => [ '60K', 'ttf|otf' ]
  ]
];