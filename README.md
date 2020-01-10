# Plugins Deactivator for WordPress

Disables all WordPress plugins with one click. Remembers which plugins were active so they can also be re-activated with one click.

## How to Use

This plugin adds a button to the top of `wp-admin/plugins.php`. When clicked, every plugin (except for this one) will be deactivated. However, all active plugins are remembered.

After the plugins are deactivated, a button will appear to reactivate them. This will reactivate only the plugins that were active before - plugins that were inactive will not be enabled.

This is helpful for users who need to troubleshoot plugin conflicts by deactivating plugins in bulk. Without this plugin, you'd have to reactivate plugins based on memory. If there are a lot of plugins running on the site, with numerous plugins that are disabled, this can be difficult to do without first creating a list of what was active and inactive.

With the Plugins Deactivator plugin, you can easily disable all plugins at once. With one more click, you can restore your plugins to where they were before.