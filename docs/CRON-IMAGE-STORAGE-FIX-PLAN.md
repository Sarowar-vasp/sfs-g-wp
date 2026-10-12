# Cron and Image Storage Fix Plan

## Objective and scope

Stop unnecessary imported-media growth, preserve legitimate content, and safely reclaim verified unused storage. Implement prevention before historical cleanup.

This document is a plan only. It does not authorize production deletion or change application code. The existing [analysis](../CRON-IMAGE-STORAGE-ANALYSIS.md) provides context; the qualifications below take precedence where that document overstates the findings.

## Findings guiding the work

- The duplicate-event SQL deletes post rows by title and leaves attachments, files, metadata, and taxonomy relationships behind. It can also delete legitimate same-title content across categories and statuses.
- Information imports download qualifying images for new notices but always select the default thumbnail. Existing published-title matches return before the download; growth is not unconditional on every run.
- Cross-importer title collisions normally skip the second item during sequential execution. Concurrent runs, status differences, and title transformations are possible duplicate paths, not proven production causes.
- The importers lack their own locks, although the WordPress cron runner has a lock.
- Insertion failures returning `0` are not consistently handled. The event metadata fallback can discard the year/month portion of an attachment path.
- Actual storage impact remains unmeasured: this checkout has no uploads directory or database dump.

## Phase 1 — Establish a baseline and contain unsafe deletion

### Tasks

- [ ] Obtain a database and uploads backup and verify restoration in an isolated environment.
- [ ] Confirm the deployed theme matches the reviewed source.
- [ ] Inspect the external scheduler, registered cron hooks, run duration, failures, and possible overlapping execution.
- [ ] Measure uploads bytes and file count, attachment count, imported-post count, and orphaned-record counts.
- [ ] Capture duplicate candidates across all statuses, including source metadata, category, attachment parents, and featured-image IDs.
- [ ] Replace the destructive portion of `delete_duplicate_tp_events()` with reporting while retention rules are developed.
- [ ] Assess the separate domain-replacement SQL in that callback before changing scheduling; preserve required behavior deliberately.

### Completion criteria

A reproducible baseline and restorable backup exist. Automatic title-based post deletion is contained, and suspected duplicates are reported without deleting content.

## Phase 2 — Make post imports safe to repeat

### Tasks

- [ ] Confirm source-ID stability and uniqueness for events, information notices, and blogs.
- [ ] Define identity as source system + content type + source ID; avoid assuming IDs are unique across feeds.
- [ ] Map historical posts using existing source IDs and URLs. Report missing or ambiguous identities rather than guessing from titles.
- [ ] Replace title/slug-only duplicate checks with source-identity lookup.
- [ ] Define update rules for source changes, manually edited content, and existing draft or trashed posts.
- [ ] Add atomic importer locks with owner tokens, expiry, safe release, and handling for runs exceeding the lease. Do not rely on a non-atomic transient check followed by a write.
- [ ] Ensure every entry point follows the same locking and identity rules.
- [ ] Fix pagination so a page containing only existing items does not imply the end of the feed.
- [ ] Track failed items for bounded retries without silently losing them when pagination advances.

### Completion criteria

Repeated or concurrent imports do not create additional posts for the same source identity. Distinct same-title items survive, and later feed pages remain reachable.

## Phase 3 — Prevent unnecessary media creation

### Tasks

- [ ] Keep the current default notice-thumbnail behavior and remove unused notice-image sideloads.
- [ ] Validate that the configured default attachment exists and is usable.
- [ ] Record source image URLs consistently for event and blog attachments; reuse valid existing attachments before downloading.
- [ ] Define how changed image content at an unchanged URL is detected or refreshed. URL equality alone does not prove content equality.
- [ ] Prevent concurrent downloads from creating duplicate attachments for a shared source image.
- [ ] Create and validate blog posts before sideloading their images, and associate new attachments with the post.
- [ ] Resolve the blog fallback through a valid existing attachment rather than downloading a root-relative path.
- [ ] Preserve shared attachments when posts or featured images change.

### Completion criteria

New notices no longer create unused image copies. Reimports reuse valid media, blog thumbnails have valid associations, and fallback images work without repeated downloads.

## Phase 4 — Make failure handling recoverable

### Tasks

