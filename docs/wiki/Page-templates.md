# Page templates

Written against v1.3.3.

Choose a template for a page in the editor's page settings, under *Template*. The theme adds three, and its `front-page.php` controls the front page. Genesis's own Blog template is removed from the list.

| Template | File | What it does |
| --- | --- | --- |
| Landing | `page_landing.php` | A full-width page with no header, navigation, breadcrumbs, Utility Bar, footer widgets, footer menu or footer |
| Leadership Page | `page_leadership.php` | A full-width page whose content is the **Page - Leadership** widget area |
| News | `page_news.php` | A list of the five most recent posts in the category with the slug `news`, paged |

## Front page

`front-page.php` applies to the site's front page. On page 1 it adds the Home Welcome, Home Gallery and Call to Action sections where they have widgets (see [Setting up the home page](Setting-up-the-home-page.md)), and it marks the site title as the page's `h1`.

## Landing

Use it for a page that should stand alone, such as a campaign page. The page body class gets `utility-landing`, and only the page's content is shown.

## Leadership Page

The template replaces the page's content with the **Page - Leadership** widget area, full width. **The page's own editor content is not shown** while the widget area has widgets; with no widgets, the page falls back to its normal content and layout. The theme also adds an image size, `leadership` (210 by 280 pixels, cropped), for portraits in that area.

## News

The template ignores the page's own content and lists posts in the `news` category, newest first, five per page, with pagination. Create the category with the slug `news` before using it.

## See also

- [Widget areas](Widget-areas.md)
- [Troubleshooting](Troubleshooting.md)
