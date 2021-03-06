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
	use Bast1aan\DoctrineAdmin\ScalarProperty;
	use Bast1aan\DoctrineAdmin\Property;
	class FormElementScalar implements FormElement {
		
		/**
		 *
		 * @var Form
		 */
		protected $form;
		
		/**
		 *
		 * @var ScalarProperty
		 */
		protected $property;
		
		public function __construct(ScalarProperty $property, Form $form) {
			$this->form = $form;
			$this->property = $property;
		}
		
		/**
		 * @return Form
		 */
		protected function getForm() {
			return $this->form;
		}
		
		/**
		 * 
		 * @return ScalarProperty
		 */
		public function getProperty() {
			return $this->property;
		}
		
		public function setProperty(Property $property) {
			$this->property = $property;
		}
		
		protected function executeTemplate() {
			require __DIR__ . '/form_element.phtml';
		}
		
		/**
		 * @return string
		 */
		public function render() {
			ob_start();
			$this->executeTemplate();
			return ob_get_clean();
		}
		
		public function __toString() {
			return $this->render();
		}
		
		/**
		 * @return View
		 */
		public function getView() {
			return $this->form->getView();
		}

		/**
		 * @return string
		 */
		public function getClass() {
			$class = "doctrine_admin_element_" . $this->property->getType();
			if ($this->getProperty()->isId())
				$class .= ' doctrine_admin_element_id';
			return $class;
		}

		public function isDisabled() {
			$property = $this->getProperty();
			if ($property->isAutoId() || $property->isId() && strlen($property->getValue()) > 0)
				return true;
			else
				return false;
		}
	}
}
