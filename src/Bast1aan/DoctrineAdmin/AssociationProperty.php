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
		 * @var Entity 
		 */
		private $entity;
		
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
		 * @param Entity $entity
		 */
		public function __construct($name, $value, Entity $entity) {
			$this->name = $name;
			$this->value = $value;
			$this->entity = $entity;
			$this->entityName = $entity->getClassMetaData()->getAssociationTargetClass($name);
			$this->doctrineAdmin = $entity->getDoctrineAdmin();
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
		
		public function isNullable() {
			$associationMapping = $this->entity->getClassMetaData()->getAssociationMapping($this->getName());
			// single-associatons are default nullable, except when explicitly false.
			// See http://docs.doctrine-project.org/en/latest/reference/annotations-reference.html#annref-joincolumn
			if (isset($associationMapping['joinColumns']) &&
				isset($associationMapping['joinColumns']['nullable']) &&
				$associationMapping['joinColumns']['nullable'] === false)
				return false;
			else
				return true;
		}

		/**
		 * @return Entity
		 */
		public function getEntity()	{
			return $this->entity;
		}

		/**
		 * Determines if this association is read-only
		 * For now, a association is readonly if it is the inverse side of the association.
		 * Doctrine won't save the changes in this case.
		 *
		 * @return boolean
		 */
		public function isReadOnly() {
			return $this->entity->getClassMetaData()->isAssociationInverseSide($this->name);
		}

	}
}