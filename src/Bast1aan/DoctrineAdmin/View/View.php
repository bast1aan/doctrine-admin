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

namespace Bast1aan\DoctrineAdmin\View {
	use Bast1aan\DoctrineAdmin;
	use Doctrine\DBAL\Types\Type;
	class View {
		
		/**
		 * @var DoctrineAdmin\Entity
		 */
		private $entity;
		
		/**
		 * 
		 * @param DoctrineAdmin\Entity $entity
		 */
		public function __construct(DoctrineAdmin\Entity $entity) {
			$this->entity = $entity;
		}
		
		/**
		 * @return Form
		 */
		public function getForm() {
			return new Form($this);
		}
		
		/**
		 * 
		 * @param DoctrineAdmin\Property $property
		 * @param Form $form
		 * @return FormElement
		 */
		public function getFormElement(DoctrineAdmin\Property $property, Form $form) {
			if ($property instanceof DoctrineAdmin\ScalarProperty) {
				return $this->getScalarFormElement($property, $form);
			} elseif ($property instanceof DoctrineAdmin\AssociationProperty) {
				return $this->getAssociationFormElement($property, $form);
			}
		}
		
		/**
		 * 
		 * @param DoctrineAdmin\ScalarProperty $property
		 * @param Form $form
		 * @return FormElementScalar
		 */
		protected function getScalarFormElement(DoctrineAdmin\ScalarProperty $property, Form $form) {
			switch($property->getType()) {
				case Type::DATE:
					return new FormElementDate($property, $form);
				case Type::DATETIME:
				case Type::DATETIMETZ:
					return new FormElementDateTime($property, $form);
				case Type::TIME:
					return new FormElementTime($property, $form);
				case Type::STRING:
					$length = $property->getLength();
					// for strings without a known length or with a length larger than 255
					// create a textarea
					if ($length == 0 || $length > 255) {
						return new FormElementTextArea($property, $form);
					}
				default:
					return new FormElementText($property, $form);
			}
		}

		/**
		 * 
		 * @param DoctrineAdmin\AssociationProperty $property
		 * @param Form $form
		 * @return FormElementAssociation
		 */
		protected function getAssociationFormElement(DoctrineAdmin\AssociationProperty $property, Form $form) {
			if ($property instanceof DoctrineAdmin\CollectionAssociationProperty) {
				return new FormElementCollectionAssociation($property, $form);
			} elseif($property instanceof DoctrineAdmin\AssociationProperty) {
				return new FormElementAssociation($property, $form);
			}
		}
		
		/**
		 * 
		 * @return DoctrineAdmin\Entity
		 */
		final public function getEntity() {
			return $this->entity;
		}

		
	}
}