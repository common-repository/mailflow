<div class="wrap mailflow-admin-container">
    <?php if (isset($this->error) && $this->error != ''): ?>
        <div class="error below-h2">
            <p><strong>ERROR</strong>: <?php echo $this->error; ?></p>	
        </div>
    <?php endif; ?>
    <img src='https://mailflow.com/assets/mailflow-logo-green-800-200-2x-9dd797bd69948be1177af8985c3884c0.png' width="260" />
    <?php if ($this->api_key == '' || $this->api_secret == ''): ?>
        <h2>Stop struggling with email automation</h2>
        <p>Mailflow makes it easy to create beautiful email sequences in moments </p>
        <p>Build email sequences like a flowchart with Mailflow. All you need to do to connect your Mailflow account is copy your credentials from <a href="https://mailflow.com/account/api">here</a> and paste them into settings. If you need any help just get in contact with us at support@mailflow.com.</p>
        <p><a class="button button-primary" href="https://mailflow.com/sign-up?plugin" id="sign-up"><?php echo __("Get your Mailflow account", 'mailflow'); ?></a></p>


        <h2>Forms - a simple way to add email sign ups</h2>

        <p>Add new contacts to your newsletter or collect requests for more information with this simple custom form.</p>

        <p>This can be placed on any page or post using a short code. Fields can be added in the form to create contact attributes which are passed to your Mailflow account. Tags can also be added to the form which are passed to your Mailflow account along with the contacts.</p>

        <h2>Roles - Add any member of your Wordpress site</h2>
        <p>Send onboarding sequences and welcome emails to your new members, without changing any of your sign up forms.</p>
        <p>Add any new members of your site directly to your Mailflow account, regardless of how they signed up. You can also assign tags to different member roles so you can trigger different onboarding sequences for different membership levels.</p>

        <h2>Tracking - Trigger emails on page visits</h2>
        <p>Automatically send an email sequence as soon as someone views a page</p>
        <p>Assign tags to pages, when your members view those pages the tags are assigned to them. You can set sequences to be triggered by those tags in your Mailflow account or use them to send different content to different members within the same campaign.</p>

    <?php else: ?>
        <h2>Welcome to the Email Plugin for Mailflow</h2>
        <p>Here you can find the basics you’ll need to get your Wordpress site up and running with Mailflow, if you need any help there is a detailed walkthrough in our dedicated <a href="https://mailflow.com/support">support section</a> or just drop us a line at <a href="mailto:support@mailflow.com">support@mailflow.com</a>.</p>
        <h2>Forms - a simple way to add email sign ups</h2>
        <p>Add new contacts to your newsletter or collect requests for more information with this simple custom form.</p>
        <p>This can be placed on any page or post using a short code. Fields can be added in the form to create contact attributes which are passed to your Mailflow account. Tags can also be added to the form which are passed to your Mailflow account along with the contacts.</p>
        <h2>Roles - Add any member of your Wordpress site</h2>
        <p>Send onboarding sequences and welcome emails to your new members, without changing any of your sign up forms.</p>
        <p>Add any new members of your site directly to your Mailflow account, regardless of how they signed up. You can also assign tags to different member roles so you can trigger different onboarding sequences for different membership levels.</p>
        <h2>Tracking - Trigger emails on page visits</h2>
        <p>Automatically send an email sequence as soon as someone views a page</p>
        <p>Assign tags to pages, when your members view those pages the tags are assigned to them. You can set sequences to be triggered by those tags in your Mailflow account or use them to send different content to different members within the same campaign.</p>
    <?php endif; ?>
</div>