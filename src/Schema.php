<?php
/**
 * @package   business-central-sdk
 * @author    Morten Harders 🐢
 * @copyright 2020
 */

namespace BusinessCentral;


use BusinessCentral\Schema\Action;
use BusinessCentral\Schema\ComplexType;
use BusinessCentral\Schema\EntitySet;
use BusinessCentral\Schema\EntityType;
use Illuminate\Support\Collection;

class Schema
{
    protected $version;

    const GUID_FORMAT = '/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i';

    /** @var Collection|EntityType[] */
    protected $entity_types;
    /** @var Collection|EntitySet[] */
    protected $entity_sets;
    /** @var Collection|ComplexType[] */
    protected $complex_types;
    /** @var Collection|Action[] */
    protected $actions;

    /** @var array */
    protected $raw, $overrides;

    public function __construct(array $json)
    {
        $this->version = $json['@attributes']['Version'];
        $this->raw     = $json;

        $this->entity_types  = new Collection();
        $this->entity_sets   = new Collection();
        $this->complex_types = new Collection();
        $this->actions       = new Collection();

        $this->loadOverrides();

        $this->propagate();
    }

    protected function propagate()
    {
        foreach ($this->raw['DataServices']['Schema']['ComplexType'] as $type) {
            $this->complex_types[$type['@attributes']['Name']] = new ComplexType($type, $this);
        }

        foreach ($this->raw['DataServices']['Schema']['EntityType'] as $type) {
            $this->entity_types[$type['@attributes']['Name']] = new EntityType($type, $this);
        }

        foreach ($this->raw['DataServices']['Schema']['EntityContainer']['EntitySet'] as $set) {
            $this->entity_sets[$set['@attributes']['Name']] = new EntitySet($set, $this);
        }

        foreach ($this->raw['DataServices']['Schema']['Action'] as $action) {
            $this->actions[$action['@attributes']['Name']] = new Action($action, $this);
        }
    }

    // region EntityTypes

    public function getEntityTypes()
    {
        return $this->entity_types;
    }

    public function hasEntityType(string $type)
    {
        return isset($this->entity_types[$type]);
    }

    /**
     * @param string $type
     *
     * @return EntityType|null
     * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
     */
    public function getEntityType(string $type)
    {
        return $this->entity_types[static::getType($type)];
    }

    public function getEntityTypeBySet(string $set)
    {
        return $this->getEntitySet(static::getType($set))->getEntityType();
    }

    // endregion

    // region EntitySets

    public function getEntitySets()
    {
        return $this->entity_sets;
    }

    public function hasEntitySet(string $set)
    {
        return isset($this->entity_sets[$set]);
    }

    public function getEntitySet(string $set)
    {
        return $this->entity_sets[$set] ?? null;
    }

    public function getEntitySetByType(string $type)
    {
        return $this->entity_sets->first(function (EntitySet $entity_set) use ($type) {
            return $entity_set->getEntityType()->name === $type;
        });
    }

    // endregion

    // region ComplexTypes

    public function hasComplexType(string $type)
    {
        return isset($this->complex_types[$type]);
    }

    public function getComplexType(string $type)
    {
        return $this->complex_types[$type] ?? null;
    }

    // endregion

    public static function getType(string $type)
    {
        if (preg_match('/Collection\(.+\)/', $type)) {
            preg_match('/Collection\((.+)\)/', $type, $matches);
            $type = $matches[1] ?? $type;
        }

        $type = str_replace('Microsoft.NAV.', '', $type);

        return $type;
    }

    protected function loadOverrides()
    {
        $file = __DIR__ . '/../schema_overrides.json';

        return $this->overrides = json_decode(file_get_contents(realpath($file)), true);
    }

    public function hasOverrides(string $type, string $property)
    {
        return ! empty($this->getOverrides($type, $property));
    }

    public function getOverrides(string $type, string $property)
    {
        return $this->overrides[$type][$property] ?? [];
    }

    public function propertyIs(string $model, string $property, string $attribute)
    {
        return $this->overrides[$model]["properties"][$property][$attribute] ??
               $this->overrides['__always']["properties"][$property][$attribute] ?? false;
    }

    public function propertyIsGuarded(string $model, string $property)
    {
        return $this->propertyIs($model, $property, 'guarded');
    }

    public function propertyIsReadOnly(string $model, string $property)
    {
        return $this->propertyIs($model, $property, 'readOnly');
    }

    public function propertyIsFillable(string $model, string $property)
    {
        return ! $this->propertyIs($model, $property, 'readOnly');
    }
}