- [ ] Check post and attachment insertion results for both `0` and `WP_Error`, or request error objects explicitly and still validate returned IDs.
- [ ] Stop dependent metadata and media operations when post creation fails.
- [ ] If attachment insertion fails after upload, remove only the file created by that attempt after verifying ownership and references.
- [ ] Preserve the uploads-relative attachment path, including year/month directories, when metadata generation fails.
- [ ] Retry metadata generation against the existing attachment instead of downloading another copy.
- [ ] Log source identity, post/attachment IDs, outcomes, and retry state without dumping full imported content.

### Completion criteria

Simulated download, database, and metadata failures do not create silent file leaks or broken attachment paths. Retries recover without duplicating posts or media.

## Phase 5 — Validate and deploy prevention

### Validation scenarios

- [ ] Import the same page twice: the second run adds no duplicate posts or files.
- [ ] Import distinct same-title items across feeds: both remain available.
- [ ] Run competing importer executions: locking prevents duplicate creation and releases safely after failure.
- [ ] Process an all-existing page followed by a page containing new items: pagination reaches the new items.
- [ ] Import a notice with an image: the default thumbnail is used without a new media download.
- [ ] Exercise valid, missing, changed, and shared source images.
- [ ] Exercise insertion failure, metadata failure, interrupted execution, and retry recovery.
- [ ] Confirm draft/trash and manual-edit behavior matches the rules defined in Phase 2.
- [ ] Verify the external scheduler with automatic WP-Cron spawning disabled.

### Rollout and completion criteria

Deploy to staging first, then production with a limited monitored run. Compare post counts, attachment counts, failures, and uploads growth to Phase 1. Keep historical cleanup disabled until prevention checks pass.

If validation fails, pause affected import hooks and restore the last safe implementation. Do not reactivate raw title-based deletion as a rollback step.

## Phase 6 — Audit historical cleanup candidates

### Tasks

- [ ] Generate a read-only report of duplicate source identities and propose which record to retain based on content, status, references, and editorial decisions—not highest ID alone.
- [ ] Identify missing-parent attachments, unused notice images, orphaned post metadata and taxonomy relationships, and files without attachment records.
- [ ] Distinguish generated size variants and originals from independent duplicate downloads.
- [ ] Inspect references in featured images, post content, custom fields, builder data, options, and other relevant plugin storage.
- [ ] Compare attachment file paths as well as IDs to identify shared physical files.
- [ ] Treat `_sfs_source_id` as source provenance, not as an attachment reference.
- [ ] Do not classify parent-`0` blog attachments as unused when they are referenced as thumbnails.
- [ ] Assign each candidate evidence, estimated recoverable bytes, and a keep/review/delete recommendation. Avoid double-counting shared files.

### Completion criteria

A reviewable dry-run report separates proven unused data from ambiguous candidates. Filename suffixes or missing parents alone are not deletion evidence.

## Phase 7 — Perform reviewed cleanup and monitor

### Tasks

- [ ] Obtain approval for the concrete deletion candidate list and refresh the backup before cleanup.
- [ ] Recheck references immediately before deletion; coordinate with import activity to avoid races.
- [ ] Delete approved duplicate posts through WordPress APIs after preserving required content and relationships.
- [ ] Delete approved unused attachments explicitly through the attachment deletion API; deleting a post alone does not remove its media.
- [ ] Handle historical orphaned metadata and taxonomy relationships separately from post deletion.
- [ ] Handle files without attachment records through a separately reviewed file inventory; verify paths remain within uploads and that no live reference exists.
- [ ] Process small batches with an audit log and stop on unexpected failures or missing content.
- [ ] Verify retained event, notice, and blog images after each batch.
- [ ] Compare reclaimed bytes with the dry-run estimate and monitor several complete import cycles.

### Completion criteria

Verified unused storage is reclaimed without breaking retained content. Import runs remain repeatable, and measured growth is attributable to new or intentionally refreshed media.

## Implementation references

- [Event importer and identity checks](../wp-content/themes/unicamp-child/functions.php): `sfs_import_all_events()`, `sfs_create_post_from_item()`.
- [Media and information imports](../wp-content/themes/unicamp-child/functions.php): `sfs_safe_media_sideload_image()`, `sfs_import_all_info()`, `sfs_create_info_post_from_item()`.
- [Blog import and duplicate cleanup](../wp-content/themes/unicamp-child/functions.php): `sfs_import_blog_posts()`, `delete_duplicate_tp_events()`.
- [WordPress post and attachment behavior](../wp-includes/post.php).
- [WordPress media sideload behavior](../wp-admin/includes/media.php).
- [Cron runner and locking](../wp-cron.php).
