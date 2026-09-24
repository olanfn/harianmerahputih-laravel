# Article Table Search, Filter, and Sorting

The admin article index now uses a server-side Eloquent query. It never loads the full article dataset into the browser.

Supported query parameters:

- `q`: title search, trimmed and limited to 100 characters.
- `status`: draft, review, scheduled, published, or archived.
- `category`: validated category ID.
- `sort`: explicit whitelist for updated, created, title, and publication date ordering.

Pagination remains 20 rows per page and preserves all query parameters. The result count is database-calculated. Reset returns to the clean index URL. Writer ownership is applied in SQL before pagination; other roles see the authorized article set.

Category is eager-loaded with only `id` and `name`, avoiding an N+1 query. The query selects only fields used by the table. Existing indexes cover status, category_id, published_at, and timestamps; no new index was added. Title search currently uses parameterized `LIKE '%term%'`, which is appropriate for the initial shared-hosting scale. Revisit a MySQL FULLTEXT title index when title search becomes slow at a sustained large dataset; FULLTEXT has different behavior for short/partial Indonesian terms and is not added speculatively.

The form works with normal GET requests when JavaScript is unavailable. No DataTables, jQuery, SPA, or additional infrastructure was introduced. Hostinger compatibility remains unchanged.

