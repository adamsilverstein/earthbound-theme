<?php
/**
 * Title: Contact Section
 * Slug: earthbound/contact-section
 * Categories: earthbound
 * Keywords: contact, form, email, get in touch
 * Description: A contact section with an introduction and a contact form.
 *
 * @package Earthbound
 */

?>
<!-- wp:group {"metadata":{"name":"Contact"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group">

    <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.15rem","lineHeight":"1.7"},"spacing":{"margin":{"bottom":"2.5rem"}}}} -->
    <p style="margin-bottom:2.5rem;font-size:1.15rem;line-height:1.7"><?php echo esc_html__('Questions, ideas, or work you would like to talk through? Send a note using the form below and it will land in my inbox.', 'earthbound'); ?></p>
    <!-- /wp:paragraph -->

    <!-- wp:separator {"className":"is-style-newspaper-rule","style":{"border":{"top":{"width":"3px"},"bottom":{"width":"1px"}}},"backgroundColor":"foreground"} -->
    <hr class="wp-block-separator is-style-newspaper-rule has-foreground-background-color has-background" style="border-top-width:3px;border-bottom-width:1px"/>
    <!-- /wp:separator -->

    <!-- wp:spacer {"height":"2.5rem"} -->
    <div style="height:2.5rem" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:earthbound/contact-form /-->

</div>
<!-- /wp:group -->
