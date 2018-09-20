# AutoID Plugin for Kirby CMS

![Screenshot](plugin-logo.png?raw=true)

AutoID is a plugin for [Kirby 2](http://getkirby.com/) wich generates unique ids (numeric or hash) for every page.

We love Kirby and its database-less nature. One drawback though is that you don't have unique page ids.

But sometimes this can be very helpful if you need to reference pages to each other or generally need a way to uniquely identify pages other than by name or url (which may change). This is why we developed this plugin.

![Screenshot](screenshot.png?raw=true)

**License:** [MIT](http://opensource.org/licenses/MIT)

## Installation

### Manually (Copy & Paste)

Add a `plugins` folder inside your `site` directory of your kirby installation, if not already existing.

Then download the zip file and copy all its contents into an `autoid` folder inside the `plugins` directory. Your folder structure should now look like this:

```yaml
site/
  plugins/
    autoid/
      autoid.php
      ...
```

### Git Submodule

Alternatively, you can add the plugin as a git submodule in order to make future updates of the plugin quick and easy.

```bash
$ cd your/project/directory
$ git submodule add https://github.com/helllicht/kirby-autoid.git site/plugins/autoid
```

## Usage

In your blueprint, add a new field and use `autoid` as the field name. This way the plugin knows on which field to act on. As field type set `autoid` to use the custom AutoID field which comes with the plugin.

```yaml
fields:
  autoid:
    type: autoid
```

*Recommended Configuration*

Now, the plugin creates a unique id for each new page created, which is stored inside the field. It also works with existing pages, all you need to do is to open the page in the panel and hit save once.

### Example Use Cases

#### Default Usage In Your Blueprint

Let's say you want to have a field that allows you to add related projects to a project page. Normally you would query each sibling and reference them by their name/url/uid. But what if they names change? You would need to update each reference individually.

AutoIDs to the rescue! You can use the `autoid` field to uniquely reference the projects. This way even if the project names/urls change, the references won't break.

```yaml
fields:
  relatedprojects:
    label: Related Projects
    type: checkboxes
    options: query
      query:
        fetch: siblings
        value: '{{autoid}}'
        text: '{{title}}'
```

#### Creating An AutoId In Your Code

Maybe you need to create an autoId within your code itself, for example if you are importing content or you want to create an autoId for existing content. Then you have access to the now public method `getUniqueAutoId()` after instantiating the `AutoIdPlugin`class, too.

```
$autoId = new AutoIdPlugin('autoid', c::get('autoid.type', 'id'));
return $autoId->getUniqueAutoId();
```
*Example programmatic usage*

## Options

### Field Name

Some of you might want to have a custom name for your autoid field. You can override the name inside the `site/config.php` file of your Kirby installation.

```php
c::set('autoid.name', 'yourcustomfieldname');
```

This allows you to use `yourcustomfieldname` as the field name for your autoid field.

### Type

**By default**, AutoID now uses unique **md5 hashes** (microtimestamp + session id) to make sure each ID is unique. **This is our recommended method.**

However, for those of you who don't want unreadable hashes and prefer more readable numeric ids, AutoID offers this method as well. You can set it in your config.

**Please note:** This method is *only* recommended if you're the only contributor to the project. If you're working in a larger team working in parallel in local repositories, you might run into problems using numeric ids as pages will end up getting the same ids.

```php
c::set('autoid.type', 'hash');
```

This will also work when switching back and forth between the regular numeric id and the hashes. **Please note** that your existing ids won't change, so you might end up having both numeric *and* hashed ids.

---

Do you have feature suggestions or want to help improving the plugin? Feel free to contribute!

### Credits

[\#madebyhelllicht](http://helllicht.com) with ♥ in Groß-Gerau
