<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cms;

trait Utilities {

  /**
   * CMS Global search
   * @param string $keyword
   * @param string $lang Language code (default: current language)
   */
  protected function cms_global_search($keyword, $lang=APP_LANG) {
    // Fetch posts
    $posts = $this->DB()->select('cms_posts', [
      'post_id(post_id)',
      'post_type(type)',
      'title',
    ], [
      'AND' => [
        'OR' => [
          'post_type[~]' => $keyword,
          'content[~]' => $keyword,
          'title[~]' => $keyword,
          'slug[~]' => $keyword,
          'blocks[~]' => $keyword,
        ],
        'lang' => $lang,
      ]
    ]);

    foreach ($posts as $k => $post) {
      $posts[$k]['type'] = 'Post: '.$post['type'];
      $posts[$k]['url'] = '/admin/posts/edit/'.$post['type'].'/'.$post['post_id'];
    }

    // Fetch forms
    $forms = $this->DB()->select('cms_forms', [
      'id',
      'name',
      'content'
    ], [
      'OR' => [
        'name[~]' => $keyword,
        'content[~]' => $keyword,
      ]
    ]);

    foreach ($forms as $k => $entry) {
      $content = implode(', ', array_values(json_decode($entry['content'], true)));
      $forms[$k]['title'] = char_limit($content , 40, '...');
      $forms[$k]['type'] = 'Entry: '.$entry['name'];
      $forms[$k]['url'] = '/admin/forms/'.$entry['name'].'/entry/'.$entry['id'];
    }

    // Fetch terms
    $terms = $this->DB()->select('cms_terms', [
      '[>]cms_taxonomy' => ['taxonomy_id' => 'id']
    ],[
      'cms_taxonomy.name(name)',
      'cms_taxonomy.slug(slug)',
      'cms_terms.name(term_name)',
    ], [
      'AND' => [
        'OR' => [
          'cms_terms.name[~]' => $keyword,
          'cms_terms.slug[~]' => $keyword,
          'cms_taxonomy.name[~]' => $keyword,
          'cms_taxonomy.slug[~]' => $keyword,
        ],
        'cms_terms.lang' => $lang,
      ]
    ]);

    foreach ($terms as $k => $term) {
      $terms[$k]['title'] = 'Term: '.$term['term_name'];
      $terms[$k]['type'] = 'Taxonomy: '.$term['name'];
      $terms[$k]['url'] = '/admin/taxonomy/'.$term['slug'];
    }

    return array_merge($posts, $forms, $terms);
  }


  /**
   * New items notification
   * Fetch new entries from SQLite and send to the view to notify
   */
  protected function get_new_items_notification() {
    // Form Entries
    $entries = ozz_log_get('ozz_notification', 'key, item_id', []);
    $entries_count = [];

    if($entries && !empty($entries)){
      foreach ($entries as $entry) {
        $key = $entry['key'];
        if (isset($entries_count[$key])) {
          $entries_count[$key]['count']++;
        } else {
          $entries_count[$key]['count'] = 1;
        }
        $entries_count[$key]['ids'][] = $entry['item_id'];
      }
    }

    // All notifications
    return [
      'form_entries' => $entries_count,
      // implement any other notifications here if required
    ];
  }

}