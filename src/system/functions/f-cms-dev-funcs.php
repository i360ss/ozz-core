<?php
use Ozz\Core\CMS;
# ----------------------------------------------------
// CMS Specific functions (Common usage)
# ----------------------------------------------------

class CMSFuncs {
  use \Ozz\Core\DB;
  use \Ozz\Core\system\cms\Posts;
  use \Ozz\Core\system\cms\Blocks;
  use \Ozz\Core\system\cms\Settings;
  use \Ozz\Core\system\cms\Taxonomy;
  use \Ozz\Core\system\cms\Forms;

  /**
   * Gte all posts from a post type
   * @param string $post_type
   * @param array $where SQL where statement
   * @param $lang Language
   */
  public function get_posts($post_type, $where=[], $lang=APP_LANG) {
    $where = array_merge([
      'post_type' => $post_type,
      'post_status' => 'published',
      'lang' => $lang,
    ], $where);

    $posts = $this->DB()->select('cms_posts', [
      '[>]user' => ['author' => 'user_id']
    ], [
      'cms_posts.id',
      'cms_posts.post_id',
      'cms_posts.title',
      'cms_posts.post_type',
      'cms_posts.slug',
      'cms_posts.lang',
      'cms_posts.post_status',
      'cms_posts.published_at',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.blocks',
      'cms_posts.author',
      'user.first_name',
      'user.last_name',
      'user.email',
    ], $where);

    // Modify each post
    foreach ($posts as $k => $post) {
      $posts[$k]['author'] = [
        'id' => $post['author'],
        'email' => $post['email'],
        'first_name' => $post['first_name'],
        'last_name' => $post['last_name'],
      ];
      // Unset author info from main tree
      unset( $posts[$k]['email'], $posts[$k]['first_name'], $posts[$k]['last_name'] );

      $posts[$k]['content'] = json_decode($post['content'], true);
      $posts[$k]['blocks'] = json_decode($post['blocks'], true);
    }

    return $posts;
  }


  /**
   * Return single post
   * @param int|string $post_id_slug Post ID (id) or slug
   * @param array $where SQL where arguments
   * @param string $lang Language code
   */
  public function get_post($post_id_slug, $where=[], $lang=APP_LANG) {
    $where = array_merge([
      'OR' => [
        'id' => $post_id_slug,
        'slug' => $post_id_slug
      ],
      'post_status' => 'published',
      'lang' => $lang
    ], $where);

    $post = $this->DB()->get('cms_posts', [
      '[>]user' => ['author' => 'user_id']
    ], [
      'cms_posts.id',
      'cms_posts.post_id',
      'cms_posts.title',
      'cms_posts.post_type',
      'cms_posts.slug',
      'cms_posts.lang',
      'cms_posts.post_status',
      'cms_posts.created_at',
      'cms_posts.published_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.blocks',
      'cms_posts.author',
      'user.first_name',
      'user.last_name',
      'user.email',
    ], $where);

    if(!is_null($post)){
      $post['author'] = [
        'id' => $post['author'],
        'email' => $post['email'],
        'first_name' => $post['first_name'],
        'last_name' => $post['last_name'],
      ];

      // Unset author info from main tree
      unset( $post['email'], $post['first_name'], $post['last_name'] );

      // Bring content to the main tree
      $post['content'] = json_decode($post['content'], true);
      $post['blocks'] = json_decode($post['blocks'], true);
      $post['terms'] = $this->get_post_terms($post['id']);
    }

    return $post;
  }


