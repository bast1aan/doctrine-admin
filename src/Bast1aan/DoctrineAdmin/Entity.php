<?php

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
				
		public function __construct($entity, DoctrineAdmin $doctrineAdmin) {
			$this->entity = $entity;
			$this->doctrineAdmin = $doctrineAdmin;
			$this->classMetaData = $doctrineAdmin->getEntityManager()->getClassMetadata(get_class($entity));
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
			if (in_array($offset, $this->fieldNames) || in_array($offset, $this->associationNames))
				return true;
			else
				return false;
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

	}
}