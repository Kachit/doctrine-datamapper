<?php
namespace Kachit\Silex\Database\Column;

class Handler
{
    /**
     * @var string
     */
    private $columnFieldName = 'field';

    /**
     * @param array $columns
     * @return array
     */
    public function handle(array $columns)
    {
        $result = [];
        foreach ($columns as $values) {
            foreach ($values as $name => $value) {
                if (strtolower($name) == $this->columnFieldName) {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
}