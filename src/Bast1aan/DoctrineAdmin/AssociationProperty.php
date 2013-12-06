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
	class AssociationProperty implements Property {
		
		/**
		 *
		 * @var name of the association
		 */
		private $name;
		
		/**
		 *
		 * @var object target entity
		 */
		private $value;
		
		/**
		 * Classname of the entity of the association
		 * @var string 
		 */
		private $entityName;
		
		/**
		 * @var DoctrineAdmin
		 */
		protected $doctrineAdmin;
		
		/**
		 * 
		 * @param string $name
		 * @param object $value
		 * @param string $entityName
		 * @param DoctrineAdmin $doctrineAdmin
		 */
		public function __construct($name, $value, $entityName, DoctrineAdmin $doctrineAdmin) {
			$this->name = $name;
			$this->value = $value;
			$this->entityName = $entityName;
			$this->doctrineAdmin = $doctrineAdmin;
		}

		/**
		 * @return string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * @return Entity
		 */
		public function getValue() {
			if ($this->value != null)
				return Entity::factory($this->value, $this->doctrineAdmin);
		}
		
		/**
		 * @param Entity|object $value the entity
		 */
		public function setValue($value) {
			if ($value instanceof Entity)
				$this->value = $value->getOriginalEntity();
			else
				$this->value = $value;
		}
		
		
		public function getEntityName() {
			return $this->entityName;
		}
		
		public function isNull() {
			return $this->value === null;
		}

		
		/**
		 * 
		 * @obsolete
		 */
		public function __toString() {
			return (string) $this->getAsEntity();
		}
		
		/**
		 * 
		 * @return Entity
		 * @obsolete
		 */
		public function getAsEntity() {
			return $this->getValue();
		}
	}
}