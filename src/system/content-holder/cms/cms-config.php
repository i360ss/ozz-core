<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => [
    'pages' => [
      'label' => 'Pages',
      'note' => 'This post type can be used to create pages',
      'fields' => [
        [
          'name' => 'test_image',
          'type' => 'media',
          'label' => 'Primary Image',
        ],
        [
          'name' => 'test_name',
          'type' => 'text',
          'label' => 'Test Name',
        ]
      ]
    ],
    'news' => [
      'label' => 'News',
      'singular_label' => 'News',
      'note' => 'This is a default post type. You can overwrite or remove this.',
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
        'create_button' => 'Create blog post',
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
            'name' => 'hero_image',
            'type' => 'media',
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
          ]
        ]
      ]
    ],
    [
      'name' => 'cta-section',
      'label' => 'CTA Section',
      'note' => 'Call to action component',
      'template' => 'view/blocks/cta',
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