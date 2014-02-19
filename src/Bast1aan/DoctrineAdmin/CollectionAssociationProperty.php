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
	use Countable;
	use Doctrine\Common\Collections\ArrayCollection;
	use Iterator;
	use Doctrine\Common\Collections\Collection as DoctrineCollection;

	/**
	 * CollectionAssociationProperty represents a property of an @see Entity
	 * containing an association with another entity concerning a to-many relation,
	 * thus a collection of entities.
	 */
	class CollectionAssociationProperty extends AssociationProperty implements Iterator, Countable {
		
		/**
		 * @var DoctrineCollection
		 */
		private $targetEntities;

		/**
		 * @var mixed[]
		 */
		private $targetEntitiesKeys;

		/**
		 *
		 * @var int
		 */
		private $i;
		
		/**
		 * Construct the CollectionAssociationProperty, with the field name, the
		 * collection containing the associated entities, and the @see Entity this
		 * property belongs to
		 *
		 * @param string $name
		 * @param Collection|array $values
		 * @param Entity $entity
		 * @throws Exception
		 */
		public function __construct($name, $values, Entity $entity) {
			if ($values instanceof DoctrineCollection) {
				$this->targetEntities = $values;
			} elseif ($values == null) {
				$this->targetEntities = new ArrayCollection();
			} else {
				throw new Exception('Collection property value not an instance of Doctrine\Common\Collections\Collection');
			}
			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			parent::__construct($name, count($values) > 0 ? $values[0] : null, $entity);
		}

		/**
		 * Return the ammount of items in the collections
		 *
		 * @return int
		 */
		public function count() {
			$this->targetEntities->count();
		}

		/**
		 * return the entity where the iterator currently points to
		 *
		 * @return Entity
		 */
		public function current() {
			return Entity::factory($this->targetEntities->get($this->targetEntitiesKeys[$this->i]), $this->doctrineAdmin);
		}

		/**
		 * Return the iterator pointer
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

		public function valid() {
			return isset($this->targetEntitiesKeys[$this->i]);
		}
		
		/**
		 * Add an entity to the collection
		 *
		 * @param Entity|object $entity
		 */
		public function add($entity) {
			if ($entity instanceof Entity) {
				$entity = $entity->getOriginalEntity();
			}

			$this->targetEntities->add($entity);

			$this->addToOwningSide($entity);

			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}

		/**
		 * Clear the collection by removing all entities it contains
		 */
		public function clear() {
			foreach($this->targetEntities as $targetEntity) {
				$this->unlinkFromOwningSide($targetEntity);
			}
			$this->targetEntities->clear();
			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}
		
		/**
		 * Remove an entity from the collection
		 *
		 * @param Entity|object $entity
		 */
		public function remove($entity) {
			if ($entity instanceof Entity) {
				$entity = $entity->getOriginalEntity();
			}
			$this->unlinkFromOwningSide($entity);

			$this->targetEntities->removeElement($entity);

			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}

		/**
		 * Check if the collection contains an entity.
		 *
		 * @param Entity|object $entity
		 * @return bool
		 */
		public function contains($entity) {
			return $this->targetEntities->contains($entity instanceof Entity ? $entity->getOriginalEntity() : $entity);
		}
		
		/**
		 * Return an array of entities the collection is containing
		 *
		 * @return object[]
		 */
		public function toArray() {
			return $this->targetEntities->toArray();
		}

		/**
		 * An entity collection can never be null, so returns always false.
		 *
		 * @return boolean
		 */
		public function isNullable() {
			return false;
		}

		/**
		 * An entity collection can never be null, so returns always false.
		 *
		 * @return boolean
		 */
		public function isNull() {
			return false;
		}

		/**
		 * Return the internal collection of entities
		 *
		 * @return DoctrineCollection
		 */
		public function getTargetEntities() {
			return $this->targetEntities;
		}

		private function checkInverseSide() {

		}

	}
}