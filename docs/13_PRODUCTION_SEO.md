# Production SEO Readiness

The public application uses `APP_URL` as its canonical origin and does not hardcode the production domain in business logic. Published articles, active categories, the index, institutional pages, sitemap, news sitemap, RSS, robots, Open Graph, X/Twitter metadata, NewsArticle, BreadcrumbList, Organization, and WebSite JSON-LD are server-rendered.

Search pages and parameterized category tabs use `noindex, follow`. Admin, preview, unpublished, scheduled-future, archived, and internal content is not publicly queryable. Article pages expose a visible category label and matching `BreadcrumbList` hierarchy in JSON-LD.

The main sitemap contains all published, non-demonstration public URLs. The news sitemap is limited to published, non-demonstration articles from the last two days (maximum 1000 entries), as required for Google News discovery, and excludes future/non-published content. XML responses use XML content types and Blade escaping. RSS contains published, non-demonstration articles only.

Production requirements remain environment-owned: `APP_URL=https://harianmerahputih.co.id`, HTTP-to-HTTPS and www-to-non-www redirects, HTTPS certificate, and Google Search Console verification. These are not performed in this local phase.

Before deployment, validate APP_URL-generated metadata, robots, XML, and structured data locally. After deployment:

1. Deploy the application on a public HTTPS domain and set `APP_URL` to the canonical HTTPS origin.
2. In Google Search Console, add a **Domain property** and verify ownership through the DNS TXT record. This is preferred because it covers both `www` and the apex domain.
3. Submit `https://domain-anda.tld/sitemap.xml`. The news sitemap is referenced by `robots.txt`; it can also be submitted separately when Google News visibility is relevant.
4. Use URL Inspection for a published article, request indexing, and run the Rich Results Test to validate `NewsArticle` and breadcrumb structured data.
5. Confirm that `/robots.txt`, `/sitemap.xml`, `/news-sitemap.xml`, and article URLs are reachable without authentication and use the HTTPS canonical host.

Google Search Console submission and ownership verification cannot be performed from the local application or repository.

Owner input still required: confirmation of the official company logo/publisher facts and any official social profiles or contact details that may be added to structured data. No address, telephone, founding date, or social profile was invented.
