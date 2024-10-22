<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cms;

use Ozz\Core\Form;
use Ozz\Core\Medoo;

trait Taxonomy {

  /**
   * Create new taxonomy
   * @param array $taxonomy Taxonomy name and slug as array
   * @param string $lang Language code (default: current language)
   */
  protected function create_taxonomy($taxonomy, $lang=APP_LANG) {
    unset($taxonomy['crete-btn'], $taxonomy['csrf_token']);
    set_flash('form_data', $taxonomy);

    $name = $taxonomy['name'];
    $slug = $taxonomy['slug'];
    unset($taxonomy['name'], $taxonomy['slug']);

    $check_if_exist = $this->DB()->get('cms_taxonomy', ['slug'], ['slug' => $slug]);
    if(is_null($check_if_exist)){
      $taxonomy_created = $this->DB()->insert('cms_taxonomy', [
        'lang' => $lang,
        'name' => $name,
        'slug' => $slug,
        'content' => json_encode($taxonomy)
      ]);

      if ($taxonomy_created) {
        set_error('success', trans('created_success'));
        remove_flash('form_data');
      } else {
        set_error('error', trans_e('error'));
      }
    } else {
      set_error('error', trans_e('already_exist'));
    }

    return back();
  }


  /**
   * Update Taxonomy
   * @param array $data
   * @param string|integer $id_or_slug
   */
  protected function update_taxonomy($taxonomy, $id_or_slug) {
    unset($taxonomy['crete-btn'], $taxonomy['csrf_token']);
    set_flash('form_data', $taxonomy);

    $name = $taxonomy['name'];
    $slug = $taxonomy['slug'];
    unset($taxonomy['name'], $taxonomy['taxonomy_id'], $taxonomy['slug']);

    $taxonomy_updated = $this->DB()->update('cms_taxonomy', [
      'name' => $name,
      'slug' => $slug,
      'content' => json_encode($taxonomy)
    ], [ 
      'OR' => [
        'id' => $id_or_slug,
        'slug' => $id_or_slug
      ]
    ]);

    if ($taxonomy_updated) {
      set_error('success', trans('updated_success'));
      remove_flash('form_data');
    } else {
      set_error('error', trans_e('error'));
    }

    return back();
  }


  /**
   * Delete Taxonomy
   * @param integer $taxonomy_id
   */
  protected function delete_taxonomy($taxonomy_id) {
    $delete = $this->DB()->delete('cms_taxonomy', [ 'id' => $taxonomy_id ]);
    $deleteFromPosts = $this->DB()->delete('cms_post_terms', ['taxonomy_id' => $taxonomy_id]);
    $deleteTerms = $this->DB()->delete('cms_terms', ['taxonomy_id' => $taxonomy_id]);

    return ($delete && $deleteFromPosts) ? true : false;
  }


  /**
   * Get all taxonomies
   * @param array $slugs_or_ids Get only these taxonomies
   */
  protected function get_taxonomies($slugs_or_ids=[]) {
    $result = [];
    $params = [];

    $sql = "SELECT
      t1.id AS id,
      t1.name AS name,
      t1.slug AS slug,
      t1.content AS content,
        (
          SELECT JSON_ARRAYAGG( JSON_OBJECT( 
            'id', t2.id,
            'taxonomy', t2.taxonomy_id,
            'name', t2.name,
            'slug', t2.slug,
            'lang', t2.lang
            ) )
          FROM cms_terms t2
          WHERE t2.taxonomy_id = t1.id
        )
      AS terms
    FROM cms_taxonomy AS t1";

    if (!empty($slugs_or_ids)) {
        $placeholders = implode(', ', array_fill(0, count($slugs_or_ids), '?'));
        $sql .= " WHERE t1.slug IN ($placeholders) OR t1.id IN ($placeholders)";
        $params = array_merge($slugs_or_ids, $slugs_or_ids);
    }

    $stmt = $this->DB()->pdo->prepare($sql);
    $stmt->execute($params);
    $all_taxonomies = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($all_taxonomies as $key => $taxonomy) {
      $result[$taxonomy['slug']] = $taxonomy;
      $result[$taxonomy['slug']]['terms'] = !is_null($taxonomy['terms']) ? json_decode($taxonomy['terms'], true) : [];
      $result[$taxonomy['slug']]['content'] = json_decode($taxonomy['content'], true);
    }

    return $result;
  }


  /**
   * Gt single taxonomy
   * @param integer|string $id_or_slug
   */
  protected function get_taxonomy($id_or_slug) {
    $taxonomy = $this->DB()->get('cms_taxonomy', '*', [ 
      'OR' => [
        'slug' => $id_or_slug,
        'id' => $id_or_slug
      ]
    ]);
    if(!is_null($taxonomy) && isset($taxonomy['id'])){
      $taxonomy['terms'] = $this->DB()->select('cms_terms', '*', [ 'taxonomy_id' => $taxonomy['id'] ]);

      $taxonomy['content'] = json_decode($taxonomy['content'], true);
      unset(
        $taxonomy['content']['terms'],
        $taxonomy['content']['slug'],
        $taxonomy['content']['name'],
        $taxonomy['content']['lang']
      );
      $taxonomy = array_merge($taxonomy, $taxonomy['content']);
    }

    return $taxonomy;
  }


