## import-from-mastodon.js

这是一个使用 jQuery 编写的 JavaScript 脚本。在文档加载完成后执行，主要用于处理 WordPress 插件页面中的一些用户交互。

具体来说，这个脚本为页面上的一个特定按钮（类名为 `button-reset-settings`，且在 `settings_page_import-from-mastodon` 类下）添加了一个点击事件处理函数。

当用户点击这个按钮时，将会出现一个对话框，显示一个来自 `import_from_mastodon_obj` 对象的消息，以向用户确认是否真的要重置设置。

如果用户选择“取消”，则 `confirm` 函数会返回 `false`，`e.preventDefault()` 函数会被调用，这将阻止按钮的默认行为，即不会提交表单或者跳转页面等。这样做是为了确保用户在不完全确定的情况下不会误点击并触发可能的设置重置。

如果用户选择“确定”，`confirm` 函数会返回 `true`，`e.preventDefault()` 不会被调用，按钮的默认行为（例如提交表单或者跳转页面）将会继续执行。

总的来说，这个脚本为特定按钮添加了一个警告机制，避免用户在不经意间进行可能影响到插件设置的操作。
