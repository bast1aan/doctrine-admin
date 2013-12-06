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
	
	class CollectionAssociationProperty extends AssociationProperty implements Property, Iterator, Countable {
		
		/**
		 * @var array
		 */
		private $targetEntities;
		
		/**
		 *
		 * @var int
		 */
		private $i;
		
		/**
		 * 
		 * @param string $name
		 * @param Collection|array $values
		 * @param string $entityName
		 * @param DoctrineAdmin $doctrineAdmin
		 * @throws Exception
		 */
		public function __construct($name, $values, $entityName, DoctrineAdmin $doctrineAdmin) {
			if (is_array($values)) {
				$this->targetEntities = $values;
			} elseif ($values instanceof DoctrineCollection) {
				$this->targetEntities = $values->toArray();
			} else {
				return new Exception('Collection property value not an instance of Doctrine\Common\Collections\Collection and not an array');
			}
			parent::__construct($name, count($values) > 0 ? $values[0] : null, $entityName, $doctrineAdmin);

		}
		
		public function count() {
			count($this->targetEntities);
		}
		
		public function current() {
			return Entity::factory($this->targetEntities[$this->i], $this->doctrineAdmin);
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
			return isset($this->targetEntities[$this->i]);
		}
		
		/**
		 * @param Entity|object $entity
		 */
		public function add($entity) {
			if ($entity instanceof Entity) {
				$this->targetEntities[] = $entity->getOriginalEntity();
			} else {
				$this->targetEntities[] = $entity;
			}
		}
		
		public function clear() {
			$this->targetEntities = array();
		}
		
		/**
		 * @param Entity|object $entity
		 */
		public function remove($entity) {
			foreach($this->targetEntities as $key => $item) {
				if ($item == $entity) {
					unset($this->targetEntities[$key]);
					return;
				}
			}
		}
	}
}