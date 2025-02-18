<div class="wrap mailflow-admin-container">
    <?php if (isset($this->error) && $this->error != ''): ?>
        <div class="error below-h2">
            <p><strong>ERROR</strong>: <?php echo $this->error; ?></p>	
        </div>
    <?php endif; ?>
    <h1><?php echo __('Trigger an email on page visit', 'mailflow'); ?></h1>
    <p><?php echo __('Automatically trigger an email when a logged in member visits a page. Create email sequences specifically for when content is viewed, or change the emails you send your audience depending on the content they have viewed.', 'mailflow'); ?></p>
    <p><?php echo __("Add either new or existing tags. New tags will be automatically created within your Mailflow account. Existing tags can be viewed from within the Tag Manager in your your account. Select the page you want to assign the tags to from the drop down within the 'Pages' field.", 'mailflow'); ?></p>
    <?php if($this->success && $this->success != ""): ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
        <p><?php echo $this->success; ?></p>
    </div>
    <?php endif; ?>
    <?php if (isset($pages) && !empty($pages)): ?>
        <form action="" method="post">
            <input type="hidden" name="mailflow_form_action" value="update_tag_form" />
            <table class="widefat" id="mailflow_admin_tags_list">
                <thead>
                <th>Tags</th>
                <th>Pages</th>
                <th>Manage</th>
                </thead>
                <tbody>
                    <?php if(!empty($tagsArray)): ?>
                    <?php foreach($tagsArray as $tname => $ta): ?>
                    <?php $uniqid = uniqid(); ?>
                    <tr>
                        <td class="ce_editable"><input type="text" value="<?php echo $tname; ?>" name="mailflow-tags[<?php echo$uniqid; ?>][tag]" placeholder="tag name" /></td>
                        <td class="ce_editable">
                            <select class="mailflow-tags-pagelist-edit" multiple="multiple" name="mailflow-tags[<?php echo $uniqid; ?>][pages][]">
                                <?php foreach ($pages as $p): ?>
                                    <option value="<?php echo $p->ID; ?>" <?php echo (in_array($p->ID, $ta))? "selected='selected'" : ""?>><?php echo $p->post_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete" /></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php else: ?>
                        <?php $uniqid = uniqid(); ?>
                        <tr>
                            <td class="ce_editable"><input type="text" value="" name="mailflow-tags[<?php echo$uniqid; ?>][tag]" placeholder="tag name" /></td>
                            <td class="ce_editable">
                                <select class="mailflow-tags-pagelist-edit" multiple="multiple" name="mailflow-tags[<?php echo$uniqid; ?>][pages][]">
                                    <?php foreach ($pages as $p): ?>
                                        <option value="<?php echo $p->ID; ?>"><?php echo $p->post_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete" /></td>
                        </tr>                    
                    <?php endif; ?>
                </tbody>
            </table>
        <p><input type="button" class="button button-primary" value="Add new Field" id="mailflow-addnew-tag"></p>
        <p><input type="submit" value="Save" class="button button-primary" /></p>
        </form>

    <?php else: ?>
        <div id="error" class="updated error notice-error is-dismissible below-h2">
            <?php echo __('No pages exist. Please create a few pages first to use this function.', 'mailflow'); ?>
        </div>
    <?php endif; ?>

</div>
<table style="display: none" id="mailflow-tag-select-tpl">
    <tr>
        <td class="ce_editable"><input type="text" value="" class="mailflow-tag" name="mailflow-tags[]" placeholder="tag name" /></td>
        <td class="ce_editable">
            <select class="mailflow-tags-pagelist-edit-tpl" multiple="multiple" name="mailflow-tag-pagelist[]">
                <?php foreach ($pages as $p): ?>
                    <option value="<?php echo $p->ID; ?>"><?php echo $p->post_name; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="button" class="button button-primary mailflow-feild-delete-button" value="Delete" ></td>
    </tr>
</table>
