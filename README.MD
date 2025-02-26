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

### Step 3: Verify in FluentCRM (Optional)

1. Go to **FluentCRM > Settings > Email Deliverability** 
2. You should see EmailIt listed as a connection option
3. Make sure EmailIt is configured as your preferred email service

## How It Works

When the integration is enabled:

1. All FluentCRM emails (campaigns, automations, etc.) will be routed through the EmailIt API
2. EmailIt will handle email delivery, tracking, and reporting
3. Your sending domain configuration from EmailIt will be used

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