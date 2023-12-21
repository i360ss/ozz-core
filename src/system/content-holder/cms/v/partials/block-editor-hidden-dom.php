<?php
/**
 * Block editor hidden dom (load form DOMs of blocks)
 */

if (isset($data['blocks_forms'])) : ?>
<section class="ozz-block-editor-hidden-form-dom">
  <div class="container">
    <?php foreach ($data['blocks_forms'] as $key => $block_form) :
      echo $block_form;
    endforeach; ?>
  </div>
</section>
<?php endif; ?>