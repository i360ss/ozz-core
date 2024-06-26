<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => [
    'pages' => [
      'label' => 'Leadership team',
      'note' => 'This post type can be used to create pages',
      'fields' => [
        [
          'name' => 'test_image',
          'type' => 'media',
          'label' => 'Primary Image',
          'validate' => 'req',
        ],
        [
          'name' => 'post_information',
          'type' => 'text',
          'label' => 'Post Information',
          'validate' => 'req|max:20',
          'note' => 'Test Note'
        ],
        [
          'name' => 'post-body',
          'type' => 'rich-text',
          'label' => 'Post Body',
        ],
        [
          'name' => 'c-test',
          'type' => 'repeater',
          'label' => 'Country',
          'fields' => [
            [
              'name' => 'country',
              'type' => 'select',
              'label' => 'Country',
              'validate' => 'req',
              'id' => 'country',
              'options' => [
                'Sri Lanka',
                'Afganistan',
                'United Kingdom',
                'Russia',
                'Saudi Arabia',
              ]
            ]
          ]
        ],
        [
          'name' => 'another-rp-field2',
          'type' => 'select',
          'label' => 'Another Repeater Field',
          'repeat_label' => 'Add Item',
          'validate' => 'req',
          // 'repeat' => true,
          'options' => [
            '' => '-- Select a value --',
            't1' => 'Test One',
            't2' => 'Test Two',
            't3' => 'Test Three'
          ]
        ]
      ],
      'tabs' => [
        'seo' => [
          'slug' => 'seo',
          'label' => 'SEO',
          'fields' => [
            [
              'name' => 'meta-title',
              'type' => 'text',
              'label' => 'Title',
              'validate' => 'req|max:20',
            ],
            [
              'name' => 'meta-description',
              'type' => 'textarea',
              'label' => 'Meta Description',
            ],
            [
              'name' => 'meta-keywords',
              'type' => 'text',
              'label' => 'Meta Keywords',
              'note' => 'Separate each keyword by commas (,)'
            ]
          ]
        ],
        'additional-settings' => [
          'slug' => 'additional-settings',
          'label' => 'Additional Settings',
          'fields' => [
            [
              'name' => 'size',
              'type' => 'text',
              'label' => 'Size',
            ],
            [
              'name' => 'color',
              'type' => 'select',
              'label' => 'Color',
              'options' => [
                'Green',
                'Red',
                'Black',
                'White',
              ]
            ]
          ]
        ]
      ]
    ],
    'news' => [
      'label' => 'News',
      'singular_label' => 'News',
      'fields' => [
        [
          'name' => 'description',
          'type' => 'textarea',
          'label' => 'News Body',
          'validate' => 'req',
          'placeholder' => 'Write something here',
        ],
        [
          'name' => 'reporter_name',
          'type' => 'text',
          'label' => 'Reporter name',
          'validate' => 'req',
          'placeholder' => 'John Doe',
          'note' => 'Name of the reporter',
          'wrapper_class' => 'cl cl-6'
        ],
        [
          'name' => 'reporter_email',
          'type' => 'email',
          'label' => 'Reporter email address',
          'validate' => 'req | email',
          'placeholder' => 'john@example.com',
          'note' => 'Email address of the reporter',
          'wrapper_class' => 'cl cl-6'
        ]
      ],
      'labels' => [
        'create_button' => 'Add News',
      ],
      'taxonomies' => [
        'type',
        'country'
      ],
    ],
    'resources' => [
      'label' => 'Resources',
      'singular_label' => 'Resource',
      'form' => [
        'fields' => [
          [
            'name' => 'post_body',
            'type' => 'textarea',
            'label' => 'Post Body',
            'validate' => 'req'
          ]
        ]
      ],
      'labels' => [
        'create_button' => 'Create blog post',
      ],
    ],
    'products' => [
      'label' => 'Products',
      'singular_label' => 'Product',
      'taxonomies' => [
        'gsm',
        'sizes',
        'colors'
      ],
      'fields' => [
        [
          'name' => 'image',
          'type' => 'media',
          'label' => 'Product Primary Image',
          'validate' => 'req'
        ]
      ],
      'labels' => [
        'create_button' => 'Add Product',
      ],
    ],
    'blog' => [
      'label' => 'Blogs',
      'singular_label' => 'Blog',
      'note' => 'This is a default post type. You can overwrite or remove this.',
      'form' => [
        'fields' => [
          [
            'name' => 'post_body',
            'type' => 'textarea',
            'label' => 'Post Body',
            'validate' => 'req'
          ]
        ]
      ],
      'labels' => [
        'create_button' => 'Create event',
      ],
    ]
  ],
  'blocks' => [
    [
      'name' => 'rich-text',
      'label' => 'Rich Text',
      'form' => [
        'fields' => [
          [
            'name' => 'rich_text',
            'type' => 'rich-text',
            'label' => 'Rich Text Content',
            'rows' => '10'
          ]
        ]
      ]
    ],
    [
      'name' => 'call-component',
      'label' => 'Call Component',
      'form' => [
        'fields' => [
          [
            'name' => 'component_name',
            'type' => 'text',
            'label' => 'Component Name',
            'validate' => 'required'
          ]
        ]
      ]
    ]
  ],
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