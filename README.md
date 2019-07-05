# StevoTVRBot

This application lives on my server to supply a custom API for the [StreamElements](https://streamelements.com/) chat bot on the [StevoTVR Twitch channel](https://www.twitch.tv/stevotvr). This allows us to add custom chat responses via the `customapi` variable.

## Configuration

The configuration for the application is found in the src/inc/Config.php file (rename Config.php.example if installing fresh).

| Option   | Description                                                       |
| -------- | ----------------------------------------------------------------- |
| `DBHOST` | The hostname of the MySQL database server                         |
| `DBUSER` | The name of the user to use for accessing the MySQL database      |
| `DBPASS` | The password for the user to use for accessing the MySQL database |
| `DBNAME` | The name of the MySQL database                                    |
| `SECRET` | The secret string required by API requests                        |

## Implementation

Chat commands are implemented in the StreamElements chat bot using the `${customapi}` variable. In the response string:

```
${customapi.https://<BASE_URL>/bot?input=${queryescape ${0:}}&user=${queryescape ${user}}&secret=<SECRET>}
```

Replace `<SECRET>` with the value of `Config::SECRET`. Since the secret is included in the URL, **the chat commands must be hidden in public pages**.
