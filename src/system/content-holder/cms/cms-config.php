<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => [
    'pages' => [
      'label' => 'Leadership team',
      // 'note' => 'This post type can be used to create pages',
      'fields' => [
        [
          'name' => 'test_image',
          'type' => 'media',
          'label' => 'Primary Image',
        ],
        [
          'name' => 'post_information',
          'type' => 'text',
          'label' => 'Post Information',
          'validate' => 'req|max:20',
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
        ],
        [
          'name' => 'reporter_email',
          'type' => 'email',
          'label' => 'Reporter email address',
          'validate' => 'req | email',
          'placeholder' => 'john@example.com',
          'note' => 'Email address of the reporter',
        ]
      ],
      'labels' => [
        'create_button' => 'Add News',
      ]
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
    'articles' => [
      'label' => 'Global Settings',
      'singular_label' => 'Global setting',
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
    'events' => [
      'label' => 'Events',
      'singular_label' => 'Event',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'additional-configurations' => [
      'label' => 'Additional Configurations',
      'singular_label' => 'Additional Configuration',
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
    'resources2' => [
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
    'articles2' => [
      'label' => 'Global Settings',
      'singular_label' => 'Global setting',
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
    'events2' => [
      'label' => 'Events',
      'singular_label' => 'Event',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'additional-configurations2' => [
      'label' => 'Additional Configurations',
      'singular_label' => 'Additional Configuration',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'resources3' => [
      'label' => 'Resources',
      'singular_label' => 'Resource',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'articles3' => [
      'label' => 'Global Settings',
      'singular_label' => 'Global setting',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'events3' => [
      'label' => 'Events',
      'singular_label' => 'Event',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'additional-configurations3' => [
      'label' => 'Additional Configurations',
      'singular_label' => 'Additional Configuration',
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
        'create_button' => 'Create blog post',
      ],
    ],
    'resources4' => [
      'label' => 'Resources',
      'singular_label' => 'Resource',
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
        'create_button' => 'Create blog post',
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
      'name' => 'home-hero',
      'label' => 'Home Hero',
      'note' => 'Home page hero section block',
      'template' => 'view/blocks/home-hero',
      'form' => [
        'fields' => [
          [
            'name' => 'hero__image',
            'type' => 'media',
            'label' => 'Hero Image'
          ],
          [
            'name' => 'title',
            'type' => 'text',
            'label' => 'Hero Title',
            'validate' => 'req'
          ],
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Hero banner description',
            'validate' => 'req'
          ],
          [
            'name' => 'rp1',
            'label' => 'Repeater Test',
            'type' => 'repeater',
            'fields' => [
              [
                'name' => 'card-title',
                'type' => 'text',
                'label' => 'Card Title',
                'validate' => 'req'
              ], [
                'name' => 'card_tags',
                'type' => 'repeater',
                'label' => 'Card Tags',
                'fields' => [
                  [
                    'name' => 'tag_name',
                    'type' => 'text',
                    'label' => 'Tag name',
                    'note' => 'Sample tag name test repeater field',
                    'validate' => 'req|max:25'
                  ],
                  [
                    'name' => 'tag_url',
                    'type' => 'text',
                    'label' => 'Tag URL',
                    'note' => 'Sample tag name',
                    'validate' => 'req|min:15'
                  ]
                ]
              ]
            ]
          ]
        ]
      ]
    ],
    [
      'name' => 'cta-section',
      'label' => 'CTA Section',
      // 'note' => 'Call to action component',
      'template' => 'view/blocks/cta',
      'expand' => true,
      'form' => [
        'fields' => [
          [
            'name' => 'title',
            'type' => 'text',
            'label' => 'CTA Title',
            'validate' => 'req'
          ],
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'CTA description'
          ],
          [
            'name' => 'gender',
            'type' => 'checkbox',
            'label' => 'Gender',
            'validate' => 'req',
            // 'repeat' => true,
            'options' => [
              'Male',
              'Female'
            ]
          ],
          [
            'name' => 'test_image2',
            'type' => 'media',
            'label' => 'Primary Image'
          ],
          [
            'name' => 'cta_link_url',
            'type' => 'text',
            'label' => 'CTA Link (URL)'
          ],
          [
            'name' => 'cta_link_label',
            'type' => 'text',
            'label' => 'CTA Link (Label)'
          ]
        ]
      ]
    ],
    [
      'name' => 'vision',
      'label' => 'Our vision',
      'note' => 'Our vision component',
      'template' => 'view/blocks/vision',
      'form' => [
        'fields' => [
          [
            'name' => 'title',
            'type' => 'text',
            'label' => 'Title',
            'note' => 'Vision title text here'
          ],
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'description'
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
  ]
];