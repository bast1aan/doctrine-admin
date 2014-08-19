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

	use Bast1aan\DoctrineAdmin\View\EntityList;
	use Doctrine\Common\Collections\Collection as DoctrineCollection;
	use Doctrine\Common\Persistence\Mapping\ClassMetadata;
	use Iterator, ArrayAccess, Countable;

	/**
	 * This class represents a collection of @see Entity objects
	 */
	class Collection implements Iterator, ArrayAccess, Countable {

		/**
		 * @var EntityList
		 */
		private $entityList;

		/**
		 * @var array
		 */
		protected $collection;
		
		/**
		 *
		 * @var DoctrineAdmin
		 */
		protected $doctrineAdmin;
		
		/**
		 * @var integer
		 */
		private $i = 0;
		
		/**
		 * @var ClassMetadata
		 */
		private $classMetadata;

		/**
		 * Construct the collection with the doctrine collection containing the
		 * entity objects, the name of the entity this collection is about, and
		 * the DoctrineAdmin instance holding the connection
		 *
		 * @param DoctrineCollection $collection
		 * @param $entityName
		 * @param DoctrineAdmin $doctrineAdmin
		 */
		public function __construct(DoctrineCollection $collection, $entityName, DoctrineAdmin $doctrineAdmin) {
			$this->collection = $collection->toArray();
			$this->doctrineAdmin = $doctrineAdmin;
			$this->classMetadata = $doctrineAdmin->getEntityManager()->getClassMetadata($entityName);
		}

		/**
		 * {@inheritdoc}
		 */
		public function offsetExists($offset) {
			return isset($this->collection[$offset]);
		}

		/**
		 * Return the Entity on this offset
		 *
		 * @param int $offset
		 * @return Entity
		 */
		public function offsetGet($offset) {
			return Entity::factory($this->collection[$offset], $this->doctrineAdmin);
		}

		/**
		 * Set an @see Entity on this collection on the specific offset.
		 *
		 * @param mixed $offset
		 * @param Entity $value
		 * @throws Exception
		 */
		public function offsetSet($offset, $value) {
			if ($value instanceof Entity) {
				$this->collection[$offset] = $value->getOriginalEntity();
			} else {
				throw new Exception('Item set on ' . __CLASS__ . ' must be instance of Entity');
			}
		}

		/**
		 * Delete the entity from this collection on the offset
		 *
		 * @param mixed $offset
		 */
		public function offsetUnset($offset) {
			unset($this->collection[$offset]);
		}
		
		/**
		 * Return the Entity of the current iterator position
		 *
		 * @return Entity
		 */
		public function current() {
			return Entity::factory($this->collection[$this->i], $this->doctrineAdmin);
		}

		/**
		 * Get the iterator position
		 *
		 * @return int
		 */
		public function key() {
			return $this->i;
		}

		/**
		 * Increment the iterator pointer
		 */
		public function next() {
			++$this->i;
		}

		/**
		 * Reset the iterator pointer
		 */
		public function rewind() {
			$this->i = 0;
		}

		/**
		 * Determine if the iterator position is still valid
		 *
		 * @return boolean
		 */
		public function valid() {
			return isset($this->collection[$this->i]);
		}

		/**
		 * Count the amount if Entities in this collection
		 *
		 * @return int
		 */
		public function count() {
			return count($this->collection);
		}

		/**
		 * Return the field names of the entity this collection holds
		 *
		 * @return array
		 */
		public function getFieldNames() {
			return $this->classMetadata->getFieldNames();
		}

		/**
		 * Return the association names of the entity this collection holds
		 * @return array
		 */
		public function getAssociationNames() {
			return $this->classMetadata->getAssociationNames();
		}

		/**
		 * Return the EntityList of this collection to be used in the view context.
		 *
		 * @return EntityList
		 */
		public function getEntityList() {
			if ($this->entityList == null) {
				$this->entityList = new EntityList($this, $this->doctrineAdmin->getView());
			}

			return $this->entityList;
		}

		/**
		 * Return the type of the entity this collection holds
		 *
		 * @return string
		 */
		public function getEntityName() {
			return $this->classMetadata->getName();
		}

	}
}