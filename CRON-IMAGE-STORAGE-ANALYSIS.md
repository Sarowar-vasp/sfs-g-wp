# Imported Image Storage: Findings and Suggested Solutions

## Summary

The custom theme contains a cleanup job that removes duplicate event rows directly from the WordPress `posts` table. It does not remove the corresponding attachment records or image files. If those duplicate events have imported images, the cleanup leaves the images and their generated size variants in `wp-content/uploads`, even though the event posts are gone.

Every code-level claim below has been verified against the theme source and the bundled WordPress core. This is a confirmed cleanup gap in the code. It is a plausible source of accumulating duplicate images, but the code alone does not establish how much storage it accounts for or whether this is the only cause.

## Verified code facts

All line numbers refer to [`wp-content/themes/unicamp-child/functions.php`](./wp-content/themes/unicamp-child/functions.php) unless marked otherwise.

| Claim | Evidence |
|---|---|
| Event dedup rejects any **published** `tp_event` with the same title | `3446-3455` |
| Info dedup uses the same title-based check | `3793-3801` |
| Dedup is title-based, not `_sfs_source_id`-based | `_sfs_source_id` is written at `3498` (events) and `3838` (info) but is never queried anywhere |
| Event sideload downloads, creates attachment, generates metadata and size variants | `sfs_safe_media_sideload_image()` `3549-3617`; `wp_generate_attachment_metadata()` calls `wp_create_image_subsizes()` (`wp-admin/includes/image.php:588`) |
| Cleanup runs hourly via raw SQL `DELETE`, keeping `MAX(ID)` per `post_title`, with no WP APIs and no attachment/file deletion | `delete_duplicate_tp_events()` `4352-4379`, scheduled `4381-4388` |
| Info importer sideloads an image but sets a fixed default thumbnail | `3866-3876`, default ID `SFS_DEFAULT_INFO_IMAGE_ID = 17070` (`3285`) |
| Blog importer creates attachments with parent `0` | `media_handle_sideload($file_array, 0)` `4286` |
| WP-Cron is disabled | `wp-config.php:104` `define('DISABLE_WP_CRON', true)` |

Supporting WordPress-core behavior verified in this backup:

- The title check compiles to an exact SQL match `post_title = %s` (`wp-includes/class-wp-query.php:2179`).
- Every sideload goes through `wp_handle_sideload()` → `_wp_handle_upload()` → `wp_unique_filename()` (`wp-admin/includes/file.php:991, 1135-1142`), so each re-download creates a **new** physical file set (for example `name.jpg`, `name-1.jpg`, `name-2.jpg`) plus size variants rather than overwriting the earlier copy. Repeat imports can only add files, never reuse them.
- The theme contains **zero** `wp_delete_post` / `wp_delete_attachment` calls; the only `unlink` calls (`3579`, `4289`) are error paths for temp files. Nothing in the theme ever deletes attachment rows or files.

## Corrections and additions found while verifying

1. **The title-mismatch scenario is overstated.** The stored title is uppercased (`3462`, `3809`) while the check uses the raw item title, but this is an exact `post_title` comparison and default MySQL/MariaDB collations are case-insensitive, so the case difference alone does not cause duplicates.

2. **The real collision is cross-importer.** Both importers create posts of the same type `tp_event` under one title namespace (events `3465`, info `3812`) and neither consults category or `_sfs_source_id`. When an event and an information share a title, the cleanup keeps only `MAX(ID)` — across **all post statuses and both categories** (`4360-4366`). One legitimately distinct post is deleted along with its attachment, and the deletion does not care whether the kept row is a draft and the deleted row is published.

3. **The information importer is a larger source of waste than described.** The image is downloaded **unconditionally for every notice** (`3866-3867`) and is **never** used, because all three branches set the default thumbnail instead. No code anywhere checks for an existing attachment or an identical URL before downloading, so the same URL produces a new copy on every run. This grows storage even with zero duplicate posts.

4. **There is no cron locking.** `sfs_import_all_events()` reads the page option, processes the page, then updates it (`3289` → `3389`) with no lock or "already running" flag. Overlapping executions process the same page concurrently and can create competing posts and attachments.

5. **The blog fallback is inert.** `$default_image = '/wp-content/uploads/2021/01/logoN.png'` (`4242`) is a root-relative path, so `download_url()` (`4278`) fails silently and no copy is stored; such posts simply end up with no thumbnail. It is not an active source of duplicate files, but it may explain missing thumbnails.

6. **A raw `DELETE` also orphans `postmeta`.** Replacing the SQL with `wp_delete_post()` would not by itself free storage either: WordPress does not delete a post's attachments automatically. Attachments need explicit cleanup, and because the raw SQL removes posts without touching `wp_postmeta`, leftover `_thumbnail_id` and `_sfs_source_id` rows can point at deleted posts and must be part of any reference audit.

