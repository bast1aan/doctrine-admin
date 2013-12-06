<?php
/*
 * Doctrine Admin
 * Copyright (C) 2013 Bastiaan Welmers, bastiaan@welmers.net
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */


namespace Bast1aan\DoctrineAdmin {
	
	use Doctrine\ORM\Mapping\ClassMetadata;
	use Bast1aan\DoctrineAdmin\View\View;
	use ArrayAccess;
	use Countable;
	
	class Entity implements ArrayAccess {
		/**
		 * @var object
		 */
		private $entity;
		
		/**
		 * @var ClassMetadata
		 */
		private $classMetaData;
		
		/**
		 *
		 * @var string[]
		 */
		private $fieldNames;
		
		/**
		 *
		 * @var string[]
		 */
		private $associationNames;
		
		/**
		 *
		 * @var string[]
		 */
		private $identifierNames;
		
		/**
		 * @var DoctrineAdmin
		 */
		private $doctrineAdmin;
		
		/**
		 * 
		 * @param object $entity
		 * @param DoctrineAdmin $da
		 * @return Entity
		 */
		public static function factory($entity, DoctrineAdmin $da) {
			$config = $da->getConfig();
			if ($config != null) {
				$daEntity = $config->getDoctrineAdminEntityByNativeEntity($entity);
			}
			if ($daEntity == null) {
				$daEntity = new Entity($entity);
			}
			$daEntity->setDoctrineAdmin($da);
			return $daEntity;
		}
		
		public function __construct($entity, DoctrineAdmin $doctrineAdmin = null) {
			$this->entity = $entity;
			if ($doctrineAdmin != null) {
				$this->setDoctrineAdmin($doctrineAdmin);
			}
		}
		
		public function setDoctrineAdmin(DoctrineAdmin $doctrineAdmin) {
			$this->doctrineAdmin = $doctrineAdmin;
			$this->classMetaData = $doctrineAdmin->getEntityManager()->getClassMetadata(get_class($this->entity));
			$this->fieldNames = $this->classMetaData->getFieldNames();
			$this->associationNames = $this->classMetaData->getAssociationNames();
			$this->identifierNames = $this->classMetaData->getIdentifierFieldNames();
		}
		
		/**
		 * @return string[]
		 */
		public function getAssociationNames() {
			return $this->associationNames;
		}
		
		/**
		 * @return string[]
		 */
		public function getFieldNames() {
			return $this->fieldNames;
		}
		
		/**
		 * @return string[]
		 */
		public function getIdentifierNames() {
			return $this->identifierNames;
		}
		
		public function offsetExists($offset) {
			return in_array($offset, $this->fieldNames) || in_array($offset, $this->associationNames);
		}

		public function offsetGet($offset) {
			return $this->getColumn($offset);
		}
		
		/**
		 * @param string $columnName
		 * @return Property
		 */
		public function getColumn($offset) {
			if ($this->offsetExists($offset)) {
				$clazz = $this->classMetaData->getReflectionClass();
				$prop = $clazz->getProperty($offset);
				$prop->setAccessible(true);
				$value = $prop->getValue($this->entity);
				if (in_array($offset, $this->fieldNames)) {
					$type = $this->classMetaData->getTypeOfField($offset);
					$fieldMapping = $this->classMetaData->getFieldMapping($offset);
					return new ScalarProperty($offset, $value, $fieldMapping['type'], $fieldMapping['length'], $this->doctrineAdmin);
				} elseif (in_array($offset, $this->associationNames)) {
					$targetEntityName = $this->classMetaData->getAssociationTargetClass($offset);
					if ($this->classMetaData->isCollectionValuedAssociation($offset)) {
						return new CollectionAssociationProperty($offset, $value, $targetEntityName, $this->doctrineAdmin);
					} elseif($this->classMetaData->isSingleValuedAssociation($offset)) {
						return new AssociationProperty($offset, $value, $targetEntityName, $this->doctrineAdmin);
					}
				}
			}
		}
		
		public function setColumn(Property $property) {
			$offset = $property->getName();
			if ($this->offsetExists($offset)) {
				$clazz = $this->classMetaData->getReflectionClass();
				$prop = $clazz->getProperty($offset);
				$prop->setAccessible(true);
				$prop->setValue($this->entity, $property->getValue());
			}
		}
		
		public function offsetSet($offset, $value) {
			
		}

		public function offsetUnset($offset) {
			
		}
		
		/**
		 * 
		 * @return object
		 */
		public function getOriginalEntity() {
			return $this->entity;
		}
		
		/**
		 * @return string
		 */
		public function getIdAsStr() {
			$idValues = array();
			// escape the - so it won't be recognized as a compound separator
			foreach($this->classMetaData->getIdentifierValues($this->entity) as $idValue)
				$idValues[] = str_replace('-', '--', $idValue);
			
			return implode('-', $idValues);
		}
		
		public function __toString() {
			// TODO make better
			return get_class($this->entity) . '_' . $this->getIdAsStr();
		}
		
		public function save() {
			$em = $this->doctrineAdmin->getEntityManager();
			$em->flush($this->entity);
		}
		
		public function delete() {
			$em = $this->doctrineAdmin->getEntityManager();
			$em->remove($this->entity);
		}
		
		/**
		 * @return DoctrineAdmin
		 */
		public function getDoctrineAdmin() {
			return $this->doctrineAdmin;
		}
		
		/**
		 * @return View
		 */
		public function getView() {
			return new View($this);
		}

	}
}