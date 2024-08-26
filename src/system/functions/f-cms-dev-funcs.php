<?php
use Ozz\Core\CMS;
use Ozz\Core\Request;

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
   * Get posts by filtering
   * @param string $post_type Post type to be loaded
   * @param array $params Filters (taxonomy terms, search, WHERE, and pagination)
   * @param string $lang Language
   */
  public function get_posts($post_type=false, $params=[], $lang=APP_LANG) {
    $select = "SELECT DISTINCT p.* FROM cms_posts p";
    $joins = [];
    $where_conditions = [];
    $query_params = [];

    // Set Default filter values
    $default_where = [
      'post_status' => 'published'
    ];
    ($post_type !== false) ? $default_where['post_type'] = $post_type : false;
    ($lang !== false) ? $default_where['lang'] = $lang : false;

    if (isset($params['where'])) {
      $params['where'] = array_merge($default_where, $params['where']);
    } else {
      $params['where'] = $default_where;
    }

    // Handle taxonomy conditions
    if (isset($params['taxonomy']['values'])) {
      $taxonomy_conditions = [];
      $taxonomy_count = 0;
      foreach ($params['taxonomy']['values'] as $slug => $terms) {
        $taxonomy_count++;
        $alias_pt = "pt$taxonomy_count";
        $alias_t = "t$taxonomy_count";
        $alias_tt = "tt$taxonomy_count";

        // Generate unique parameter names for the taxonomy slug and terms
        $slug_param = ":taxonomy_slug_$taxonomy_count";
        $term_params = [];
        foreach ($terms as $index => $term) {
          $term_params[] = ":taxonomy_term_{$taxonomy_count}_{$index}";
        }
        $placeholders = implode(",", $term_params);
        $txf = is_numeric($slug) ? "$alias_t.id" : "$alias_t.slug";
        $ttf = is_numeric($terms[0]) ? "$alias_tt.id" : "$alias_tt.slug";

        $joins[] = "LEFT JOIN cms_post_terms $alias_pt ON p.id = $alias_pt.post_id";
        $joins[] = "LEFT JOIN cms_taxonomy $alias_t ON $alias_pt.taxonomy_id = $alias_t.id";
        $joins[] = "LEFT JOIN cms_terms $alias_tt ON $alias_pt.term_id = $alias_tt.id";

        $taxonomy_conditions[] = "($txf = $slug_param AND $ttf IN ($placeholders))";

        // Bind the slug parameter
        $query_params[$slug_param] = is_numeric($slug) ? $slug : str_replace('[]', '', $slug);

        // Bind the term parameters
        foreach ($terms as $index => $term) {
          $query_params[$term_params[$index]] = $term;
        }
      }

      $tx_operator = $params['taxonomy']['operator'] ?? 'AND';
      $where_conditions[] = '(' . implode(" $tx_operator ", $taxonomy_conditions) . ')';
    }

    // Handle search condition
    if (isset($params['search'])) {
      $search_query = $params['search']['query'] ?? '';
      $search_fields = $params['search']['fields'] ?? [];
      $search_operator = $params['search']['operator'] ?? 'OR';
      if (!empty($search_query)) {
        $search_conditions = [];
        if (!empty($search_fields)) {
          foreach ($search_fields as $index => $field) {
            $search_param = ":search_query_$index";
            $search_conditions[] = "p.$field LIKE $search_param";
            $query_params[$search_param] = "%$search_query%";
          }
        } else {
          $search_param = ":search_query_default";
          $search_conditions[] = "p.title LIKE $search_param";
          $query_params[$search_param] = "%$search_query%";
        }
        $where_conditions[] = '(' . implode(" $search_operator ", $search_conditions) . ')';
      }
    }

    // Handle additional WHERE conditions
    if (isset($params['where'])) {
      foreach ($params['where'] as $key => $condition) {
        $field = $key;
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? $condition;
        $placeholder = ":where_{$field}";

        // Check if the field should be treated as a JSON field
        $is_json_field = !in_array($field, [
          'id', 'post_id', 'title', 'post_type', 'slug', 'lang', 'post_status',
          'published_at', 'created_at', 'modified_at', 'content', 'blocks', 'author'
        ]);
        $field_expression = $is_json_field ? "JSON_UNQUOTE(JSON_EXTRACT(p.content, '$.\"$field\"'))" : "p.$field";

        switch (strtoupper($operator)) {
          case 'IN':
          case 'NOT IN':
            $placeholders = [];
            foreach ($value as $index => $val) {
              $placeholders[] = ":{$field}_in_$index";
              $query_params[":{$field}_in_$index"] = $val;
            }
            $placeholders_str = implode(",", $placeholders);
            $where_conditions[] = "$field_expression $operator ($placeholders_str)";
            break;
          case 'BETWEEN':
            if (is_array($value) && count($value) == 2) {
              $placeholder1 = ":{$field}_between_1";
              $placeholder2 = ":{$field}_between_2";
              $where_conditions[] = "$field_expression $operator $placeholder1 AND $placeholder2";
              $query_params[$placeholder1] = $value[0];
              $query_params[$placeholder2] = $value[1];
            }
            break;
          case 'IS NULL':
          case 'IS NOT NULL':
            $where_conditions[] = "$field_expression $operator";
            break;
          case 'LIKE':
          case 'NOT LIKE':
            $placeholder = ":{$field}_like";
            $where_conditions[] = "$field_expression $operator $placeholder";
            $query_params[$placeholder] = $value;
            break;
          default:
            $placeholder = ":{$field}_default";
            $where_conditions[] = "$field_expression $operator $placeholder";
            $query_params[$placeholder] = $value;
            break;
        }
      }
    }

    // Handle order conditions
    $order_by = '';
    if (isset($params['order'])) {
      $order_conditions = [];
      foreach ($params['order'] as $field => $direction) {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $order_conditions[] = "p.$field $direction";
      }
      if (!empty($order_conditions)) {
        $order_by = ' ORDER BY ' . implode(', ', $order_conditions);
      }
    }

    // Handle pagination
    $items_per_page = $params['pagination']['items_per_page'] ?? $params['pagination']['posts_per_page'] ?? 10;
    $page_number = $params['pagination']['page_number'] ?? $params['pagination']['page'] ?? 1;
    $offset = ($page_number - 1) * $items_per_page;

    // Build the final query
    $query = $select;
    if (!empty($joins)) {
      $query .= ' ' . implode(' ', $joins);
    }
    if (!empty($where_conditions)) {
      $query .= ' WHERE ' . implode(' AND ', $where_conditions);
    }
    if (!empty($order_by)) {
      $query .= $order_by;
    }
    $query .= " LIMIT $items_per_page OFFSET $offset";

    // Build the count query for pagination
    $count_query = "SELECT COUNT(p.id) as total_posts FROM cms_posts p";
    if (!empty($joins)) {
      $count_query .= ' ' . implode(' ', $joins);
    }
    if (!empty($where_conditions)) {
      $count_query .= ' WHERE ' . implode(' AND ', $where_conditions);
    }

    // Execute the count query
    $count_stmt = $this->DB()->pdo->prepare($count_query);
    $count_stmt->execute($query_params);
    $total_posts = $count_stmt->fetch(PDO::FETCH_ASSOC)['total_posts'];

    $total_pages = ceil($total_posts / $items_per_page);

    // Execute the main query
    $stmt = $this->DB()->pdo->prepare($query);
    $stmt->execute($query_params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($data)) {
      // Decode JSON fields
      foreach ($data as $k => $v) {
        $data[$k]['content'] = json_decode($v['content'], true);
        $data[$k]['blocks'] = json_decode($v['blocks'], true);

        // Reorder blocks by correct index
        usort($data[$k]['blocks'], function ($a, $b) {
          return $a['i'] <=> $b['i'];
        });
      }
    }

    $pagination = [
      'total_posts' => $total_posts,
      'posts_per_page' => $items_per_page,
      'current_page' => $page_number,
      'total_pages' => $total_pages,
    ];

    return [
      'posts' => $data,
      'pagination' => $pagination,
      'facets' => $params['taxonomy'] ?? [],
    ];
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

      // Reorder blocks by correct index
      usort($post['blocks'], function ($a, $b) {
        return $a['i'] <=> $b['i'];
      });
    }

    return $post;
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
  public function public_get_taxonomies($slugs_or_ids) {
    return $this->get_taxonomies($slugs_or_ids);
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
 * @param array $params Filters (Taxonomy terms, search, WHERE conditions, pagination)
 * @param string $lang Language
 */
function get_posts($post_type=false, $params=[], $lang=APP_LANG) {
  $cms = new CMSFuncs;
  return $cms->get_posts($post_type, $params, $lang);
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
 * @param array $slugs_or_ids Taxonomy IDs or Slugs to fetch
 */
function get_taxonomies($slugs_or_ids=[]) {
  $cms = new CMSFuncs;
  return $cms->public_get_taxonomies($slugs_or_ids);
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
 * Get CMS Page (render CMS page dynamically)
 * @param string $post Post slug or ID
 * @param array $data
 * @param string $view view file default: cms-page
 */
function get_cms_page($post, $data=[], $view='cms-page') {
  $data['page'] = get_post($post);
  if (!$data['page']) {
    return render_error_page();
  }

  return view($view, $data);
}

/**
 * Render Blocks
 * @param array $blocks
 * @param array $data
 */
function render_blocks($blocks, $data=[]) {
  if (!empty($blocks)) {
    foreach ($blocks as $block) {
      if ($block['b'] == 'call-component') {
        echo component($block['f']['component_name'], $data);
      } else {
        echo component($block['b'], $block['f']);
      }
    }
  }
}

/**
 * Get all available components
 */
function get_all_components() {
  $comps = get_directory_content(VIEW.'components/');
  $return = [];
  foreach ($comps as $key => $comp) {
    $nm = str_replace('.phtml', '', $comp);
    $fnm = str_replace('-', ' ', $nm);
    $return[$nm] = ucwords($fnm);
  }

  return $return;
}