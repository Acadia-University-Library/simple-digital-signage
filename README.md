# Simple Digital Signage

This application is a simple, browser-based digital signage utility, built using everyday web technologies. It can be hosted anywhere that supports PHP 7. *(Security note: `glob()`, `get_file_contents()`, `put_file_contents()` and `header()` functions are used.)*

## Configuration

A series of values are defined at the top of `template.php` which can be changed to configure the display for your local environment.

* `MEDIA_DIRECTORY` = (string, default "./media") Path to the directory containing media files for display. Location must be accessible by URL.
* `MEDIA_CACHE` = (string, default "media_cache.txt") Path to the media cache file. Web server must be permitted to read from and write to this location.
* `MEDIA_CACHE_TIMEOUT_SECONDS` = (integer, default 900) Length of time before which the media directory is reprocessed and cache updated.
* `DISPLAY_REFRESH_SECONDS` = (integer, default 30) Lenght of time for which a screen content is displayed if not otherwise specified by the media item itself.
* `DISPLAY_CSS` = (string, default "template.css") Display template stylesheet.
* `DISPLAY_HEAD` = (string, default "template_head.html") Additional tags or javascript that should be added to the display template's `<head>` block.
* `DISPLAY_TITLE` = (string, default "Simple Digital Signage") Display template title: `<title>X</title>`.
* `DISPLAY_LANGUAGE` = (string, default "en") Display template language: `<html lang="X">`.
* `DISPLAY_CHARACTER_SET` = (string, default "utf-8") Display template character set: `<meta charset="X">`.

## Media Directory

In its simplest sense, you can copy a few images into the location defined by `MEDIA_DIRECTORY` (above) and be good to go, but to maximize the signage utility's potential, you should adhere to a handful of naming conventions and media type syntax instructions.

### Naming Conventions

`{SEQUENCE (optional)}TITLE/DESCRIPTION{.REFRESH_TIME(optional)}.TYPE`

#### Filename Example

`301-psy-gangnam_style.252.youtube`

* `301-` = (string) Display sequence.
* `psy-gangnam_style` = (string) Media file description.
* `.252` = ("." + integer) Length of time, in seconds, for which this media item is displayed. This value overrides `DISPLAY_REFRESH_SECONDS` from the global configuration.
* `.youtube` = ("." + string) Type of media represented by this file.

#### Directory Example 1

The filenames in the media directory list below have been prefixed with sequence numbers to specify where each item will appear in the display order.  

```
001-hello_world.html
101-mallard_duck_public_domain.jpg
201-merriam-webster_word_of_the_day.url
301-psy-gangnam_style.252.youtube
401-phpinfo.php
501-lorem_ipsum.txt
```

#### Directory Example 2

Using the same set of filenames without an explicit sequence identifier will display them in an order determined by a simple ascending alphabetic sort.

```
hello_world.html
lorem_ipsum.txt
mallard_duck_public_domain.jpg
merriam-webster_word_of_the_day.url
phpinfo.php
psy-gangnam_style.252.youtube
```

## Supported File Types/Extensions

Unless otherwise noted, the contents of each media file will be embedded as-is into the display template.

* `gif`, `jpg`, `png` = Images.

* `html` = Basic inline HTML. No `<html>`, `<head>` or `<body>` tags; however, `<style>` is permitted.

* `php` = PHP code. Included via `include()` function. (Handled the same as the aforementioned `html` type.)

* `txt` = Plain-text wrapped with `<div class="container"><pre>...</pre></div>`.

* `url` = URL of another website. Embedded using `<iframe>`.

* `youtube` = ID string of a YouTube video. (eg. dQw4w9WgXcQ) Do not use the full URL or embed code. The video will auto-play with no visible controls, captions (if available) enabled, and no tracking cookies. If you require different player settings, copy and paste the appropriate embed code in a `html`-type media file instead.

## Running the Display

Open a web browser and go to `http(s)://your_digital_signage_location/template.php`. The signage display will begin automatically. For best results, put the web browser in fullscreen mode (press "F11" on most computers).

If you wish to view a list of all media in the display queue, go to `./template.php?id=-1`. This also flushes the media directory cache.

If you wish to flush the media directory cache and then immediately resume the signage display, go to `./template.php?id=999`.

### Adding/Removing Media

Files that have been newly added to the media directory will be automatically integrated into the display order the next time the media cache is rebuilt whether via timeout (config: `MEDIA_CACHE_TIMEOUT_SECONDS`) or URL request.

Files that are deleted or cannot otherwise be loaded from the media directory will be skipped and trigger an immediate cache rebuild.

With these two points in mind, you do not need to manually restart the signage display after media changes take place.

## License

This utility is licensed under the GNU Public License (GPL) version 3. Refer to [`LICENSE.md`](LICENSE.md) for the complete text.

## Copyright & Contact

Copyright (C) 2022  Vaughan Memorial Library, Acadia University
* https://library.acadiau.ca
* library-systems@acadiau.ca
