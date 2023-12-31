<?php
use Ozz\Core\Auth;

# ----------------------------------------------------
// Auth related functions
# ----------------------------------------------------
/**
 * Check if user logged in
 */
function is_logged_in(){
  return Auth::isLoggedIn();
}

/**
 * Logged User info
 * @param string $key
 */
function auth_info($key=false){
  return Auth::info($key);
}

/**
 * Get Gravatar image
 * @param string $email The email address
 * @param string $size Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $default Default image set to use [ 404 | mp | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @return string This will be an image URL
 */
function get_gravatar($email=false, $size=false, $d='identicon', $r=false){
  return Auth::getGravatar($email, $size, $d, $r);
}