## class-import-from-mastodon.php

这段代码定义了一个叫做 `Import_From_Mastodon` 的 PHP 类，这个类应该是 WordPress 插件的主要部分。它的主要功能包括定时从 Mastodon 网站获取更新（也称为 toots）并导入到你的 WordPress 站点。

以下是该类的主要组成部分：

1. **单例模式**：`Import_From_Mastodon` 类使用了单例模式，这意味着该类的实例只能创建一次，这通过 `get_instance` 方法和私有的构造函数 `__construct` 实现。

2. **实例变量**：`Import_From_Mastodon` 类有两个私有实例变量 `$import_handler` 和 `$options_handler`，它们分别代表 Import 和 Options 的处理器实例。它们被赋值和初始化在 `register` 方法中。

3. **register 方法**：该方法负责初始化类的主要组件，注册钩子回调函数以及计划定时任务。

4. **定时任务**：方法 `add_cron_schedule` 添加了一个新的 WP Cron 间隔，每 15 分钟执行一次。在 `activate` 方法中，如果不存在一个预定的 'import_from_mastodon_get_statuses' 事件，就会使用这个间隔调度一个。`deactivate` 方法则用来清除这个计划任务。

5. **激活和停用**：`activate` 和 `deactivate` 是插件生命周期的一部分，当插件被激活或停用时，它们会被调用。在这个例子中，它们被用于调度和清除定时任务。

6. **本地化**：`load_textdomain` 方法是用来加载插件的本地化文件，这样插件就可以支持多种语言。
    

总的来说，这段代码创建了一个 WordPress 插件，该插件每 15 分钟从 Mastodon 网站导入更新的嘟文（toots）。该插件使用了插件的生命周期（激活和停用），并且可以根据本地语言设置进行本地化。

## class-import-handler.php

### 功能

这个 PHP 脚本是一种在 WordPress 中从 Mastodon 导入帖子的工具。它主要的功能有：

1. **内容清理和格式化**：通过 wp_kses 函数清理和格式化内容，只保留链接（a 标签），段落（p 标签）和换行（br 标签）。

2. **内容重复检查和管理**：如果内容已经存在，将不会导入。这包括对 Mastodon 帖子的重新发布和回复。

3. **内容为空的检查**：如果内容为空并且没有媒体附件，将跳过。

4. **标题生成**：如果内容不为空，将从内容中生成标题。如果内容为空但媒体附件的描述不为空，将从媒体附件的描述生成标题。

5. **内容和标题的过滤**：允许通过使用 apply_filters 函数来重写自动生成的内容和标题。

6. **帖子插入**：通过 wp_insert_post 函数插入帖子，并设置一些元数据，比如作者，分类，发布状态等。

7. **媒体附件管理**：如果帖子包含媒体附件（目前只支持图像），则会下载并创建一个 WordPress 附件。第一个成功上传的附件将被设置为特色图像。

8. **Mastodon 账户管理**：该脚本还有一个获取已认证用户的 Mastodon 账户 ID 的函数。

9. **错误日志**：有许多错误检查和日志记录步骤，以帮助识别和解决问题。

### 原理

这个脚本的工作原理是通过 Mastodon API 获取信息，然后根据得到的信息在 WordPress 中创建帖子。

首先，这段代码使用了 `wp_kses` 函数来清理并过滤从 Mastodon 返回的状态消息内容，然后存储到 `$content` 变量中。`wp_kses` 函数的作用是确保输入的内容安全，防止潜在的 XSS 攻击。它的第一个参数是要清理的数据，第二个参数是一个允许的 HTML 元素和属性的列表。这里，允许的 HTML 元素包括 `a`，`br` 和 `p` 标签。

接着，这段代码检查 Mastodon 状态是否是一个转发的消息。如果是，那么将转发的消息内容也保存到 `$content` 变量中，并且将转发的消息包含在一个块引用（blockquote）中，为了给读者提供更多的上下文信息。

如果 Mastodon 状态是一个回复，则这段代码会尝试获取父级状态并将其内容添加到 `$content` 中，为回复提供上下文。

在处理完 Mastodon 状态的内容后，代码会生成一个文章的标题，方法是从内容中提取前 10 个词。如果内容为空，且有媒体附件存在，那么标题则从媒体附件的描述中生成。

