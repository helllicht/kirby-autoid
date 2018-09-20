<?php
/**
 *
 * AutoID Plugin for Kirby 2
 *
 * @version   1.1.0
 * @author    Helllicht medien GmbH <http://helllicht.com>
 * @copyright Helllicht medien GmbH <http://helllicht.com>
 * @link      https://github.com/helllicht/kirby-autoid
 * @license   MIT <http://opensource.org/licenses/MIT>
 */

class AutoIdPlugin
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string id or hash
     */
    protected $fieldType;

    public function __construct($fieldName, $fieldType)
    {
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
    }

    public function onPageCreate($page)
    {
        if ($page->{$this->fieldName}()->exists()) {
            $page->update(array(
                $this->fieldName => $this->getUniqueAutoId()
            ));
        }
    }

    public function onPageUpdate($page)
    {
        if (
            $page->{$this->fieldName}()->exists() &&
            $page->{$this->fieldName}()->isEmpty()
        ) {
            $page->update(array(
                $this->fieldName => $this->getUniqueAutoId()
            ));
        }
    }

    /**
     * @return string
     */
    protected function getNewAutoId($offset = 0)
    {
        if ($this->fieldType === 'id') {

          // get latest ID, numbers only!
            $fieldName = $this->fieldName;
            $filteredPages = site()->pages()->index()->filter(function ($p) use ($fieldName) {
                $val = $p->{$fieldName}()->value();

                return is_numeric($val);
            })->sortBy($fieldName, 'desc');

            if ($filteredPages->count() == 0) {
                $nextId = 1 + $offset;
            } else {
                $latestId = intval($filteredPages->first()->{$this->fieldName}()->value());
                $nextId = $latestId + 1 + $offset;
            }

            return (string) $nextId;
        } else { // defaults to hash

            // Get Elements
            $elements[] = microtime();
            $elements[] = session_id();
            $elements[] = $offset;

            // Concatenate Elements
            $idString = implode('', $elements);

            // Build Hash
            $idHash = md5($idString);

            return $idHash;
        }
    }

    /**
     * @return string
     */
    public function getUniqueAutoId()
    {
        for ($offset = 0; $offset <= 10; $offset++) {
            // Get new Id
            $autoid = $this->getNewAutoId($offset);

            // Check if id is existing
            $existingId = site()->pages()->index()->filterBy($this->fieldName, $autoid)->count();

            // Return unique id
            if ($existingId == 0) {
                return $autoid;
            }

            // Try again
        }

        throw new Exception('Fatal Error: Cannot create new id. Tried 10 offsets with no luck.');
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }
}

// Allow to overwrite field name in config.php with c::set('autoid.name', 'NameMyField');
$fieldName = c::get('autoid.name', 'autoid');

// Allow to overwrite field type to 'id' instead of 'hash' with c::set('autoid.type', 'id');
$fieldType = c::get('autoid.type', 'hash');

// create a instance and hook into kirby
$plugin = new AutoIdPlugin($fieldName, $fieldType);

// Set id for new pages
kirby()->hook('panel.page.create', function ($page) use ($plugin) {
    return $plugin->onPageCreate($page);
});

// Set id for existing pages (if added later)
kirby()->hook('panel.page.update', function ($page) use ($plugin) {
    // trigger update only with version 2.2.2 or higher
    if (version_compare(panel()->version(), '2.2.2', '>=')) {
        return $plugin->onPageUpdate($page);
    } else {
        // do nothing, because of a kirby bug: https://github.com/getkirby/panel/issues/667
    }
});

// Register Custom Form Field
$kirby->set('field', 'autoid', __DIR__ . '/fields/autoid');
