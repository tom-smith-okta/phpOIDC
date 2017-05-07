Okta OIDC session manager - PHP

tom.smith@okta.com

This site shows how to use Okta OIDC flows to manage user sessions.

configuration

configuration happens in the config.json file.

OIDCclients: for dev purposes, you can have different OIDC clients. The script will first look for values in the 'default' object.

client_secret_path: a path to a file containing (only) a client_secret.

requireAuthN: sets the default behavior of the script when loading a page. When true, the script will bounce the user to Okta for authN before loading the page. In other words, the page will not load at all for the user. This setting can be overridden (set to false) on a per-page basis. 