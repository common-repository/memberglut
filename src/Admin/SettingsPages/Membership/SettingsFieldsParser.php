<?php
// 

namespace MemberGlut\Core\Admin\SettingsPages\Membership;


class SettingsFieldsParser
{
    protected $config;
    protected $dbData;
    protected $field_class;

    public function __construct($config, $dbData = [], $field_class = 'mglut-plan-control')
    {
        $this->config      = $config;
        $this->dbData      = $dbData;
        $this->field_class = $field_class;
    }

    protected function field_output($config)
    {
        $field_id    = sanitize_text_field($config['id']);
        $placeholder = esc_attr(mglut_var($config, 'placeholder', ''));

        $field_data = isset($_POST[$field_id]) && isset($_POST['mglut_form_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mglut_form_nonce'], 'mglut_form_nonce'))) ? sanitize_text_field($_POST[$field_id]) : (isset($this->dbData->$field_id) ? $this->dbData->$field_id : '');

        switch ($config['type']) {
            case 'text':
                 /* translators: %s is a placeholder */                   
                printf('<input placeholder="%4$s" class="%3$s" name="%1$s" id="%1$s" type="text" value="%2$s">', esc_attr($field_id), esc_attr($field_data), esc_attr($this->field_class), esc_attr($placeholder));
                break;
            case 'number':
                /* translators: %s is a placeholder */
                printf('<input placeholder="%4$s" class="%3$s" name="%1$s" id="%1$s" type="number" value="%2$s">', esc_attr($field_id), esc_attr($field_data), esc_attr($this->field_class), esc_attr($placeholder));
                break;
            case 'price':
                /* translators: %s is a placeholder */
                printf('<input class="%3$s" step="any" placeholder="0.00" name="%1$s" id="%1$s" type="number" value="%2$s">', esc_attr($field_id), esc_attr($field_data), esc_attr($this->field_class));
                break;
            case 'textarea':
                /* translators: %s is a placeholder */
                printf('<textarea class="%3$s" name="%1$s" id="%1$s">%2$s</textarea>', esc_attr($field_id), esc_attr($field_data), esc_attr($this->field_class));
                break;

            case 'wp_editor':
                // Remove all TinyMCE plugins.
                remove_all_filters('mce_buttons', 10);
                remove_all_filters('mce_external_plugins', 10);
                remove_all_actions('media_buttons');
                // add core media button back.
                add_action('media_buttons', 'media_buttons');

                wp_editor(wp_kses_post($field_data), sanitize_text_field($field_id), ["editor_height" => 100, 'editor_class' => 'mglut-plan-control']);
                break;
            case 'select':
                if (is_array($config['options']) && ! empty($config['options'])) {
                    $is_multiple = mglut_var($config, 'multiple') === true;
                    $name        = $is_multiple ? $config['id'] . '[]' : $config['id'];
                    printf('<select class="%2$s" name="%1$s" id="%3$s"%4$s>', esc_attr($name), esc_attr($this->field_class), esc_attr($config['id']), $is_multiple ? ' multiple' : '');
                    foreach ($config['options'] as $option_id => $option_name) {
                        if (is_array($field_data) || mglut_var($config, 'multiple') === true) {
                            $field_data = is_array($field_data) ? $field_data : [];
                            $selected   = in_array($option_id, $field_data) ? 'selected' : '';
                        } else {
                            $selected = selected($option_id, $field_data, false);
                        }
                        /* translators: %s is a placeholder */
                        printf('<option value="%1$s" %3$s>%2$s</option>', esc_attr($option_id), esc_attr($option_name), esc_attr($selected));
                    }
                    echo '</select>';
                }
                break;
            case 'select2':
                if (is_array($config['options']) && ! empty($config['options'])) {
                    $is_multiple = mglut_var($config, 'multiple') === true;
                    $name        = $is_multiple ? $config['id'] . '[]' : $config['id'];
                    /* translators: %s is a placeholder */
                    printf('<select class="ppselect2 %2$s" name="%1$s[]" id="%3$s" multiple>', esc_attr($name), esc_attr($this->field_class), esc_attr($config['id']));
                    foreach ($config['options'] as $option_id => $option_name) {
                        if (is_array($field_data) || mglut_var($config, 'multiple') === true) {
                            $selected = in_array($option_id, $field_data) ? 'selected' : '';
                        } else {
                            $selected = selected($option_id, $field_data, false);
                        }
                    /* translators: %s is a placeholder */
                        printf('<option value="%1$s" %3$s>%2$s</option>', esc_attr($option_id), esc_attr($option_name), esc_attr($selected));
                    }
                    echo '</select>';
                }
                break;
            case 'radio':
                if (is_array($config['options']) && ! empty($config['options'])) {
                    foreach ($config['options'] as $option_id => $option_name) {
                        /* translators: %s is a placeholder */
                        printf('<label><input type="radio" name="%4$s" value="%1$s" %3$s>%2$s</label>', esc_attr($option_id), esc_attr($option_name), checked($option_id, $field_data, false), esc_attr($field_id));
                    }
                }
                break;
            case 'checkbox':
                $checkbox_label = esc_html(mglut_var($config, 'checkbox_label', '', true));
                /* translators: %s is a placeholder */
                printf('<input type="hidden" name="%1$s" value="false">', esc_attr($field_id));
                printf('<label><input type="checkbox" name="%1$s" value="true"%2$s>%3$s</label>', esc_attr($field_id), checked('true', $field_data, false), esc_html($checkbox_label));
                break;
        }
    
    }

    public function build()
    {
    
        wp_nonce_field('mglut_form_nonce', 'mglut_form_nonce');
        ?>
        <table class="form-table">
            <tbody>
            <?php foreach ($this->config as $config) : ?>
                <tr class="form-field" id="field-role-<?php echo esc_attr($config['id']) ?>">
                    <th scope="row" valign="top">
                        <label for="<?php  echo esc_attr($config['id']) ?>"><?php echo esc_html($config['label']) ?></label>
                    </th>
                    <td>
                        <?php $this->field_output($config); ?>
                        <?php if ( ! empty($config['description'])) : ?>
                            <p class="description"><?php echo esc_attr($config['description']); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
}