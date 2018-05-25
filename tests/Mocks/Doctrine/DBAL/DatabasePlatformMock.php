<?php
/**
 * Class DatabasePlatformMock
 *
 * @package Kachit\Database\Mocks\Doctrine\DBAL
 * @author Kachit
 */
namespace Kachit\Database\Mocks\Doctrine\DBAL;

use Doctrine\DBAL\DBALException;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class DatabasePlatformMock extends AbstractPlatform
{
    private $_sequenceNextValSql = "";
    private $_prefersIdentityColumns = true;
    private $_prefersSequences = false;
    /**
     * @override
     */
    public function prefersIdentityColumns()
    {
        return $this->_prefersIdentityColumns;
    }
    /**
     * @override
     */
    public function prefersSequences()
    {
        return $this->_prefersSequences;
    }
    /** @override */
    public function getSequenceNextValSQL($sequenceName)
    {
        return $this->_sequenceNextValSql;
    }
    /** @override */
    public function getBooleanTypeDeclarationSQL(array $field) {}
    /** @override */
    public function getIntegerTypeDeclarationSQL(array $field) {}
    /** @override */
    public function getBigIntTypeDeclarationSQL(array $field) {}
    /** @override */
    public function getSmallIntTypeDeclarationSQL(array $field) {}
    /** @override */
    protected function _getCommonIntegerTypeDeclarationSQL(array $columnDef) {}
    /** @override */
    public function getVarcharTypeDeclarationSQL(array $field) {}
    /** @override */
    public function getClobTypeDeclarationSQL(array $field) {}
    /* MOCK API */
    public function setPrefersIdentityColumns($bool)
    {
        $this->_prefersIdentityColumns = $bool;
    }
    public function setPrefersSequences($bool)
    {
        $this->_prefersSequences = $bool;
    }
    public function setSequenceNextValSql($sql)
    {
        $this->_sequenceNextValSql = $sql;
    }
    public function getName()
    {
        return 'mock';
    }
    protected function initializeDoctrineTypeMappings() {
    }
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
    }
    /**
     * Gets the SQL Snippet used to declare a BLOB column type.
     */
    public function getBlobTypeDeclarationSQL(array $field)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    /**
     * @param string      $table
     * @param string|null $database
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException If not supported on this platform.
     */
    public function getListTableColumnsSQL($table, $database = null)
    {
        return '';
    }
}
