[English](README.md)

---

# 从 Mastodon 导入

该插件自动将 Mastodon（[链接](https://joinmastodon.org/)）上的嘟文（即短贴文）转换为 WordPress 贴文。

## 安装

目前，你可以下载 [ZIP 文件](https://github.com/Guanchishan/import-from-mastodon/archive/refs/heads/master.zip)。上传至 `wp-content/plugins` 并解压。你还可以选择在解压后将位于WordPress目录下的文件夹从 `import-from-mastodon-master` 重命名为 `import-from-mastodon`（该操作可能有助于避免潜在的冲突）。

激活插件后，请访问 设置 > Import From Mastodon。填写你的实例的 URL 以及其他选项。点击保存更改。

然后，在同一设置页面上，点击授权访问按钮。这将带你到你的 Mastodon 实例，并允许你授权 WordPress 从你的时间线读取信息（我们不请求写入权限）。之后，你将自动被重定向到 WordPress。

**注意**：WordPress 不会立即开始导入嘟文，但会在几分钟后开始。我将在下个版本中「修复」这个问题。

## 工作原理

大约每 15 分钟——因为 WordPress 的计划任务系统并不精确——你的 Mastodon 时间线会被检查有无新的嘟文，然后导入为你所选择的贴文类型（post type）。

默认情况下，只考虑最近的 40 条嘟嘟（这也是 Mastodon API 允许的最大值。除非你每 15 分钟创建超过 40 条嘟嘟，否则这不应该是个问题）。

### 注意事项

当插件首次运行时，最多将导入 40 条（根据上述说明）嘟嘟（这可能在下个版本中更改为只导入一条）。在此之后，只有 _最新的_ 嘟嘟会被考虑（我们使用 `since_id` API 参数告诉 Mastodon 为我们查找哪些嘟嘟。这个 `since_id` 对应最近导入的 _现有的_ ——即在 WordPress 中的贴文）。

如果这听起来有些混乱，那是因为确实满乱的。不过，或许并不那么复杂。不管怎样，你可以选择无视这些。

## 转发和回复，以及自定义格式化

可以选择排除或包含转发或回复。

只是，呃，转发和回复可能看起来有点 _奇怪_，并且可能缺少一些上下文。

### 话题串

没有该功能。如果启用了回复功能，对自己的回复将作为单独的新文章，而不是作为 WordPress 评论导入（这倒是可以再做一个很好的插件扩展）。

## 标签和黑名单

**标签**：（可选）只查找带有这些标签的嘟嘟（并忽略所有其他嘟嘟）。标签之间用逗号分隔。  
**黑名单**：（可选）忽略含有这些单词的嘟嘟（每行一个单词或单词的一部分）。要注意部分匹配！

## 图片

图片将被下载并[附加](https://wordpress.org/support/article/using-image-and-file-attachments/#attachment-to-a-post)到导入的嘟嘟，但尚未自动包含 _在_ 贴文中。

唯第一张图片将被设置为新导入文章的特色图片。当然，这种设置也可以更改：

```
add_filter( 'import_from_mastodon_featured_image', '__return_false' ); // 不设置特色图片

```

## 杂项

实际上还有一些其他的过滤器和设置，我可能会逐渐提供更好的文档（尽管这些设置应该会一目了然）。

### 自定义贴文类型

比如，如果想让你导入的嘟文成为自定义贴文类型（而不是默认的 `post`）：

```
add_filter( 'import_from_mastodon_args', function( $args, $status ) {
$args['post_type'] = 'iwcpt_note'; // 自定义「post」类型。

unset( $args['post_category'] ); // 因为自己的自定义贴文类型可能完全不支持「category」分类法。

return $args;
}, 10, 2 );


```

上述 `$args` 实际上就是传递给 [`wp_insert_post()`](https://developer.wordpress.org/reference/functions/wp_insert_post/#parameters) 的参数（无穷的可能性！）。