  /**
   * Get Term
   * @param string $slug_id_name Slug, ID or Name
   */
  protected function get_term($slug_id_name) {
    $term = $this->DB()->get('cms_terms', '*', [
      'OR' => [
        'id' => $slug_id_name,
        'slug' => $slug_id_name,
        'name' => $slug_id_name
      ]
    ]);

    return $term;
  }


  /**
   * Create new taxonomy term
   * @param array $term Taxonomy ID, Term name, and slug as array
   * @param string $lang Language code (default: current language)
   */
  protected function create_term($term, $lang=APP_LANG) {
    $term['lang'] = $lang;

    // Check if already exist
    $isHas = $this->DB()->get('cms_terms', '*', [
      'OR' => [
        'slug' => $term['slug'],
        'name' => $term['name']
      ],
      'AND' => [
        'taxonomy_id' => $term['taxonomy_id']
      ]
    ]);

    if(is_array($isHas) && !empty($isHas)){
      set_error('error', trans_e('already_exist'));
    } elseif($this->DB()->insert('cms_terms', $term)){
      set_error('success', trans('created_success'));
    }
  }


  /**
   * Update Term
   * @param integer $id Term ID
   * @param array $data Fields and values to be updated
   */
  protected function update_term($id, $data) {
    $update = $this->DB()->update('cms_terms', $data, ['id' => $id]);

    return $update ? true : false;
  }


  /**
   * Delete Term, and clear post links
   * @param integer $termID Term ID
   */
  protected function delete_term($termID) {
    $delete = $this->DB()->delete('cms_terms', ['id' => $termID]);
    $deleteFromPosts = $this->DB()->delete('cms_post_terms', ['term_id' => $termID]);

    return ($delete && $deleteFromPosts) ? true : false;
  }


  /**
   * Link post term relationship
   * @param integer $postID post ID or complete array (post_id, taxonomy_id, term_id)
   * @param integer|boolean $taxonomyID
   * @param integer|boolean|array $termID single ID or multiple IDs as array
   */
  protected function link_post_term($postID, $taxonomyID=false, $termID=false) {
    $data = [];
    // Insert updated taxonomies
    if(is_array($postID)) {
      $data = $postID;
    } else {
      if(is_array($termID) && !empty($termID)){
        foreach ($termID as $key => $term) {
          $data[$key] = [
            'post_id' => $postID,
            'taxonomy_id' => $taxonomyID,
            'term_id' => $term
          ];
        };
      } elseif(!empty($termID)) {
        $data = [
          'post_id' => $postID,
          'taxonomy_id' => $taxonomyID,
          'term_id' => $termID
        ];
      }
    }

    if(!empty($data)){
      $insert = $this->DB()->insert('cms_post_terms', $data);
      return $insert ? true : false;
    } else {
      return false;
    }
  }


  /**
   * Clear Post terms
   * @param integer $postID
   * @return void
   */
  protected function clear_post_terms($postID) {
    $this->DB()->delete('cms_post_terms', ['post_id' => $postID]);
  }


  /**
   * Get Post terms
   * @param integer $postID
   * @param array $what columns to be returned
   */
  protected function get_post_terms($postID) {
    $result = $this->DB()->select('cms_post_terms', [
      '[>]cms_terms' => ['term_id' => 'id'],
      '[>]cms_taxonomy' => ['taxonomy_id' => 'id'],
    ],[
      'cms_taxonomy.id(id)',
      'cms_taxonomy.name(name)',
      'cms_taxonomy.slug(slug)',
      'cms_terms.slug(term_slug)',
      'cms_terms.name(term_name)',
      'cms_terms.id(term_id)',
    ],[
      'cms_post_terms.post_id' => $postID,
    ]);

    $post_terms = [];
    foreach ($result as $row) {
      $txSlug = $row['slug'];

      $post_terms[$txSlug]['id'] = $row['id'];
      $post_terms[$txSlug]['slug'] = $txSlug;
      $post_terms[$txSlug]['name'] = $row['name'];
      $post_terms[$txSlug]['terms'][] = [
        'term_id' => $row['term_id'],
        'name' => $row['term_name'],
        'slug' => $row['term_slug']
      ];
    }

    return $post_terms;
  }


  /**
   * Setup taxonomy field values of a post
   * @param integer $postID
   */
  protected function setup_taxonomy_field_values($postID) {
    $values = [];
    $t = '___taxonomy___';
    $txs = $this->get_post_terms($postID);
    foreach ($txs as $key => $taxonomy) {
      $values[$t.'['.$key.']'] = [
        'taxonomy' => $taxonomy['id'],
        'terms' => array_column($taxonomy['terms'], 'term_id')
      ];
    }

    return $values;
  }

}