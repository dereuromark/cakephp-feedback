# Configuration

Settings live under `Feedback`. See [the example configuration](https://github.com/dereuromark/cakephp-feedback/blob/master/config/app.example.php) for the full array.

| Setting | Behavior |
| --- | --- |
| `stores` | Map store classes to their class names; `null` disables a default store. Filesystem storage is the default. |
| `configuration` | Per-store options keyed by the store's `NAME`, such as `Filesystem` and `Database`. |
| `returnlink` | Append a report link to the success message when supported by the store. Disable when report browsing is unavailable. |
| `skipCss` | Skip the element's stylesheet; load `Feedback.sidebar` through your layout instead. |
| `enableacceptterms` | Display the screenshot acknowledgement checkbox. |
| `termstext` | Enable the translated screenshot explanation in the acknowledgement tooltip. |
| `enablecopybyemail` | Display the legacy email-copy checkbox. Leave disabled for the standard store setup. |
| `autoLink` | Turn report URLs into links in supported report templates. |
| `displayExisting` | Set to `false` to hide name/email inputs when account data already exists. |
| `forceauthusername`, `forceemail` | Replace submitted name/email with values resolved from account data. |
| `sessionKey` | Fallback session prefix for the submission controller; defaults to `Auth.User`. |
| `authMap` | Field names for an AuthUser component/helper, or complete session paths without AuthUser. |
| `priorities` | `false` disables priorities; use `true` for the built-in list or an array for your own list. |
| `icons` | Override `close`, `screenshot`, `submit`, and `cancel` CSS icon classes. |

## Session account data

The example uses complete session paths:

```php
'authMap' => [
    'username' => 'Auth.User.username',
    'email' => 'Auth.User.email',
],
```

Without AuthUser, the element reads these paths. The submission controller also falls back to `<sessionKey>.username` and `<sessionKey>.email`. To prefill a different session format, change `authMap` as well as any fallback prefix you use.

With an AuthUser component/helper, configure field names such as `username` and `email` instead of session paths. The controller falls back to `account` for the name and `email` for the address. The bundled element requires an AuthUser helper to prefill through that API.

The Authentication plugin's identity request attribute is not automatically read. Provide compatible session data, an AuthUser adapter, or customize your element/controller.

## Store-specific options

`configuration.Filesystem.location` is a writable directory ending in a directory separator. `configuration.Database.table` defaults to `Feedback.FeedbackItems`. Per-store configuration overrides top-level options, including `returnlink`.

Old `mantis`, `github`, `bitbucket`, `jira`, `redmine`, and `mail` configuration blocks do not activate StoreInterface implementations. The deprecated `FeedbackstoreTable` contains legacy integration methods; several are unfinished. New integrations should use [custom stores](./stores).
