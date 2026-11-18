# Setting up the home page

Written against v1.3.3.

The front page can show three sections between the header and the content: a welcome area, a gallery row of up to four columns, and a call-to-action bar. Each is a widget area, so the page is built by adding widgets.

## Steps

1. **Set a static front page or keep the latest posts.** The theme's `front-page.php` applies either way, under *Settings*, *Reading*.
2. **Add the welcome.** Under *Appearance*, *Widgets*, add a widget (for example a Text or Custom HTML widget) to **Home Welcome**. The front page shows it below the header.
3. **Add the gallery.** Add widgets to **Home Gallery 1**, then to **Home Gallery 2**, **3** and **4** as needed. The gallery row appears only when **Home Gallery 1** has a widget; the other three fill the remaining columns.
4. **Add the call to action.** Add a widget to **Call to Action**. The front page shows it as a bar after the gallery.
5. **Check the front page.** Load the site's home page. Each section with widgets appears, in the order welcome, gallery, call to action.

**Note:** the sections appear on the first page of the front page only. A paged front page (page 2 and later) shows the content without them.

**Note:** shortcodes run in Text widgets, so a Text widget in any of these areas can hold a shortcode.

## Reference

- [Widget areas](Widget-areas.md)
- [Page templates](Page-templates.md#front-page)
