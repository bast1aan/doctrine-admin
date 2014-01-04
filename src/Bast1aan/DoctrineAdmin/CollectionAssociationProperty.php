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
	use Iterator;
	use Doctrine\Common\Collections\Collection as DoctrineCollection;
	
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
		 * 
		 * @param string $name
		 * @param Collection|array $values
		 * @param Entity $entity
		 * @throws Exception
		 */
		public function __construct($name, $values, Entity $entity) {
			if ($values instanceof DoctrineCollection) {
				$this->targetEntities = $values;
				$this->targetEntitiesKeys = $values->getKeys();
			} else {
				return new Exception('Collection property value not an instance of Doctrine\Common\Collections\Collection');
			}
			parent::__construct($name, count($values) > 0 ? $values[0] : null, $entity);

		}

		public function count() {
			$this->targetEntities->count();
		}
		
		public function current() {
			return Entity::factory($this->targetEntities->get($this->targetEntitiesKeys[$this->i]), $this->doctrineAdmin);
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
			return isset($this->targetEntitiesKeys[$this->i]);
		}
		
		/**
		 * @param Entity|object $entity
		 */
		public function add($entity) {
			error_log('add ' . implode('-' . $entity->getIdentifierValues()));
			if ($entity instanceof Entity) {
				$this->targetEntities->add($entity->getOriginalEntity());
			} else {
				$this->targetEntities->add($entity);
			}
			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}
		
		public function clear() {
			$this->targetEntities->clear();
			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}
		
		/**
		 * @param Entity|object $entity
		 */
		public function remove($entity) {
			error_log('remove ' . implode('-' . $entity->getIdentifierValues()));
			if ($entity instanceof Entity) {
				$this->targetEntities->removeElement($entity->getOriginalEntity());
			} else {
				$this->targetEntities->removeElement($entity);
			}
			$this->targetEntitiesKeys = $this->targetEntities->getKeys();
			$this->rewind();
		}
		
		/**
		 * @return object[]
		 */
		public function toArray() {
			return $this->targetEntities->toArray();
		}
		
		public function isNullable() {
			return false;
		}

		/**
		 * @return DoctrineCollection
		 */
		public function getTargetEntities() {
			return $this->targetEntities;
		}

	}
}