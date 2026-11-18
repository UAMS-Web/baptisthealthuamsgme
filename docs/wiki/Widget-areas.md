# Widget areas

Written against v1.3.3.

The theme registers these widget areas, in addition to Genesis's own **Primary Sidebar** and **Header Right**. Add widgets to them under *Appearance*, *Widgets*. Genesis's **Secondary Sidebar** is unregistered, together with the three layouts that use it.

| Widget area | ID | Where it appears | Shown when |
| --- | --- | --- | --- |
| Utility Bar | `utility-bar` | A bar above the header, on every page except the Landing template | It has a widget |
| Home Welcome | `utility-home-welcome` | The front page, below the header | It has a widget, on page 1 of the front page |
| Home Gallery 1 to 4 | `utility-home-gallery-1` to `utility-home-gallery-4` | The front page, a row of up to four columns | **Home Gallery 1** has a widget, on page 1 of the front page |
| Call to Action | `utility-call-to-action` | The front page, a bar after the gallery | It has a widget, on page 1 of the front page |
| Page - Leadership | `page-leadership` | The content of a page using the Leadership template | It has a widget |
| Footer 1 to 3 | `footer-1` to `footer-3` | Three columns above the footer, from Genesis footer widgets | It has a widget; not on the Landing template |

## Example

To show a notice above the header on every page, add a Text widget to **Utility Bar**. The theme wraps it in `<div class="utility-bar"><div class="wrap">`.

## See also

- [Setting up the home page](Setting-up-the-home-page.md)
- [Page templates](Page-templates.md)