  /**
   * Get Filtered posts
   * @param string $post_type
   * @param array $filters
   * @param string $lang
   * @return array Posts anf filtered by Facets
   */
  public function filter_posts($post_type, $filters=[], $lang=APP_LANG) {
    function build_where($post_type, $filters, $lang) {
      $where_conditions = [];
      $params = [];

      // Taxonomy conditions
      if (isset($filters['taxonomy'])) {
        foreach ($filters['taxonomy'] as $slug => $terms) {
          $placeholders = implode(",", array_fill(0, count($terms), "?"));
          $txf = is_numeric($slug) ? 't.id' : 't.slug';
          $ttf = is_numeric($terms[0]) ? 'tt.id' : 'tt.slug';
          $where_conditions[] = "($txf = ? AND $ttf IN ($placeholders))";
          $params[] = is_numeric($slug) ? $slug : str_replace('[]', '', $slug);
          $params = array_merge($params, $terms);
        }
      }

      // Search condition
      if (isset($filters['search'])) {
        $search_query = $filters['search']['query'] ?? '';
        $search_fields = $filters['search']['fields'] ?? [];
        if (!empty($search_query)) {
          $search_conditions = [];
          if (!empty($search_fields)) {
            foreach ($search_fields as $field) {
              $search_conditions[] = "p.$field LIKE ?";
              $params[] = "%$search_query%";
            }
          } else {
            $search_conditions[] = "p.title LIKE ?";
            $params[] = "%$search_query%";
          }
          $where_conditions[] = '(' . implode(" OR ", $search_conditions) . ')';
        }
      }

      // Additional conditions
      $where = array_merge([
        'post_type' => $post_type,
        'post_status' => 'published',
        'lang' => $lang,
      ], $filters['where'] ?? []);

      foreach ($where as $key => $value) {
        $where_conditions[] = "p.$key = ?";
        $params[] = $value;
      }

      return [$where_conditions, $params];
    }

    list($where_conditions, $params) = build_where($post_type, $filters, $lang);
    $query = "SELECT DISTINCT p.*
      FROM cms_posts p
      INNER JOIN cms_post_terms pt ON p.id = pt.post_id
      INNER JOIN cms_taxonomy t ON pt.taxonomy_id = t.id
      INNER JOIN cms_terms tt ON pt.term_id = tt.id
      WHERE " . implode(" AND ", $where_conditions);

    $stmt = $this->DB()->pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($data as $k => $v) {
      $data[$k]['content'] = json_decode($v['content'], true);
      $data[$k]['blocks'] = json_decode($v['blocks'], true);
    }

    return ['facets' => $filters, 'posts' => $data];
  }


  /**
   * Get post terms
   * @param integer $post_id
   */
  public function public_get_post_terms($post_id) {
    return $this->get_post_terms($post_id);
  }


  /**
   * Get All taxonomies
   */
  public function public_get_taxonomies() {
    return $this->get_taxonomies();
  }


  /**
   * Get Single taxonomies
   * @param integer|string $id_or_slug
   */
  public function public_get_taxonomy($id_or_slug) {
    return $this->get_taxonomy($id_or_slug);
  }
}

/**
 * Get single post
 * @param string|integer $post_id_or_slug
 * @return array Post data
 */
function get_post($post_id_or_slug, $where=[], $lang=APP_LANG) {
  $cms = new CMSFuncs;
  return $cms->get_post($post_id_or_slug, $where, $lang);
}

/**
 * Get All posts from a post type
 * @param string $post_type
 * @param array $where SQL where statement
 * @param $lang Language
 */
function get_posts($post_type, $where=[], $lang=APP_LANG) {
  $cms = new CMSFuncs;
  return $cms->get_posts($post_type, $where, $lang);
}

/**
 * Get all terms of a single post
 * @param string|integer $post_id
 */
function get_post_terms($post_id) {
  $cms = new CMSFuncs;
  return $cms->public_get_post_terms($post_id);
}

/**
 * Return all available taxonomies
 */
function get_taxonomies() {
  $cms = new CMSFuncs;
  return $cms->public_get_taxonomies();
}

/**
 * Return single taxonomy
 * @param integer|string $id_or_slug
 */
function get_taxonomy($id_or_slug) {
  $cms = new CMSFuncs;
  return $cms->public_get_taxonomy($id_or_slug);
}

/**
 * Filter posts
 * @param string $post_type
 * @param array $filters (taxonomy:array(key->term), search:array(query, fields), where:array)
 */
function filter_posts($post_type, $filters) {
  $cms = new CMSFuncs;
  return $cms->filter_posts($post_type, $filters);
}
