<?php
/**
 * Ozz CMS configuration
 */

return [
  'post_types' => [
    'pages' => [
      'label' => 'Pages',
      'note' => 'This post type can be used to create pages',
      'tabs' => [
        'seo' => [
          'slug' => 'seo',
          'label' => 'SEO',
          'fields' => [
            [
              'name' => 'meta_title',
              'type' => 'text',
              'label' => 'Title',
            ],
            [
              'name' => 'meta_description',
              'type' => 'textarea',
              'label' => 'Meta Description',
            ],
            [
              'name' => 'meta_keywords',
              'type' => 'text',
              'label' => 'Meta Keywords',
              'note' => 'Separate each keyword by commas (,)'
            ]
          ]
        ]
      ]
    ],
    'blog' => [
      'label' => 'Blogs',
      'singular_label' => 'Blog',
      'note' => 'This post type can be used to create blog posts',
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
    ],
    'testimonials' => [
      'label' => 'Testimonials',
      'singular_label' => 'Testimonial',
      'note' => 'Customer testimonial posts',
      'fields' => [
        [
          'name' => 'customer_name',
          'type' => 'text',
          'label' => 'Customer Name',
          'validate' => 'req'
        ],
        [
          'name' => 'customer_company',
          'type' => 'text',
          'label' => 'Customer Company',
          'wrapper_class' => 'cl cl-6'
        ],
        [
          'name' => 'customer_designation',
          'type' => 'text',
          'label' => 'Customer Designation',
          'wrapper_class' => 'cl cl-6'
        ],
        [
          'name' => 'customer_photo',
          'type' => 'media',
          'label' => 'Customer Photo',
        ],
        [
          'name' => 'review',
          'type' => 'textarea',
          'label' => 'Review',
          'validate' => 'req'
        ],
      ]
    ],
    'team_members' => [
      'label' => 'Team Members',
      'singular_label' => 'Member',
      'note' => 'This post type can be used to create team members',
      'fields' => [
        [
          'name' => 'name',
          'type' => 'text',
          'label' => 'Name'
        ],
        [
          'name' => 'designation',
          'type' => 'text',
          'label' => 'Designation'
        ],
        [
          'name' => 'bio',
          'type' => 'textarea',
          'label' => 'Bio'
        ],
        [
          'name' => 'photo',
          'type' => 'media',
          'label' => 'Photo'
        ],
        [
          'name' => 'social_links',
          'type' => 'repeater',
          'label' => 'Social Media Links',
          'fields' => [
            [
              'name' => 'platform',
              'type' => 'text',
              'label' => 'Social media platform',
              'wrapper_class' => 'cl cl-5'
            ],
            [
              'name' => 'link',
              'type' => 'text',
              'label' => 'Link (URL)',
              'wrapper_class' => 'cl cl-6'
            ]
          ]
        ]
      ]
    ],
    'faq' => [
      'label' => 'FAQs',
      'singular_label' => 'FAQ',
      'note' => 'Frequently asked questions',
      'fields' => [
        [
          'name' => 'question',
          'type' => 'text',
          'label' => 'Question'
        ],
        [
          'name' => 'answer',
          'type' => 'textarea',
          'label' => 'Answer'
        ],
        [
          'name' => 'category',
          'type' => 'select',
          'label' => 'Category',
          'options' => [
            'General',
            'Billing',
          ]
        ]
      ]
    ]
  ],
  'blocks' => [
    [
      'name' => 'rich-text',
      'label' => 'Rich Text',
      'note' => 'Default rich-text block',
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
      'name' => 'hero-banner',
      'label' => 'Hero Banner',
      'note' => 'Simple banner section',
      'form' => [
        'fields' => [
          [
            'name' => 'image',
            'type' => 'media',
            'label' => 'Banner Image'
          ],
          [
            'name' => 'title',
            'type' => 'text',
            'label' => 'Main Title',
            'wrapper_class' => 'cl cl-8',
            'validate' => 'req'
          ],
          [
            'name' => 'highlight_text',
            'type' => 'text',
            'label' => 'Highlight Text',
            'wrapper_class' => 'cl cl-4'
          ],
          [
            'name' => 'sub_title',
            'type' => 'text',
            'label' => 'Sub Title',
          ],
          [
            'name' => 'eyebrow_text',
            'type' => 'text',
            'label' => 'Eyebrow Text',
          ],
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Description',
          ],
          [
            'name' => 'cta_label',
            'type' => 'text',
            'label' => 'CTA Label',
            'wrapper_class' => 'cl cl-6'
          ],
          [
            'name' => 'cta_link',
            'type' => 'text',
            'label' => 'CTA Link (URL)',
            'wrapper_class' => 'cl cl-6'
          ],
        ]
      ]
    ],
    [
      'name' => 'cta',
      'label' => 'CTA',
      'note' => 'Simple call to action section',
      'form' => [
        'fields' => [
          [
            'name' => 'image',
            'type' => 'media',
            'label' => 'CTA Image'
          ],
          [
            'name' => 'title',
            'type' => 'text',
            'label' => 'Main Title',
          ],
          [
            'name' => 'sub_title',
            'type' => 'text',
            'label' => 'Sub Title',
          ],
          [
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Description',
          ],
          [
            'name' => 'cta_label',
            'type' => 'text',
            'label' => 'CTA Label',
            'wrapper_class' => 'cl cl-6'
          ],
          [
            'name' => 'cta_link',
            'type' => 'text',
            'label' => 'CTA Link (URL)',
            'wrapper_class' => 'cl cl-6'
          ],
        ]
      ]
    ],
    [
      'name' => 'accordion',
      'label' => 'Accordion',
      'note' => 'Default accordion block',
      'form' => [
        'fields' => [
          [
            'name' => 'items',
            'label' => 'Accordion Items',
            'type' => 'repeater',
            'fields' => [
              [
                'name' => 'title',
                'type' => 'text',
                'label' => 'Item Title',
              ],
              [
                'name' => 'body',
                'type' => 'textarea',
                'label' => 'Item Body Content',
              ]
            ]
          ]
        ]
      ]
    ],
    [
      'name' => 'call-component',
      'label' => 'Call Comp',
      'note' => 'Render a code based component',
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