# EmailIt FluentCRM Integration

This plugin integrates FluentCRM with the EmailIt email delivery service, allowing you to send your FluentCRM emails through EmailIt for improved deliverability and tracking.

## Installation

1. Download the plugin file
2. Upload it to your WordPress site's `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the integration settings

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- EmailIt Mailer plugin activated and configured with a valid API key
- FluentCRM plugin activated

> [!NOTE]  
> For optimal email delivery performance, we recommend disabling WordPress default cron and setting up a server-side cron job instead.
> You can find the steps to set up this cron job below.

## Configuration

### Step 1: Configure EmailIt Mailer

Before using this integration, make sure your EmailIt Mailer plugin is properly configured:

1. Go to **Settings > EmailIt Mailer** in your WordPress admin
2. Enter your EmailIt API key
3. Configure your sending domain settings
4. Save settings and verify that the API connection is successful

### Step 2: Configure the Integration

1. Go to **Settings > EmailIt Mailer** in your WordPress admin
2. Click on the **FluentCRM** tab
3. Check the "Enable Integration" box
4. Click "Save Settings"


## How It Works

When the integration is enabled:

1. All FluentCRM emails (campaigns, automations, etc.) will be routed through the EmailIt API
2. EmailIt will handle email delivery, tracking, and reporting
3. Your sending domain configuration from EmailIt will be used

## Setting Up Server-Side Cron for EmailIt Mailer

For optimal email delivery performance, we recommend disabling WordPress default cron and setting up a server-side cron job instead.

### Step 1: Disable WordPress Cron

Add this line to your wp-config.php file:

```php
define('DISABLE_WP_CRON', true);
```

### Step 2: Set Up Server-Side Cron

Choose one of these methods:

#### Option A: Standard Cron with wget

Add this to your server's crontab (run `crontab -e`):

`*/5 * * * * wget -q https://yourdomain.com/wp-cron.php?doing_wp_cron -O /dev/null`

#### Option B: WP-CLI (if available on your server)

Add this to your server's crontab:

`*/5 * * * * cd /path/to/wordpress && wp cron event run --due-now`

#### Option C: Third-Party Cron Service

If you don't have access to server-side cron, use a service like [Cron-Job.org](https://cron-job.org):

1.  Create an account
2.  Add a new cronjob
3.  Set the URL to: `https://yourdomain.com/wp-cron.php?doing_wp_cron`
4.  Schedule it to run every 5 minutes

The 5-minute frequency is recommended for typical email volume. Adjust as needed for your site's requirements.

## Troubleshooting

If you encounter issues with the integration:

1. Verify that both EmailIt Mailer and FluentCRM plugins are active
2. Check that your EmailIt API key is valid and the connection test is successful
3. Make sure your sending domain is properly configured in EmailIt
4. Check WordPress debug logs for any error messages related to email sending

## Support

For support with this integration, please contact:

- Email: [your-support-email@example.com](mailto:your-support-email@example.com)
- Website: [your-website.com](https://your-website.com)

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
```