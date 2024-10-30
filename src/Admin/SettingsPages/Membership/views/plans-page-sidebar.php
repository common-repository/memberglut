<?php
// 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use MemberGlut\Core\Admin\SettingsPages\Membership\PlansPage\PlanWPListTable;

?>
<div class="submitbox" id="submitpost">

    <div id="major-publishing-actions">
        <div id="delete-action">
            <?php if (isset($_GET['mglut_subp_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['mglut_subp_action'])), 'appglut_memberglut_nonce') && sanitize_text_field($_GET['mglut_subp_action']) == 'edit') : ?>
                <a class="submitdelete deletion pp-confirm-delete" href="#"> 
                    <?php echo esc_html__('Delete', 'memberglut') ?>
                </a>
            <?php endif; ?>
        </div>

        <div id="publishing-action">
            <input type="submit" name="mglut_save_subscription_plan" class="button button-primary button-large" value="<?php echo esc_html__('Save Plan', 'memberglut') ?>">
        </div>
        <div class="clear"></div>
    </div>

</div>