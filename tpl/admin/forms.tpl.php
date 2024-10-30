<div class="wrap mailflow-admin-container">
    <?php if (isset($this->error) && $this->error != ''): ?>
        <div class="error below-h2">
            <p><strong>ERROR</strong>: <?php echo $this->error; ?></p>	
        </div>
    <?php endif; ?>
    <h1><?php echo __('Create a form to add new emails', 'mailflow'); ?></h1>
    <p><?php echo __('Add new contacts to your newsletter or collect requests for more information with this simple custom form.', 'mailflow'); ?></p>
    <p><?php echo __("This form can be created quickly and easily then added to any page or post using the shortcode [mailflow-form]. The 'Field name' value is the label applied to the text boxes in the form. A common use would be \"First Name\". The 'Attribute' is the name applied to that information within your Mailflow account. Attributes are used to personalise your emails, so for example you could use the \"First Name\" attribute to start an email with a contact's name.", 'mailflow'); ?></p>
    <?php if ($this->success && $this->success != ""): ?>
        <div id="message" class="updated notice notice-success is-dismissible below-h2">
            <p><?php echo $this->success; ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php endif; ?>
    <h2><?php echo __('Form Fields', 'mailflow'); ?></h2>
    <form method="post">
        <input type="hidden" name="mailflow_form_action" value="update_field_form" />
        <p><input type="checkbox" value="1" name="mailflow-doubleopt-form" <?php echo (isset($double_opt) && $double_opt == 1) ? "checked='checked'" : ""; ?>>Send a confirmation email to double opt in these contacts - this should always be done unless double opted in already</p>
        <table class="widefat" id="mailflow_admin_field_list">
            <thead>
            <th>Field name</th>
            <th>Attribute</th>
            <th>Field Type</th>
            <th>Manage</th>
            </thead>
            <tbody>
                <?php if (!empty($fields)): ?>
                    <?php foreach ($fields as $f): ?>
                        <tr class="mailflow-input-container field-1">
                            <td class="ce_editable"><input type="text" name="field[name][]"  value="<?php echo $f['name']; ?>" /></td>
                            <td class="ce_editable"><input type="text" name="field[key][]"  value="<?php echo $f['key']; ?>" /></td>
                            <td class="ce_editable">
                                <select name="field[type][]">
                                    <option <?php echo ($f['type'] == 'text-box') ? 'selected="selected"' : ""; ?> value="text-box">Text box</option>
                                    <option <?php echo ($f['type'] == 'text-area') ? 'selected="selected"' : ""; ?> value="text-area">Text area</option>
                                </select>
                            </td>
                            <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete"/></td>                
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="mailflow-input-container field-1">
                        <td class="ce_editable"><input type="text" name="field[name][]"  value="" /></td>
                        <td class="ce_editable"><input type="text" name="field[key][]"  value="" /></td>
                        <td class="ce_editable">
                            <select name="field[type][]">
                                <option value="text-box">Text box</option>
                                <option value="text-area">Text area</option>
                            </select>
                        </td>
                        <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete"/></td>                
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p><input type="button" class="button button-primary" id="mailflow-addnew-fields" value="Add new Field" /></p>
		<p></p>
        <h2><?php echo __('Customise form messaging', 'mailflow'); ?></h2>
		<p></p>
        <table>
            <tr>
                <td>Success message: </td><td><input type="text" name="mailflow-form-message[success-message]" size="64" value="<?php echo(isset($form_messages) && isset($form_messages['success-message'])) ? $form_messages['success-message'] : "" ?>"/></td></td>
            </tr>
            <tr>
                <td>Incorrect email error message: </td><td> <input type="text" name="mailflow-form-message[incorrect-email-message]" size="64" value="<?php echo(isset($form_messages) && isset($form_messages['incorrect-email-message'])) ? $form_messages['incorrect-email-message'] : "" ?>" /></td></td>
            </tr>
            <tr>
                <td>Submit button text: </td><td><input type="text" name="mailflow-form-message[submit-text]" size="64" value="<?php echo(isset($form_messages) && isset($form_messages['submit-text'])) ? $form_messages['submit-text'] : "" ?>" /></td></td>
            </tr>
        </table>
        
        <p></p>
        <h2><?php echo __('Add tags', 'mailflow'); ?></h2>
		<p>These tags will be assigned to every contact who is added through this form. New or existing tags can be used. New tags will be automatically created within your Mailflow account, existing tags can be viewed from within the <a href="https://mailflow.com/contacts/manage-tags">tag manager</a> in your Mailflow account.</p>
		<p></p>
        <select class="mailflow-form-tags" multiple="multiple" name="mailflow-form-tags[]">
            <?php if (isset($form_tags) && !empty($form_tags)): ?>
                <?php foreach ($form_tags as $t): ?>
                    <option selected="selected" value="<?php echo $t ?>"><?php echo $t ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <p><input type="submit" value="Save" class="button button-primary" /></p>
    </form>
    <table class="widefat" id="mailflow_admin_field_list_tpl" style="display: none;">
        <tbody>
            <tr class="mailflow-input-container field-1">
                <td class="ce_editable"><input type="text" name="field[name][]"  value="" /></td>
                <td class="ce_editable"><input type="text" name="field[key][]"  value="" /></td>
                <td class="ce_editable">
                    <select name="field[type][]">
                        <option value="text-box">Text box</option>
                        <option value="text-area">Text area</option>
                    </select>
                </td>
                <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete"/></td>                
            </tr>
        </tbody>
    </table>
</div>
