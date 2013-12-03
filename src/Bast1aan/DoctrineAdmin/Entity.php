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
	
	use Doctrine\Common\Persistence\Mapping\ClassMetadata;
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
		
		private $fieldNames;
		
		private $associationNames;
		
		private $identifierNames;
		
		/**
		 * @var DoctrineAdmin
		 */
		private $doctrineAdmin;
		
		/**
		 * 
		 * @param Config $config
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
		
		public function getAssociationNames() {
			return $this->associationNames;
		}
		
		public function getFieldNames() {
			return $this->fieldNames;
		}
		
		public function offsetExists($offset) {
			return in_array($offset, $this->fieldNames) || in_array($offset, $this->associationNames);
		}

		public function offsetGet($offset) {
			return $this->getColumn($offset);
		}
		
		/**
		 * @param string $columnName
		 * @return Column
		 */
		public function getColumn($offset) {
			if ($this->offsetExists($offset)) {
				$clazz = $this->classMetaData->getReflectionClass();
				$prop = $clazz->getProperty($offset);
				$prop->setAccessible(true);
				$value = $prop->getValue($this->entity);
				$type = $this->classMetaData->getTypeOfField($offset);
				if (in_array($offset, $this->fieldNames)) {
					return new ScalarProperty($offset, $value, $type, $this->doctrineAdmin);
				} elseif (is_array($value) || $value instanceof Countable) {
					return new CollectionAssociationProperty($offset, $value, $type, $this->doctrineAdmin);
				} else {
						//				print $value instanceof Countable;
					return new AssociationProperty($offset, $value, $type, $this->doctrineAdmin);
				}
				
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
		
		public function __toString() {
			// TODO make better
			return get_class($this->entity) . '_' . implode('-', $this->classMetaData->getIdentifierValues($this->entity));
		}
		
		public function save() {
			$em = $this->doctrineAdmin->getEntityManager();
			$em->flush($this->entity);
		}
		
		public function delete() {
			$em = $this->doctrineAdmin->getEntityManager();
			$em->remove($this->entity);
		}

	}
}