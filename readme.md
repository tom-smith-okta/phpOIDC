Okta OIDC session manager - PHP

tom.smith@okta.com

This site shows how to use Okta OIDC flows to manage user sessions.

configuration

configuration happens in the config.json file.

OIDCclients: for dev purposes, you can have different OIDC clients. The script will first look for values in the 'default' object.

client_secret_path: a path to a file containing (only) a client_secret.

requireAuthN: sets the default behavior of the script when loading a page. When true, the script will bounce the user to Okta for authN before loading the page. In other words, the page will not load at all for the user. This setting can be overridden (set to false) on a per-page basis.

Pseudo-code / Logic
// Is the user currently authenticated?
	// Does the local session contain a valid id_token?
		// If yes:
			// then TRUE
		// If no:
			// Does the app allow IDP-init flow?
				// If yes:
					// Have we recently (within the last few seconds) redirected the user to Okta?
						// If yes:
							// then FALSE
						// If no:
							// redirect the user to Okta with noprompt=true

// Does this page *require* authentication?
	// If yes:
		// Is the user authenticated?
			// If yes: we are done
			// If no:
				// redirect the user to Okta (with noprompt=false)
