<?php

namespace Bast1aan\DoctrineAdmin {
	
	use Doctrine\Common\Collections\Collection as DoctrineCollection;
	// use Doctrine\ORM\Mapping\ClassMetadata;
	use Doctrine\Common\Persistence\Mapping\ClassMetadata;
	use Iterator, ArrayAccess, Countable;
	
	class Collection implements Iterator, ArrayAccess, Countable {
		
		/**
		 * @var array
		 */
		private $collection;
		
		private $doctrineAdmin;
		
		/**
		 * @var integer
		 */
		private $i = 0;
		
		/**
		 * @var ClassMetadata
		 */
		private $classMetadata;
		
		public function __construct(DoctrineCollection $collection, $entityName, DoctrineAdmin $doctrineAdmin) {
			$this->collection = $collection->toArray();
			$this->doctrineAdmin = $doctrineAdmin;
			$this->classMetadata = $doctrineAdmin->getEntityManager()->getClassMetadata($entityName);
		}
		
		public function offsetExists($offset) {
			return isset($this->collection[$offset]);
		}

		/**
		 * @param int $offset
		 * @return Entity
		 */
		public function offsetGet($offset) {
			return new Entity($this->collection[$offset], $this->doctrineAdmin);
		}

		/**
		 * 
		 * @param int $offset
		 * @param Entity $value
		 */
		public function offsetSet($offset, $value) {
			if ($value instanceof Entity) {
				$this->collection[$offset] = $value->getOriginalEntity();
			} else {
				return new Exception('Item set on ' . __CLASS__ . ' must be instance of Entity');
			}
		}

		public function offsetUnset($offset) {
			unset($this->collection[$offset]);
		}
		
		/**
		 * 
		 * @return Entity
		 */
		public function current() {
			return new Entity($this->collection[$this->i], $this->doctrineAdmin);
		}

		public function key() {
			return $this->i;
		}

		public function next() {
			++$this->i;
		}

		public function rewind() {
			$this->i = 0;
		}

		public function valid() {
			return isset($this->collection[$this->i]);
		}
		
		public function count() {
			return count($this->collection);
		}
		
		public function getFieldNames() {
			return $this->classMetadata->getFieldNames();
		}
		
		public function getAssociationNames() {
			return $this->classMetadata->getAssociationNames();
		}
		
	}
}