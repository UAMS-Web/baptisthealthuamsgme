<!-- cspell:words UAMS -->

# Installation

Written against v1.3.3.

At the end of this page the theme is active on a WordPress site, running on the Genesis Framework.

## Requirements

- **WordPress 6.8 or later** (`Requires at least` in `style.css`).
- **PHP 8.2 or later** (`Requires PHP` in `style.css`).
- **The Genesis Framework** as the parent theme (`Template: genesis`). Genesis must be in `wp-content/themes/genesis/`; it does not need to be active.

## Steps

1. **Install Genesis.** Put the Genesis parent theme in `wp-content/themes/genesis/`. The theme list in *Appearance*, *Themes* shows Genesis.
2. **Download the theme.** From the repository's [releases](https://github.com/UAMS-Web/baptisthealthuamsgme/releases), download the source archive of the release you want. It holds only the files the theme runs.
3. **Install the theme.** Upload the archive under *Appearance*, *Themes*, *Add New Theme*, *Upload Theme*, or unpack it into `wp-content/themes/`. The theme list shows **Baptist Health UAMS GME**.
4. **Activate it.** Select **Activate** on the theme. The site now uses the theme's header, menus and footer.
5. **Configure Genesis.** Under *Genesis*, *Theme Settings*, choose the default layout and the color style (see [Theme features](Theme-features.md#color-styles)).

**Note:** the theme's text domain is `utility-pro`, inherited from Utility Pro, so translations and some admin labels use that name.

## Next

- [Setting up the home page](Setting-up-the-home-page.md)
- [Widget areas](Widget-areas.md)
