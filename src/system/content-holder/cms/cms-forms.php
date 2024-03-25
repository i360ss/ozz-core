<?php
/**
* Ozz CMS forms
*/

use Cms\controller\CMSAdminController;

return [
  'contact-us' => [
    'label' => 'Contact Us',
    'note' => 'Default Contact form',
    // 'action' => '/form/submit',
    // 'callback' => [CMSAdminController::class, 'submit1'],
    'method' => 'post',
    'fields' => [
      [
        'name' => 'name',
        'type' => 'text',
        'label' => 'Your Name'
      ],
      [
        'name' => 'email',
        'type' => 'email',
        'label' => 'Email Address'
      ],
      [
        'name' => 'subject',
        'type' => 'text',
        'label' => 'Subject'
      ],
      [
        'name' => 'message',
        'type' => 'textarea',
        'label' => 'Message'
      ],
      [
        'name' => 'submit',
        'type' => 'submit',
        'value' => 'Send',
        'class' => 'button small'
      ]
    ],
    'table-fields' => [
      'name',
      'email',
      'subject',
      'message',
      'created'
    ]
  ],
  'checkout' => [
    'label' => 'Checkout',
    'class' => 'ozz-fm',
    'entry-status' => [
      1 => 'Pending',
      2 => 'Processing',
      3 => 'Shipped',
      4 => 'Delivered',
      5 => 'Cancelled',
      6 => 'Refunded',
      7 => 'Returned',
      8 => 'Completed',
      9 => 'On Hold',
      10 => 'Overdue',
      11 => 'Spam'
    ],
    'fields' => [
      [
        'name' => 'f-name',
        'type' => 'text',
        'label' => 'First Name',
        'validate' => 'req'
      ],
      [
        'name' => 'l-name',
        'type' => 'text',
        'label' => 'Last Name',
        'validate' => 'req'
      ],
      [
        'name' => 'address',
        'type' => 'text',
        'label' => 'Address',
        'validate' => 'req'
      ],
      [
        'name' => 'address-2',
        'type' => 'text',
        'label' => 'Address Line Two'
      ],
      [
        'name' => 'email',
        'type' => 'email',
        'label' => 'Email',
        'validate' => 'req|email'
      ],
      [
        'name' => 'phone',
        'type' => 'text',
        'label' => 'Contact Number',
        'validate' => 'req'
      ],
      [
        'name' => 'country',
        'type' => 'select',
        'label' => 'Country',
        'options' => [
          'lk' => 'Sri Lanka',
          'us' => 'USA',
          'uk' => 'United Kingdom',
        ]
      ],
      [
        'name' => 'state',
        'type' => 'text',
        'label' => 'State'
      ],
      [
        'name' => 'zip-code',
        'type' => 'number',
        'label' => 'Zip Code'
      ]
    ]
  ]
];
