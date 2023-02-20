<?php
use Ozz\Core\Cache;

# ----------------------------------------------------
// Application Cache
# ----------------------------------------------------
/**
 * Purge Cache
 * @param string $typ Cache type to clear
 */
function purge_cache($typ){
  return (new Cache)->purge($typ);
}