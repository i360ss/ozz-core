<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => register_schema('post-types'),
  'blocks' => register_schema('blocks'),
  'forms' => register_schema('forms'),
  'languages' => [
    'en' => 'English',
    'fr' => 'French',
    'ru' => 'Russian',
  ],
  'media' => [
    'pagination_items_per_page' => 49,
    'validation' => ['20M', 'jpg|png|jpeg|svg|webp|mp4|mp3|ogg|pdf']
  ],
  'user_meta' => [
    'fields' => [
      [
        'name' => 'test',
        'type' => 'text',
        'label' => 'Test Meta Field'
      ],
      [
        'name' => 'test2',
        'type' => 'text',
        'label' => 'Test Meta Field 2'
      ]
    ]
  ],
  'CONFIG' => [
    'SANITIZE_SVG' => false,
    'SANITIZE_SVG_ALLOWED_ELEMENTS' => []
  ]
];