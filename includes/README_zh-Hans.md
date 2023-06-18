## class-import-from-mastodon.php

这段代码定义了一个叫做 `Import_From_Mastodon` 的 PHP 类，这个类应该是 WordPress 插件的主要部分。它的主要功能包括定时从 Mastodon 网站获取更新（也称为 toots）并导入到你的 WordPress 站点。

以下是该类的主要组成部分：

1.  **单例模式**: `Import_From_Mastodon` 类使用了单例模式，这意味着该类的实例只能创建一次，这通过 `get_instance` 方法和私有的构造函数 `__construct` 实现。
    
2.  **实例变量**：`Import_From_Mastodon`类有两个私有实例变量 `$import_handler` 和 `$options_handler`，它们分别代表 Import 和 Options 的处理器实例。它们被赋值和初始化在 `register` 方法中。
    
3.  **register方法**: 该方法负责初始化类的主要组件，注册钩子回调函数以及计划定时任务。
    
4.  **定时任务**：方法 `add_cron_schedule` 添加了一个新的 WP Cron 间隔，每15分钟执行一次。在 `activate` 方法中，如果不存在一个预定的 'import\_from\_mastodon\_get\_statuses' 事件，就会使用这个间隔调度一个。`deactivate` 方法则用来清除这个计划任务。
    
5.  **激活和停用**：`activate` 和 `deactivate` 是插件生命周期的一部分，当插件被激活或停用时，它们会被调用。在这个例子中，它们被用于调度和清除定时任务。
    
6.  **本地化**：`load_textdomain` 方法是用来加载插件的本地化文件，这样插件就可以支持多种语言。
    

总的来说，这段代码创建了一个 WordPress 插件，该插件每 15 分钟从 Mastodon 网站导入更新（toots）。该插件使用了插件的生命周期（激活和停用），并且可以根据本地语言设置进行本地化。