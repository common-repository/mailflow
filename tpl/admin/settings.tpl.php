<div class="wrap mailflow-admin-container">
    <?php if (isset($this->error) && $this->error != ''): ?>
        <div class="error below-h2">
            <p><strong>ERROR</strong>: <?php echo $this->error; ?></p>	
        </div>
    <?php endif; ?>
    <h1><?php echo __('Connect your Mailflow account', 'mailflow'); ?></h1>
    <p><?php echo __('You will find the API details you need for this section within the <a href="https://mailflow.com/account/api">API section</a> of your account', 'mailflow'); ?></p>
    <p><?php echo __("Mailflow works by assigning tags and attributes to email addresses, connecting your account means this information can be passed from your website to your Mailflow account. This is done using our secure API, the API is free to use and every Mailflow account has a unique Key and Secret which should be entered in the corresponding fields on this page.", 'mailflow'); ?></p>
    <?php if($this->success && $this->success != ""): ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
        <p><?php echo $this->success; ?></p>
    </div>
    <?php endif; ?>
    <form action="" method="post">
        <input type="hidden" name="mailflow_form_action" value="update_mailflow_settings" />
        <p><label for="mailflow-setting-apikey"><strong>API Key</strong></label></p>
        <p><input type="text" name="mailflow-api-key" value="<?php echo $this->api_key; ?>"/></p>

        <p><label for="mailflow-setting-secret"><strong>API Secret</strong></label></p>
        <p><input type="text" name="mailflow-api-secret" value="<?php echo $this->api_secret; ?>" /></p>

        <p><input type="submit"  class="button button-primary" value="Save"/></p>
    </form>
</div>