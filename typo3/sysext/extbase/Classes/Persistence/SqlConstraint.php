<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * The interface for constraints
 *
 * @package TYPO3
 * @subpackage Extbase
 * @version $Id$
 * @scope prototype
 */
class Tx_Extbase_Persistence_SqlConstraint implements Tx_Extbase_Persistence_ConstraintInterface {
	
	/**
	 * @var string
	 */
	protected $where;

	/**
	 * @var string
	 */
	protected $groupBy;

	/**
	 * @var string
	 */
	protected $orderBy;

	/**
	 * @var integer
	 */
	protected $limit;

	/**
	 * @var integer
	 */
	protected $offset;
	
	/**
	 * @var boolean
	 */
	protected $useEnableFields = TRUE;

	/**
	 * The SQL statement
	 *
	 * @var string
	 **/
	protected $statement;
		
	/**
	 * Sets the statement
	 *
	 * @param string $statement 
	 * @return void
	 */
	public function setStatement($statement) {
		if (!is_string($statement)) throw new InvalidArgumentException('The statement must be of type string, ' . gettype($statement) . ' given.', 1187951688);
		$this->statement = $statement;
	}
	
	/**
	 * Returns the statement
	 *
	 * @return string The SQL statement
	 */
	public function getStatement() {
		

	}
	
	/**
	 * Sets the WHERE Statement
	 *
	 * @param string $where The WHERE statement
	 * @return void
	 */
	public function setWhereClause($where) {
		if (!is_string($where)) throw new InvalidArgumentException('The where clause must be of type string, ' . gettype($where) . ' given.', 1187951688);
		$this->where = $where;
	}

	/**
	 * Sets the GROUP BY Statement
	 *
	 * @param string $groupBy The GROUP BY statement
	 * @return void
	 */
	public function setGroupByClause($groupBy) {
		if (!is_string($groupBy)) throw new InvalidArgumentException('The group by clause must be of type string, ' . gettype($groupBy) . ' given.', 1187951688);
		$this->groupBy = $groupBy;
	}

	/**
	 * Sets the ORDER BY Statement
	 *
	 * @param string $orderBy The ORDER BY statement
	 * @return void
	 */
	public function setOrderByClause($orderBy) {
		if (!is_string($orderBy)) throw new InvalidArgumentException('The order by clause must be of type string, ' . gettype($orderBy) . ' given.', 1187951688);
		$this->orderBy = $orderBy;
	}

	/**
	 * Sets the maximum size of the result set to limit.
	 *
	 * @param integer $limit
	 * @return void
	 */
	public function setLimit($limit) {
		if ($limit < 1 || !is_int($limit)) {
			throw new InvalidArgumentException('setLimit() accepts only integers greater than 0.', 1217244746);
		}
		$this->limit = $limit;
	}

	/**
	 * Sets the start offset of the result set to offset.
	 *
	 * @param integer $offset
	 * @return void
	 */
	public function setOffset($offset) {
		if ($offset < 0 || !is_int($offset)) {
			throw new InvalidArgumentException('setOffset() accepts only integers greater than or equal to 0.', 1217245454);
		}
		$this->offset = $offset;
	}
	
	/**
	 * The following condition array would find entities with description like the given keyword and
	 * name equal to "foo".
	 *
	 * <pre>
	 * array(
	 * 		array('blog_description LIKE ?', $keyword),
	 * 		'blogName' => 'Foo'
	 * 		)
	 * </pre>
	 *
	 * Note: The SQL part uses the database columns names, the query by example syntax uses
	 * the object property name (camel-cased, without underscore).
	 *
	 * @param Tx_Extbase_Persistence_Mapper_DataMap $dataMap The data map
	 * @param Tx_Extbase_Persistence_ConstraintInterface $constraint The constraint
	 * @return string The query where part for the class and given conditions
	 */
	public function generateWhereClause(Tx_Extbase_Persistence_Mapper_DataMap $dataMap, Tx_Extbase_Persistence_ConstraintInterface $constraint) {
		$where = '';
		if (is_array($constraint)) {
			$whereParts = array();
			foreach ($constraint as $key => $condition) {
				if (is_array($condition) && isset($condition[0])) {
					$sql = $this->replacePlaceholders($dataMap, $condition[0], array_slice($condition, 1));
					$whereParts[] = '(' . $sql . ')';
				} elseif (is_string($key)) {
					$sql = $this->generateWhereClauseByExample($dataMap, $key, $condition);
					if (strlen($sql) > 0) {
						$whereParts[] = '(' . $sql . ')';
					}
				}
			}
			$where = (implode(' AND ', $whereParts));
		} if (is_string($constraint)) {
			$where = $constraint;
		}
		return $where;
	}
	
	/**
	 * Replace query placeholders in a query part by the given
	 * parameters.
	 *
	 * @param string $queryPart The query part with placeholders
	 * @param array $parameters The parameters
	 *
	 * @return string The query part with replaced placeholders
	 */
	protected function replacePlaceholders(Tx_Extbase_Persistence_Mapper_DataMap $dataMap, $queryPart, $parameters) {
		$sql = $queryPart;
		foreach ($parameters as $parameter) {
			$markPosition = strpos($sql, '?');
			if ($markPosition !== FALSE) {
				$sql = substr($sql, 0, $markPosition) . $dataMap->convertPropertyValueToFieldValue($parameter) . substr($sql, $markPosition + 1);
			}
		}
		return $sql;
	}
	
	
	/**
	 * Get a where part for an example condition (associative array). This also works
	 * for nested conditions.
	 *
	 * @param Tx_Extbase_Persistence_Mapper_DataMap $dataMap The data map
	 * @param array $propertyName The property name
	 * @param array $example The example condition
	 *
	 * @return string The where part
	 */
	protected function generateSqlByExample(Tx_Extbase_Persistence_Mapper_DataMap $dataMap, $propertyName, $example) {
		$columnMap = $dataMap->getColumnMap($propertyName);
		$sql = '';
		if (empty($columnMap)) {
			throw new Tx_Extbase_Persistence_Exception_InvalidPropertyType("No columnMap for $propertyName", 1240305176);
		}
		if (!is_array($example)) {
			$column = $dataMap->getTableName() . '.' . $columnMap->getColumnName();
			$sql = $column . ' = ' . $dataMap->convertPropertyValueToFieldValue($example);
		} else {
			$childDataMap = $this->dataMapper->getDataMap($columnMap->getChildClassName());
			$sql = $this->generateWhereClause($childDataMap, $example);
		}
		return $sql;
	}
	
}

?>