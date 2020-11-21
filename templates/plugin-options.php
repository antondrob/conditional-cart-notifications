<?php $message_id = get_the_ID(); ?>

<div class="ccn-page-wrap">
    <div class="ccn-message-content">

        <div class="ccn-message-options-wrap">

            <div class="ccn-message-option">
                <div class="ccn-message-option__title">
                    <p>Active</p>
                </div>
                <div class="ccn-message-option__content">
                    <div class="activate-group">
                        <?php
                        if (get_post_meta($message_id, 'ccn_message_active', true)) :
                            $ccn_message_active = get_post_meta($message_id, 'ccn_message_active', true);
                        else :
                            $$ccn_message_active = 1;
                        endif;
                        ?>
                        <input type="checkbox" name="ccn_message_active" value="1" <?php checked('1', $ccn_message_active); ?> class="activate-switch">
                        <div class="activate-tip">Click to <span>activate</span><span>disable</span></div>
                    </div>
                </div>
            </div>

            <div class="ccn-message-option">
                <div class="ccn-message-option__title">
                    <p>Message Type</p>
                </div>
                <div class="ccn-message-option__content">
                    <?php
                    if (get_post_meta($message_id, 'ccn_message_type', true)) {
                        $ccn_message_type = get_post_meta($message_id, 'ccn_message_type', true);
                    } else {
                        $ccn_message_type = 'simple_message';
                    }
                    ?>
                    <select name="ccn_message_type" class="js-toggle-hidden">
                        <?php
                        $types = $this->get_types();
                        foreach ($types as $key => $value) {
                            if (strstr($value['title'], '(PRO)')) {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            if ($key == $ccn_message_type) {
                                $checked = 'selected';
                            } else {
                                $checked = '';
                            }
                            echo '<option value="' . $key . '" ' . $disabled . ' ' . $checked . '>' . $value['title'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="ccn-message-hidden-block">
                <?php
                if ($ccn_message_type != 'minimum_amount') :
                    $hidden_class = 'hidden-class';
                else :
                    $hidden_class = '';
                endif;
                ?>

                <div class="ccn-message-option  <?= $hidden_class; ?>" data-layout="minimum_amount">
                    
                    <div class="ccn-message-option__title">
                        <p>Free shipping threshold order amount</p>
                    </div>
                    <div class="ccn-message-option__content">
                        <?php $ccn_free_shipping_threshold_order_amount = number_format( $this->free_shipping_threshold_order_amount(), 2); ?>
                        <input type="number" name="ccn_free_shipping_threshold_order_amount" step="0.01" min="0" value="<?= $ccn_free_shipping_threshold_order_amount; ?>">
                    </div>

                </div>

                <div class="ccn-message-option  <?= $hidden_class; ?>" data-layout="minimum_amount">
                    <div class="ccn-message-option__title">
                        <p>Minimum order amount</p>
                    </div>
                    <?php
                    if (get_post_meta($message_id, 'minimum_order_amount', true)) {
                        $minimum_order_amount = get_post_meta($message_id, 'minimum_order_amount', true);
                    } else {
                        $minimum_order_amount = '';
                    }
                    ?>
                    <div class="ccn-message-option__content">
                        <input type="number" name="minimum_order_amount" value="<?= number_format( $minimum_order_amount, 2 ); ?>">
                    </div>
                </div>

                <div class="ccn-message-option">
                    <div class="ccn-message-option__title">
                        <p>Message Header</p>
                    </div>
                    <div class="ccn-message-option__content">
                        <?php
                        if (get_post_meta($message_id, 'ccn_message_header', true)) {
                            $ccn_message_header = get_post_meta($message_id, 'ccn_message_header', true);
                        } else {
                            $ccn_message_header = '';
                        }
                        ?>
                        <input type="text" name="ccn_message_header" value="<?= $ccn_message_header; ?>">
                        <?php
                        if (get_post_meta($message_id, 'ccn_message_header_tag', true)) {
                            $ccn_message_header_tag = get_post_meta($message_id, 'ccn_message_header_tag', true);
                        } else {
                            $ccn_message_header_tag = '';
                        }
                        ?>
                        <select name="ccn_message_header_tag">
                            <option value="h1" <?php if ($ccn_message_header_tag == 'h1') {
                                                    echo 'selected';
                                                } ?>>H1</option>
                            <option value="h2" <?php if ($ccn_message_header_tag == 'h2') {
                                                    echo 'selected';
                                                } ?>>H2</option>
                            <option value="h3" <?php if ($ccn_message_header_tag == 'h3') {
                                                    echo 'selected';
                                                } ?>>H3</option>
                            <option value="h4" <?php if ($ccn_message_header_tag == 'h4') {
                                                    echo 'selected';
                                                } ?>>H4</option>
                            <option value="h5" <?php if ($ccn_message_header_tag == 'h5') {
                                                    echo 'selected';
                                                } ?>>H5</option>
                            <option value="h6" <?php if ($ccn_message_header_tag == 'h6') {
                                                    echo 'selected';
                                                } ?>>H6</option>
                        </select>
                    </div>
                </div>

                <div class="ccn-message-option">
                    <div class="ccn-message-option__title">
                        <p>Message Text</p>
                        <p class="message-type-notice"><?= $types[$ccn_message_type]['notice']; ?></p>
                    </div>
                    <div class="ccn-message-option__content">
                        <?php

                        if (get_post_meta($message_id, 'ccn_message_text', true)) {
                            $message_text = get_post_meta($message_id, 'ccn_message_text', true);
                        } else {
                            $message_text = '';
                        }

                        wp_editor($message_text, 'simplemessageeditor', array(
                            'wpautop'       => 1,
                            'media_buttons' => 0,
                            'textarea_name' => 'ccn_message_text',
                            'textarea_rows' => 8,
                            'tabindex'      => null,
                            'editor_css'    => '',
                            'editor_class'  => '',
                            'teeny'         => 0,
                            'dfw'           => 0,
                            'tinymce'       => 1,
                            'quicktags'     => 1,
                            'drag_drop_upload' => false
                        ));
                        ?>
                    </div>
                </div>

                <div class="ccn-message-option">
                    <div class="ccn-message-option__title">
                        <p>Button text</p>
                    </div>
                    <div class="ccn-message-option__content">
                        <?php
                        if (get_post_meta($message_id, 'ccn_button_text', true)) {
                            $button_text = get_post_meta($message_id, 'ccn_button_text', true);
                        } else {
                            $button_text = '';
                        }
                        ?>
                        <input type="text" name="ccn_button_text" value="<?= $button_text; ?>">
                    </div>
                </div>

                <div class="ccn-message-option">
                    <div class="ccn-message-option__title">
                        <p>Button URL</p>
                    </div>
                    <div class="ccn-message-option__content">
                        <?php
                        if (get_post_meta($message_id, 'ccn_button_url', true)) {
                            $button_url = get_post_meta($message_id, 'ccn_button_url', true);
                        } else {
                            $button_url = '';
                        }
                        ?>
                        <input type="text" name="ccn_button_url" value="<?= $button_url; ?>">
                        <label>
                            <?php
                            if (get_post_meta($message_id, 'ccn_new_tab', true)) :
                                $ccn_new_tab = get_post_meta($message_id, 'ccn_new_tab', true);
                            else :
                                $$ccn_new_tab = 1;
                            endif;
                            ?>
                            <input type="checkbox" name="ccn_new_tab" value="1" <?php checked('1', $ccn_new_tab); ?>>
                            Open in New Window
                        </label>
                    </div>
                </div>

                <?php
                if (get_post_meta($message_id, 'ccn_message_layout', true)) :
                    $ccn_message_layout = get_post_meta($message_id, 'ccn_message_layout', true);
                else :
                    $ccn_message_layout = '';
                endif;
                ?>
                <div class="ccn-message-option">
                    <div class="ccn-message-option__title">
                        <p>Message Layout</p>
                    </div>
                    <div class="ccn-message-option__content">
                        <select name="ccn_message_layout">
                            <option value="information_layout" data-color_1="#fff" data-color_2="#99baef" data-color_3="#99baef" data-color_4="#0073aa" data-color_5="#000" data-color_6="#fff" <?php if ($ccn_message_layout == 'information_layout') { echo 'selected'; } ?>>Information layout</option>
                            <option value="warning_layout" data-color_1="#bce5b1" data-color_2="#197112" data-color_3="#00ff1a" data-color_4="#26ad0b" data-color_5="#000" data-color_6="#fff" <?php if ($ccn_message_layout == 'warning_layout') { echo 'selected'; } ?>>Warning layout</option>
                            <option value="" disabled>Add New Layout (PRO)</option>
                        </select>
                    </div>
                </div>

                <div class="ccn-message-opacity-block">
                    <div class="pro-overlay"></div>
                    <div class="ccn-message-option">
                        <div class="message-layout-option-wrap ccn-border">
                            <div class="ccn-border__label">Basic Layout / Layout Editor (PRO)</div>
                            <div class="layout-options">
                                <?php

                                if (get_post_meta($message_id, 'ccn_message_layout', true)) :
                                    $layout = get_post_meta($message_id, 'ccn_message_layout', true);
                                else :
                                    $layout = 'information_layout';
                                endif;

                                if ($layout) {
                                    $layout_colors = $this->layout_colors($layout);
                                } else {
                                    $layout_colors = 'information_layout';
                                }

                                $ccn_layout_box_border_color = $layout_colors['ccn_layout_box_border_color'];
                                $ccn_layout_box_background_color = $layout_colors['ccn_layout_box_background_color'];
                                $ccn_layout_box_text_color = $layout_colors['ccn_layout_box_text_color'];
                                $ccn_layout_button_background_color = $layout_colors['ccn_layout_button_background_color'];
                                $ccn_layout_button_text_color = $layout_colors['ccn_layout_button_text_color'];
                                $ccn_layout_button_background_color_on_hover = $layout_colors['ccn_layout_button_background_color_on_hover'];
                                ?>

                                <div class="layout-option">
                                    <p class="layout-option__title">Box background color</p>
                                    <input name="ccn_layout_box_background_color" type="text" value="<?= $ccn_layout_box_background_color; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-option">
                                    <p class="layout-option__title">Button background color</p>
                                    <input name="ccn_layout_button_background_color" type="text" value="<?= $ccn_layout_box_border_color; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-option">
                                    <p class="layout-option__title">Box border color</p>
                                    <input name="ccn_layout_box_border_color" type="text" value="<?= $ccn_layout_box_border_color; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-option">
                                    <p class="layout-option__title">Button background color on hover</p>
                                    <input name="ccn_layout_button_background_color_on_hover" type="text" value="<?= $ccn_layout_button_background_color_on_hover; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-option">
                                    <p class="layout-option__title">Box text color</p>
                                    <input name="ccn_layout_box_text_color" type="text" value="<?= $ccn_layout_box_text_color; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-option">
                                    <p class="layout-option__title">Button text color</p>
                                    <input name="ccn_layout_button_text_color" type="text" value="<?= $ccn_layout_button_text_color; ?>" class="layout-option__color colorpicker" />
                                </div>
                                <div class="layout-options__footer ccn-buttons">
                                    <a href="#" class="ccn-button save-new-layout">Save as a New Layout</a>
                                    <a href="#" class="ccn-button update-layout">Save</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ccn-message-option">
                        <div class="ccn-message-option__title">
                            <p>Show to (PRO)</p>
                        </div>
                        <div class="ccn-message-option__content">
                            <select name="show_to">
                                <option value="guests">Guests (PRO)</option>
                                <option value="logged_users">Logged Users (PRO)</option>
                            </select>
                        </div>
                    </div>

                    <div class="ccn-message-option">
                        <div class="ccn-message-option__title">
                            <p>Start and Ex. Date (PRO)</p>
                        </div>
                        <div class="ccn-message-option__content">
                            <div class="message-dates">
                                <div class="message-dates-headers">
                                    <a href="#" class="message-dates-header active">Start</a>
                                    <a href="#" class="message-dates-header">Expiration</a>
                                </div>
                                <div class="message-date active">
                                    Date:
                                    <input type="text" class="datepicker" name="message_start">
                                    Time:
                                    <input type="text" class="clockpicker" name="message_start_time">
                                </div>
                                <div class="message-date">
                                    Date:
                                    <input type="text" class="datepicker" name="message_expiration">
                                    Time:
                                    <input type="text" class="clockpicker" name="message_expiration_time">
                                </div>
                            </div>
                            <div class="exp-"></div>
                        </div>
                    </div>

                    <div class="ccn-message-option">
                        <div class="ccn-message-option__title">
                            <p>Show in (PRO)</p>
                        </div>
                        <div class="ccn-message-option__content">
                            <select name="show_in">
                                <option value="cart">Cart (PRO)</option>
                                <option value="checkout">Checkout (PRO)</option>
                                <option value="single_product">Single Product Page (PRO)</option>
                            </select>
                        </div>
                    </div>

                    <div class="ccn-message-option">
                        <div class="ccn-message-option__title">
                            <p>Message Position (PRO)</p>
                        </div>
                        <div class="ccn-message-option__content">
                            <select name="message_position">
                                <option value="before_cart_content">Before Cart Content</option>
                                <option value="before_cart_table">Before Cart Table</option>
                                <option value="after_cart">After Cart</option>
                                <option value="after_cart_content">After Cart Content</option>
                                <option value="after_cart_table">After Cart Table</option>
                            </select>
                        </div>
                    </div>

                    <div class="ccn-message-option">
                        <div class="ccn-border">
                            <div class="ccn-border__label">Conditional Hide/Show (PRO)</div>
                            <div class="hide-show-message-wrap">
                                <div class="hide-show-message-row">
                                    <p>Cart match </p>
                                    <select name="hide_show_all_any" class="ccn-select-inline">
                                        <option value="all">All</option>
                                        <option value="any">Any</option>
                                    </select>
                                    <p> of the following conditions:</p>
                                </div>
                                <div class="hide-show-message-row">
                                    <select name="hide_show_option_1">
                                        <option value="cart_value">Cart Value</option>
                                        <option value="product_in_cart">Product in the Cart</option>
                                        <option value="cart_shipping">Cart Shipping</option>
                                        <option value="cart_user">Cart User</option>
                                    </select>
                                    <select name="hide_show_option_2">
                                        <option value="is_not">is not</option>
                                        <option value="is_greater_than">is greater than</option>
                                        <option value="is_less_than">is less than</option>
                                        <option value="is_equal">is equal</option>
                                    </select>
                                    <input type="text" name="option_3">
                                    <a href="#" class="add-more-option ccn-button">+ Add More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ccn-buttons">
    <?= 
        '<a href="' . wp_nonce_url('admin.php?action=duplicate_ccn_message_as_draft&post=' . $message_id, $this->plugin_nonce_name(), 'duplicate_nonce' ) . '" class="ccn-button duplicate-page" title="Duplicate this item" rel="permalink">Duplicate</a>'; 
    ?>
        <a href="#" class="ccn-button save-page">Save</a>
    </div>
</div>
<?php
if (get_post_meta($message_id, 'ccn_message_header', true)) :
    $ccn_message_header = get_post_meta($message_id, 'ccn_message_header', true);
else :
    $ccn_message_header = 'Message header';
endif;

if (get_post_meta($message_id, 'ccn_message_header_tag', true)) :
    $ccn_message_header_tag = get_post_meta($message_id, 'ccn_message_header_tag', true);
else :
    $ccn_message_header_tag = 'h2';
endif;

if (get_post_meta($message_id, 'ccn_message_text', true)) :
    $ccn_message_text = get_post_meta($message_id, 'ccn_message_text', true);
else :
    $ccn_message_text = 'Message text';
endif;

if (get_post_meta($message_id, 'ccn_button_url', true)) :
    $ccn_button_url = get_post_meta($message_id, 'ccn_button_url', true);
else :
    $ccn_button_url = '#';
endif;

if (get_post_meta($message_id, 'ccn_button_text', true)) :
    $ccn_button_text = get_post_meta($message_id, 'ccn_button_text', true);
else :
    $ccn_button_text = 'Learn more';
endif;

if ($ccn_new_tab == 1) {
    $ccn_target_blank = 'target="_blank"';
} else {
    $ccn_target_blank = '';
}
?>

<div class="ccn-message-preview ccn-border">
    <h3 class="ccn-message-preview__label ccn-border__label">Message preview (Please save to see a preview)</h3>
    <div class="ccn-message-preview__content" style="border-color: <?= $ccn_layout_box_border_color ?>; background-color: <?= $ccn_layout_box_background_color; ?>">
        <<?= $ccn_message_header_tag; ?> class="ccn-message-preview__title" style="color: <?= $ccn_layout_box_text_color ?>;"><?= $ccn_message_header; ?></<?= $ccn_message_header_tag; ?>>
        <div class="ccn-message-preview__text" style="color: <?= $ccn_layout_box_text_color ?>;">
            <?= str_replace('{remaining_amount}', get_woocommerce_currency_symbol() . number_format( $this->free_shipping_threshold_order_amount(), 2), $ccn_message_text); ?>
        </div>
        <a <?= $ccn_target_blank; ?> href="<?= $ccn_button_url; ?>" class="ccn-message-preview__button" style="background-color: <?= $ccn_layout_button_background_color ?>; color: <?= $ccn_layout_button_text_color ?>;" onMouseOver="this.style.backgroundColor='<?= $ccn_layout_button_background_color_on_hover; ?>'" onMouseOut="this.style.backgroundColor='<?= $ccn_layout_button_background_color; ?>'"><?= $ccn_button_text; ?></a>
    </div>
</div>