# Production SEO Readiness

The public application uses `APP_URL` as its canonical origin and does not hardcode the production domain in business logic. Published articles, active categories, the index, institutional pages, sitemap, news sitemap, RSS, robots, Open Graph, X/Twitter metadata, NewsArticle, BreadcrumbList, Organization, and WebSite JSON-LD are server-rendered.

Search pages and parameterized category tabs use `noindex, follow`. Admin, preview, unpublished, scheduled-future, archived, and internal content is not publicly queryable. Article pages expose a visible breadcrumb and matching article hierarchy.

The news sitemap is limited to published articles from the last two days (maximum 1000 entries) and excludes future/non-published content. XML responses use XML content types and Blade escaping. RSS contains published articles only.

Production requirements remain environment-owned: `APP_URL=https://harianmerahputih.co.id`, HTTP-to-HTTPS and www-to-non-www redirects, HTTPS certificate, and Google Search Console verification. These are not performed in this local phase.

Before deployment, validate APP_URL-generated metadata, robots, XML, and structured data locally. After deployment, verify HTTPS, Search Console ownership, URL Inspection, sitemap submission, and Rich Results Test. No Search Console submission has been performed locally.

Owner input still required: confirmation of the official company logo/publisher facts and any official social profiles or contact details that may be added to structured data. No address, telephone, founding date, or social profile was invented.

