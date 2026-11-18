# Troubleshooting

Written against v1.3.3.

- [The theme cannot be activated](#the-theme-cannot-be-activated)
- [The home page sections do not appear](#the-home-page-sections-do-not-appear)
- [Gallery columns 2 to 4 do not appear](#gallery-columns-2-to-4-do-not-appear)
- [A Leadership page shows its editor content instead of the widgets](#a-leadership-page-shows-its-editor-content-instead-of-the-widgets)
- [A News page lists no posts](#a-news-page-lists-no-posts)
- [The Blog template is missing](#the-blog-template-is-missing)

## The theme cannot be activated

**Cause:** the Genesis parent theme is not installed, or the site runs an older WordPress or PHP than the theme requires.

**Fix:** put Genesis in `wp-content/themes/genesis/`, and check the site runs WordPress 6.8 or later and PHP 8.2 or later. See [Installation](Installation.md#requirements).

## The home page sections do not appear

**Cause:** the sections show only on page 1 of the front page, and only for areas that have widgets. When none of Home Welcome, Home Gallery 1 and Call to Action has a widget, none is shown.

**Fix:** add a widget to the area under *Appearance*, *Widgets*, and load the first page of the front page. See [Setting up the home page](Setting-up-the-home-page.md).

## Gallery columns 2 to 4 do not appear

**Cause:** the gallery row is shown only when **Home Gallery 1** has a widget.

**Fix:** add a widget to **Home Gallery 1** first.

## A Leadership page shows its editor content instead of the widgets

**Cause:** the **Page - Leadership** widget area has no widgets, so the template falls back to the page's own content.

**Fix:** add widgets to **Page - Leadership**. See [Page templates](Page-templates.md#leadership-page).

## A News page lists no posts

**Cause:** the template lists posts in the category whose slug is `news`. With no such category, or no posts in it, the list is empty.

**Fix:** create the category with the slug `news` and assign posts to it. See [Page templates](Page-templates.md#news).

## The Blog template is missing

**Cause:** the theme removes Genesis's Blog template from the template list.

**Fix:** use the [News](Page-templates.md#news) template for a list of posts, or set a posts page under *Settings*, *Reading*.
