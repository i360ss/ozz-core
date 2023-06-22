<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => [
    'news' => [
      'label' => 'News',
      'singular_label' => 'News',
      'note' => 'This is a default post type provided by ozz CMS. You can overwrite or remove this post type if not required.',
      'form' => [
        'fields' => [
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'News Body',
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
        ]
      ],
      'labels' => [
        'create_button' => 'Add News',
      ]
    ],
    'blog' => [
      'label' => 'Blogs',
      'singular_label' => 'Blog',
      'note' => 'This is a default post type provided by ozz CMS. You can overwrite or remove this post type if not required.',
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
        'create_button' => 'Create Blog post',
      ],
    ]
  ],
  'blocks' => [
    [
      'name' => 'Home Hero',
      'note' => 'Home page hero section block',
      'slug' => 'home-hero',
      'fields' => [
        [
          'name' => 'title',
          'type' => 'text',
          'label' => 'Hero Title'
        ],
        [
          'name' => 'description',
          'type' => 'text',
          'label' => 'Hero banner description'
        ]
      ]
    ]
  ],
  'languages' => [
    'en' => 'English',
    'fr' => 'French',
    'ru' => 'Russian',
  ]
];