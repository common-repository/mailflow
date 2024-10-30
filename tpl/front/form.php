<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/*
 * This file will add extra fields to registration form
 */
?>
<form action="" method="post">
<?php $field_value = (!empty($_POST['mailflow-fields']['contact']) ) ? trim($_POST['mailflow-fields']['contact']) : ''; ?>
<?php if(isset($this->error) && $this->error != ''): ?>
    <p class="error"><?php echo $this->error; ?></p>
<?php endif; ?>
    
<?php if(isset($this->success) && $this->success != ''): ?>
    <p class="success"><?php echo $this->success; ?></p>
<?php endif; ?>
<input type="hidden" name="mailflow_form_action" value="front_form_post" />
<input type="hidden" name="mailflow_form_id" value="1" />
<p>
    <label for="mailflow-field-key-contact"><?php _e("Email", 'mailflow') ?><br />
        <input type="text" name="mailflow-fields[contact]" id="mailflow-field-key-contact" class="input" value="<?php echo ($this->success == '') ?  esc_attr(wp_unslash($field_value)):""; ?>" /></label>
</p>
<?php foreach ($fields as $f): ?>
    <?php $field_value = (!empty($_POST['mailflow-fields'][$f['key']]) ) ? trim($_POST['mailflow-fields'][$f['key']]) : ''; ?>
    <?php if ($f['type'] == 'text-box'): ?>
        <p>
            <label for="mailflow-field-key-<?php echo $f['key']; ?>"><?php _e($f['name'], 'mailflow') ?><br />
                <input type="text" name="mailflow-fields[<?php echo $f['key']; ?>]" id="mailflow-field-key-<?php echo $f['key']; ?>" class="input" value="<?php echo ($this->success == '') ? esc_attr(wp_unslash($field_value)) : ""; ?>" /></label>
        </p>
    <?php elseif ($f['type'] == 'text-area'): ?>
        <p>
            <label for="mailflow-field-key-<?php echo $f['key']; ?>"><?php _e($f['name'], 'mailflow') ?><br />
                <textarea  name="mailflow-fields[<?php echo $f['key']; ?>]" id="mailflow-field-key-<?php echo $f['key']; ?>" class="input"><?php echo ($this->success == '') ? esc_attr(wp_unslash($field_value)): ""; ?></textarea></label>
        </p>
    <?php endif; ?>
<?php endforeach; ?>
    <input type="submit" value="<?php echo (isset($form_messages['submit-text'])) ? $form_messages['submit-text'] : "Submit"; ?>" />
</form>
