=== Import from Mastodon ===
Contributors: janboddez, sanguok
Tags: notes, microblog, microblogging, syndication
Tested up to: 6.2
Stable tag: 0.3.3
License: GNU General Public License v3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically turn toots—short messages on [Mastodon](https://joinmastodon.org/)—into WordPress posts.

== Description ==

## How It Works
Every 15 minutes—more or less, because WordPress's cron system isn't quite exact—your Mastodon timeline is polled for new toots, which are then imported as the post type of your choice.

By default, only the 40 most recent toots are considered. (This is also the maximum value the Mastodon API will allow. Unless you create more than 40 toots per 15 minutes, this shouldn't be an issue.)

### Of Note
The very first time this plugin does its thing, up to 40 (per the remark above) toots are imported. (This might get changed to just one in a next version.) From then on, only the _most recent_ toots are taken into account. (We use a `since_id` API param to tell Mastodon which toots to look up for us. This `since_id` corresponds with the most recently imported _existing_, i.e., in WordPress, post.)

If all that sounds confusing, it is. Well, maybe not. Regardless, it's okay to just forget about it.

## Boosts and Replies, and Custom Formatting
It's possible to either exclude or include boosts or replies.

Just, uh, know that boosts and replies may look a bit _off_, and miss some context.

### Threading
There isn't any. Replies-to-self, when replies are enabled, are imported as separate, new posts, not comments. (Again, this would make a nice add-on plugin.)

## Tags and Blocklist
**Tags**: (Optional) Poll for toots with any of these tags only (and ignore all other toots). Separate tags by commas.  
**Blocklist**: (Optional) Ignore toots with any of these words. (One word, or part of a word, per line.) Beware partial matches!

## Images
Images are downloaded and [attached](https://wordpress.org/support/article/using-image-and-file-attachments/#attachment-to-a-post) to imported toots, but **not** (yet) automatically included _in_ the post.

The first image, however, is set as the freshly imported post's Featured Image. Of course, this behavior, too, can be changed:
```
add_filter( 'import_from_mastodon_featured_image', '__return_false' ); // Do not set Featured Images
```

## Miscellaneous
There are in fact a few more filters and settings that I might eventually document a bit better (though the settings should kind of speak for themselves).

### Custom Post Types
Like, if you wanted your imported toots be a Custom Post Type (rather than the default `post`):
```
add_filter( 'import_from_mastodon_args', function( $args, $status ) {
	$args['post_type'] = 'iwcpt_note'; // My "Note" type.

	unset( $args['post_category'] ); // Because my CPT may not support the "category" taxonomy at all.

	return $args;
}, 10, 2 );

```
Above `$args` are in fact the very arguments passed on to [`wp_insert_post()`](https://developer.wordpress.org/reference/functions/wp_insert_post/#parameters). (Endless possibilities!)

== Changelog ==
= 0.3.3 =
Extracts text from the alt text of the attachment as a title when there is no text in the toot.
Increase the upper limit on the number of words that can be extracted from the toot as a title to show more complete content in the title bar.
Add support for Syndication plugin - show the original link of the toot in the syndication links column.