## Likely accumulation sequence

1. An event (or information notice) is imported; its image is sideloaded as a new attachment with a unique filename and generated size variants (`3549-3617`, `3867`).
2. A second post with a matching title exists or is created — most plausibly a cross-importer collision, since both importers share the `tp_event` title namespace (`3465`, `3812`), or from overlapping unlocked cron runs (`3289` → `3389`).
3. The hourly cleanup deletes the lower-ID post row directly with SQL (`4356-4369`).
4. The attachment row, its `_thumbnail_id` postmeta, and the files on disk are not included in that deletion, so they remain.

The published-title check means an unchanged item found by the title check should not re-download its image on that run. Repeated downloads are possible when a new post is created or the same URL is imported again, and should be verified from the actual records and logs rather than assumed.

## Suggested solutions

### 1. Stop deleting duplicate posts with raw SQL

Replace the direct posts-table deletion with a WordPress-aware duplicate cleanup. First identify duplicate event IDs and decide which event to retain; then delete the other posts through WordPress APIs so WordPress can clean up post metadata, taxonomy relationships, and related state.

Note that `wp_delete_post()` still does not remove the post's attachments, and it will not clean the `postmeta` rows left behind by earlier raw deletes. Media cleanup remains a separate, explicit step.

### 2. Delete only attachments proven safe to remove

For each event selected for deletion:

- Find attachments associated with that event, including its featured image and children whose `post_parent` points at it.
- Check whether each attachment is referenced by any other post, page, event, content field, or postmeta (`_thumbnail_id`, `_sfs_source_id`) before deleting it.
- Delete only attachments that are not shared or referenced elsewhere, using the WordPress attachment deletion API so the attachment record and its files/derivatives are handled together.

Do not delete files based only on matching titles or filenames. A media item may be reused elsewhere, and an imported image may be referenced in content even if it is not the featured image. Also remember that size variants share the attachment's file path: two attachment rows can point at the same file if a sideload reused a name, so compare `_wp_attached_file` in addition to attachment ID.

### 3. Make imports idempotent using the source identifier

Use the source event identifier (`_sfs_source_id`) as the stable identity for imported events rather than relying only on titles. It is already written at `3498` and `3838` but never read. Before downloading an image, look up the source ID and update or skip the existing event as appropriate. This reduces the chance of creating a second post and downloading another copy when a title collides across the two importers or is not unique.

Also prevent overlapping cron executions — add a lock option or transient guard around each importer run (page-state window at `3289` → `3389`), so only one run processes a given page/item at a time.

### 4. Review other importer paths

- **Information imports:** the downloaded image is never used as the thumbnail (`3866-3876`). Either stop sideloading it entirely, or reuse it as the event's image instead of the default. Additionally, look up an existing attachment by source URL/ID before downloading so repeated notices do not create fresh copies (`wp_unique_filename` guarantees a new file each time, `wp-admin/includes/file.php:991`).
- **Blog imports:** attachments with parent `0` appear to be an artifact of sideloading before the post exists (`4286`, post inserted at `4315`). Create the post first, or re-parent afterwards. Do not delete unattached media globally without a reference audit. The root-relative fallback path (`4242`) fails silently and should be replaced with a valid URL if a fallback is intended.
- Because the compare is exact and collation-dependent (`class-wp-query.php:2179`), do not rely on title matching for identity in any of these paths.

### 5. Measure existing storage before cleanup

Before removing any existing media:

1. Take a database and uploads backup.
2. Count `tp_event` posts per normalized title and inspect their `_sfs_source_id` values and featured-image IDs. Include both importer categories, since the title namespace is shared.
3. Find attachments associated with deleted or duplicate events, plus unattached attachments, and check for references from other content and from `postmeta` rows.
4. Compare attachment records and files on disk; the database alone does not show every orphaned file, and duplicate filenames with suffixes (`-1`, `-2`) indicate repeated sideloads.
5. Run a report/dry run first, review candidates, and only then perform a targeted cleanup.

## Operational consideration

The WP configuration defines `DISABLE_WP_CRON` as `true` (`wp-config.php:104`). The custom cleanup and import schedules therefore require an external scheduler or another explicit trigger to run. Confirm that scheduler's configuration and job history when investigating when duplicates or orphaned images were produced.

## What remains to be verified

The code establishes that duplicate-event cleanup does not clean associated media, that the two importers share the `tp_event` title namespace, and that the information path downloads an image per notice without using it. To confirm whether this is the primary cause of current server storage growth, inspect actual duplicate event records, attachment parent/thumbnail relationships, media references, uploads disk usage, and external cron execution history. This backup contains no `wp-content/uploads` directory or database dump, so those measurements must happen on the live site.