接下来，这段代码定义了一个新文章的参数数组，然后使用 `wp_insert_post` 函数将这个新文章插入到 WordPress 数据库中。在插入新文章时，它还会检查是否有媒体附件，如果有，会将这些媒体附件也导入到 WordPress。

此外，这段代码中还包含了一些错误处理和日志记录功能。例如，当从 Mastodon API 获取数据出现问题，或者插入新文章失败时，会有错误日志输出。

另外，该代码还包含一个名为 `create_attachment` 的函数，其作用是从 Mastodon 下载图像并将其导入到 WordPress 媒体库中。

最后，这段代码还包含两个辅助功能函数，一个是获取 Mastodon 账户的 ID，另一个是检查是否已经存在相似的文章。

总的来说，这段代码是一个处理从 Mastodon 社交平台导入数据到 WordPress 的插件。它将 Mastodon 的状态消息转换为 WordPress 的文章，并处理媒体附件和其他相关信息，以在 WordPress 网站上正确显示。
## class-options-handler.php

### 功能

这段代码为插件提供一个设置页面，该页面包含多个设置项，如：

- Mastodon 实例 URL
- 新导入的帖子状态
- 新导入的帖子的作者
- 新导入的帖子的默认类别
- 是否仅导入公开帖子
- 是否包括 boosts（Mastodon 中的转发）
- 是否包括回复
- 导入带有哪些标签的帖子
- 忽略包含哪些单词的帖子

用户可以在这个页面上设置这些选项，然后保存更改。此外，用户还可以授权 WordPress 从他们的 Mastodon 时间线中读取内容。最后，用户可以重置所有设置或撤销对 Mastodon 的访问权限。

该段代码也为插件提供了一些调试功能，在设置页可选择打开。如果在 WordPress 中启用了 WP_DEBUG 模式，用户就可以在设置页面上看到插件的配置信息。这有助于调试和解决问题。

### 原理

- `namespace Import_From_Mastodon;` 表示这个类是在 `Import_From_Mastodon` 命名空间中。
- `class Options_Handler` 定义了一个名为 `Options_Handler` 的类。
- `const DEFAULT_SETTINGS` 和 `const POST_STATUSES` 定义了类的常量。默认设置是用于当选项没有被设置时作为默认值的一个关联数组。允许的文章状态是一个数组，列出了 WordPress 文章可能的状态。
- `$options` 是一个私有的成员变量，存储插件的设置。
- `__construct()` 是类的构造函数，当这个类被实例化时，它会被调用。它从 WordPress 的选项中获取插件的设置，如果没有设置，则使用默认设置。
- `register()` 注册了许多 WordPress 的钩子，例如为 WordPress 后台添加菜单，为后台页面加载脚本，处理表单提交，以及在从 Mastodon 导入后设置最新的嘟文（toot）ID。
- `create_menu()` 和 `add_settings()` 用于在 WordPress 后台创建一个新的设置页面，并注册这个插件的设置。
- `sanitize_settings($settings)` 是一个处理提交的选项的函数。它接受通过 WordPress 管理页面提交的设置，验证并清理这些设置，然后返回要存储的选项。
- `request_access_token`：这个函数使用授权代码（传入的参数）向 Mastodon 请求一个访问令牌。如果请求成功，它将保存访问令牌；如果请求失败，它将记录错误消息并返回 false。
- `revoke_access`：这个函数会撤销 WordPress 对 Mastodon 的访问权限。如果撤销成功，它将删除访问令牌并返回 true；如果失败，它将记录响应并返回 false。
- `reset_options`：这个函数会重置所有插件选项为默认设置。
- `admin_post`：这是一个回调函数，它响应 `admin-post.php` 页面的请求。根据 GET 参数，它可能撤销访问权限，重置插件选项，或者执行其他未在代码段中展示的操作。
- `set_latest_toot`：这个函数更新最近一次导入的 Mastodon 状态的 ID。这个 ID 被保存在插件选项中。
- `forget_latest_toot`：这个函数会删除插件选项中保存的最近一次导入的 Mastodon 状态的 ID。
- `get_options`：这个函数返回当前的插件选项。

这段代码也使用了 WordPress 的核心函数，如 `wp_remote_post` 和 `update_option`。同时，它也使用了 WordPress 的一些安全性实践，如检查用户权限、验证 nonce、清理和转义 URL。

