<?php
namespace SCL\HTML;

class NamePrettifier
{
    /** @var array */
    protected $aliases;

    public function __construct(array $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function prettifyLabelName($fieldName)
    {
        return ucfirst(
            strtolower(
                preg_replace('/([A-Z0-9])/', ' \1', $this->stripId($this->getAlias(lcfirst($fieldName))))
            )
        );
    }

    private function stripId($fieldName)
    {
        $stripped = $this->getEntityName($fieldName);

        return $stripped === '' ? $fieldName : $stripped;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getEntityName($name)
    {
        return (preg_match('/^(.+)Id$/', $name, $matches) === 1) ? $matches[1] : '';
    }

    private function getAlias($name)
    {
        return $this->lookup($this->aliases, $name, $name);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    private function lookup(array $table, $name, $default)
    {
        if (!array_key_exists($name, $table)) {
            return $default;
        }

        return $table[$name];
    }
}
